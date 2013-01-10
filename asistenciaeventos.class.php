<?php

class ASISTENCIAEVENTOS
{

	private $sesion;
	private $db;
	private $html;
	private $login;
	private $usuario;

	public  function __construct($sesion,$db,$html)
    {
        $this->sesion=$sesion;
		$this->login=$sesion->issesion();
		$this->usuario=$sesion->usuario();
		$this->db=$db;
		$this->html=$html;
    }

	private function participantes($eventoid='')
	{
		if($this->db->isadmin($this->usuario))$borrar='concat( "<a href=\"javascript:setparam(\'asistenciaeventos\',\'fnt=delete&jovenid=",j.jovenid,"&eventoid='.$eventoid.'\')\">", "<img src=\'img/del.png\' width=\'32\' align=\'center\'>", "</a>" ) as asistencia';
		else $borrar='"<img src=\'img/del.png\' width=\'32\' align=\'center\'>" as asistencia';
		$nombre="concat(j.nombre,' ',j.apellido) as jnombre";

		$sql=$this->db->mkselect("$nombre,$borrar", 'jovenes j', "j.activo=1 and j.jovenid in (select distinct jovenid from asistenciaeventos where eventoid=$eventoid) order by j.nombre,j.apellido");

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$rows";

		}
		else
		{

			$form='';
			list($status,list(list($title)))=$this->db->runSQL("select nombre,nota from eventos where eventoid=$eventoid",2);
			$jovenes=$this->html->db2htmltable2('jovenes',$rows,'',$title);
			if($jovenes!='')
			{
				$div='pruebajax';
				return "$div||$jovenes";
			}
			else
			{
				$div='pruebajax';
				$title='No hay Participantes';
				return "$div|<h2>$title</h2>";
			}
		}
	}

	private function viewpaginado($req,$eventoid='',$pagina=0)
	{
		if($eventoid=='')list($eventoid,$pagina)=$this->html->datosform('id,pag', $req);
		if($pagina==0) $pagina=1;


		if($this->db->isadmin($this->usuario))$borrar='concat( "<a href=\"javascript:setparam(\'asistenciaeventos\',\'fnt=delete&jovenid=",j.jovenid,"&eventoid='.$eventoid.'&pagina='.$pagina.'\')\">", "<img src=\'img/del.png\' width=\'32\' align=\'center\'>", "</a>" ) as asistencia';
		else $borrar='"<img src=\'img/del.png\' width=\'32\' align=\'center\'>" as asistencia';
		$nombre='concat( "<a href=\"javascript:setfunctionid(\'jovenes\',\'edit\',\'",j.jovenid,"\')\">",j.nombre,\' \',j.apellido , "</a>" ) as editar';
		$sql=$this->db->mkselect("$nombre,$borrar", 'jovenes j', "j.activo=1 and j.jovenid in (select distinct jovenid from asistenciaeventos where eventoid=$eventoid) order by j.nombre,j.apellido");

		list($status,list(list($title)))=$this->db->runSQL("select nombre,nota from eventos where eventoid=$eventoid",2);
		$jovenes=$this->db->paginaciondb2htmltable('jovenes',$sql,'',$title,'asistenciaeventos',$pagina,$eventoid);
		$div='pruebajax';
		return "$div||$jovenes";
	}

	private function borrar($req)
	{
		list($jovenid,$eventoid,$pagina)=$this->html->datosform('jovenid,eventoid,pagina', $req);
		$existeid=$this->db->existeID('asistenciaeventoid',"jovenid=$jovenid and eventoid=$eventoid", 'asistenciaeventos');
		$sql="delete from asistenciaeventos where asistenciaeventoid=$existeid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return viewpaginado($req,$eventoid,$pagina);
	}

	private function agregar($req)
	{
		list($zonaid,$comunidadid,$catequistaid,$grupoid,$nivelid,$eventoid,$nombre,$origenid)=$this->html->datosform('zonaid,comunidadid,catequistaid,grupoid,nivelid,eventoid,nombre,origenid', $req);
		if($origenid=='')
		{
			$zonaid=$this->db->combosql('zonaid', 'select zonaid,nombre from zonas where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('asistenciaeventos',this.form,'add','','zonaid');\"",$addlist="Todos|0");
			$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('asistenciaeventos',this.form,'add','','comunidadid');\"",$addlist="Todos|0");
			$catequistaid=$this->db->combosql('catequistaid', 'select catequistaid,concat(nombre," ",apellido) as nombre from catequistas where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");
			$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by grupoid',$id=0,$js="onchange=\"javascript:post('asistenciaeventos',this.form,'add','','grupoid');\"",$addlist="Todos|0");
			$nivelid=$this->db->combosql('nivelid', 'select nivelid,nombre from niveles where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");
			$eventoid=$this->db->combosql('eventoid', "select eventoid,nombre from eventos where activo=1 order by fecha desc");
			$nombre=$this->html->input('nombre','text',50,'',50,'textbox');
			$btn=$this->html->input($name='btnbuscar', $type='button', $size='', $value=$this->html->mayuscula('buscar'), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('asistenciaeventos',this.form,'add','','boton')\"");

			$labels='Zona|Comunidad|Catequista|Grupo|Nivel|Evento|Nombre|';
			$fields="$zonaid|$comunidadid|$catequistaid|$grupoid|$nivelid|$eventoid|$nombre|$btn";
			$names='zonaid|comunidadid|catequistaid|grupoid|nivelid|eventoid|nombre|';
			$infos='||||||Nombre o Apellido|';
			$form=$this->html->form('buscarjoven',$fields,$labels,$names,$infos,'Busqueda de Jovenes para Parcicipar en los Eventos').'<br>'.$this->html->div('resultados', '').'<br>'.$this->html->div('asistentes', '');;

				return "formulario||$form";
		}
		else
		{
			if($origenid=='comunidadid')
			{
				if($comunidadid==0) $comunidadid="";
				else $comunidadid="and comunidadid=$comunidadid";

				$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) as nombre from catequistas where activo=1 $comunidadid order by nombre",$id=0,$js='',$addlist="Todos|0");
				return "catequistaidajax||$catequistaid";
			}
			if($origenid=='zonaid')
			{
				if($zonaid==0) $zonaid="";
				else $zonaid="and zonaid=$zonaid";

				$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) as nombre from catequistas where activo=1 and comunidadid in (select comunidadid from comunidades where activo=1 $zonaid) order by nombre",$id=0,$js='',$addlist="Todos|0");
				$comunidadid=$this->db->combosql('comunidadid', "select comunidadid,nombre from comunidades where activo=1 $zonaid  order by nombre",$id=0,$js="onchange=\"javascript:post('asistenciaeventos',this.form,'add','','comunidadid');\"",$addlist="Todos|0");
				return "catequistaidajax|comunidadidajax||$catequistaid|$comunidadid";
			}
			if($origenid=='grupoid')
			{
				if($grupoid==0) $grupoid='and grupoid in(0,'.$this->db->listaID('grupoid','grupos').')';
				else $grupoid="and grupoid=$grupoid";

				$eventoid=$this->db->combosql('eventoid', "select eventoid,nombre from eventos where activo=1 $grupoid order by fecha desc");
				return "eventoidajax||$eventoid";
			}
			if($origenid=='boton')
			{
				if($grupoid==0) $grupoid='j.grupoid in(0,'.$this->db->listaID('grupoid','grupos').')';
				else $grupoid="j.grupoid=$grupoid";

				if($nivelid==0) $nivelid='';
				else $nivelid="j.nivelid=$nivelid";

				if($comunidadid==0) $comunidadid='';
				else $comunidadid="j.comunidadid=$comunidadid";

				if($catequistaid==0) $catequistaid='';
				else $catequistaid="j.catequistaid=$catequistaid";

				if($nombre=='') $nombre='';
				else $nombre="(j.nombre like '%$nombre%' or j.apellido like '%$nombre%' or concat(j.nombre,' ',j.apellido) like '%$nombre%')";

				$l=array($nombre,$comunidadid,$nivelid,$grupoid,$catequistaid);
				$w=$this->db->makeWhere($l,$opc='');

				$agregar='concat( "<div id=\"agregado_",j.jovenid,"\"><a href=\"javascript:setparam(\'asistenciaeventos\',\'fnt=save&jovenid=",j.jovenid,"&eventoid='.$eventoid.'\')\">", "<img src=\'img/add.png\' width=\'32\' align=\'center\'>", "</a></div>" ) as asistencia';
				$nombre="concat(j.nombre,' ',j.apellido) as jnombre";

				$sql=$this->db->mkselect("$nombre,$agregar", 'jovenes j', "$w and j.activo=1 and j.jovenid not in (select distinct jovenid from asistenciaeventos where eventoid=$eventoid) order by j.nombre,j.apellido");
				$resultados=$this->view($sql,'Resultados de la Busqueda');
				$asistentes=$this->view('','Asistente al Evento',$eventoid);
				return "resultados|asistentes||$resultados|$asistentes";
			}
		}
		return"resultados|statusajax||Esto no debe salir|";
	}

	private function view($sql='',$title='',$eventoid='')
	{
		if($sql=='')
		{
			$borrar='concat( "<a href=\"javascript:setparam(\'asistenciaeventos\',\'fnt=save&jovenid=",j.jovenid,"&eventoid='.$eventoid.'\')\">", "<img src=\'img/del.png\' width=\'32\' align=\'center\'>", "</a>" ) as asistencia';
			$nombre="concat(j.nombre,' ',j.apellido) as jnombre";

			$sql=$this->db->mkselect("$nombre,$borrar", 'jovenes j', "j.activo=1 and j.jovenid in (select distinct jovenid from asistenciaeventos where eventoid=$eventoid) order by j.nombre,j.apellido");
		}

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return $this->html->msgbox($rows,'error');
		else return $jovenes=$this->html->db2htmltable2('jovenes',$rows, '',$title);
	}

	private function grabar($post)
	{
		list($jovenid,$eventoid)=$this->html->datosform('jovenid,eventoid', $post);
		$uc=$this->sesion->get('usuario');
		$icon='';

		$existeid=$this->db->existeID('asistenciaeventoid',"jovenid=$jovenid and eventoid=$eventoid", 'asistenciaeventos');
		if($existeid==0)
		{
			$sql="Insert into asistenciaeventos (eventoid,jovenid,uc) values ($eventoid,$jovenid,'$uc');";
			$icon="<img style=\"display: block; margin-left: auto; margin-right: auto; vertical-align: middle;\" alt=\"Agregado\" src=\"img/ok.png\" width=\"32\" />";
			$div="agregado_$jovenid";
		}
		else if($existeid>0)
		{
			$sql="delete from asistenciaeventos where asistenciaeventoid=$existeid;";
			$icon="<img style=\"display: block; margin-left: auto; margin-right: auto; vertical-align: middle;\" alt=\"Agregado\" src=\"img/del.png\" width=\"32\" />";
			$div='';
		}

		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$msg";

		}
		else
		{
			$asistentes=$this->view('','Asistente al Evento',$eventoid);
			$r="asistentes|";
			if($div<>'') $r.="$div||$asistentes|$icon";
			else $r.="|$asistentes";
			return $r;
		}
	}

	private function pdfevento($eventoid)
	{
		require_once 'reporte.php';
		$pdf = new PDF('P','pt','Letter');
	//	$pdf = new PDF('L','pt','A3');
		$pdf->SetFont('Arial','',11.5);
		$sql="select DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,nombre,nombrecorto,nota from eventos where eventoid=$eventoid";
		list($status,list(list($fecha,$title,$pref,$nota)))=$this->db->runSQL($sql,2);

		$attr = array('titleFontSize'=>18, 'titleText'=>"$title");
		$sql=mkselect("concat(j.nombre,' ',j.apellido) as Nombre,(select nombre from grupos where grupoid=j.grupoid) as Grupo,(select coalesce(concat(nombre,' ',apellido),NULL) from catequistas where catequistaid=j.catequistaid) as Responsable", 'jovenes j', "j.activo=1 and j.jovenid in (select distinct jovenid from asistenciaeventos where eventoid=$eventoid) order by j.nombre,j.apellido");
		$pdf->mysql_report($sql,false,$attr);
		$pdf->Output("asitencia$pref.pdf",'I');
	}

	private function dashboardasistenciaeventos()
	{
	 return;
	}


	public function asistenciaeventomenu($req)
	{
		list($fnt,$id)=$this->html->datosform('fnt,id', $req);
		$this->sesion->set("ultimoacceso",date("Y-n-j H:i:s"));
		if($this->login==true)
		{
			switch ($fnt)
			{
				case 'add':
					return $this->agregar($req);
					break;
				case 'save':
					return $this->grabar($req);
					break;
				case 'delete':
					return $this->borrar($req);
					break;
				case 'view':
					return $this->view();
					break;
				case 'pdf':
					return $this->pdfevento($id);
					break;
				case 'participantes':
					return $this->viewpaginado($req);
					break;
				case 'dashboard':
					return $this->dashboardasistenciaeventos();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index.php')");
	}
}

?>
