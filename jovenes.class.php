<?php

class JOVENES
{
	private $sesion;
	private $db;
	private $html;
	private $login;
	private $usuario;

	public function __construct($sesion,$db,$html)
    {
        $this->sesion=$sesion;
		$this->login=$sesion->issesion();
		$this->usuario=$sesion->usuario();
		$this->db=$db;
		$this->html=$html;
    }

	private function view($msg='')
	{
		$fbkicon='<img src="img/fbk.png" alt="" width="16" height="16""/>';
		$fbk="'<a target=\"_blank\" href=\"http://m.facebook.com/', j.facebook, '\">', '$fbkicon', '</a>'";
		$nombre='concat( "<a href=\"javascript:setfunctionid(\'jovenes\',\'edit\',\'",j.jovenid,"\')\">",j.nombre,\' \',j.apellido , "</a>" ) as editar';
		$sql=$this->db->newactionselect($this->usuario,'j.jovenid', "$nombre,(case when strcmp(j.facebook,'') then concat($fbk) else '' end) as jfacebook", 'jovenes j', 'j.activo=1 order by j.nombre', 'jovenes');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||$rows";
		else
		{
			$jovenes=$this->html->db2htmltable2('jovenes',$rows, '','Lista de Jovenes');
			return "pruebajax|statusajax||$jovenes|$msg";
		}
	}

	private function joven($param,$title='Registro de Nuevo Joven')
	{
		list($jovenid,$nombre,$apellido,$cedula,$celular,$correo,$escuela,$facebook,$catequistaid,$grupoid,$nivelid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,$boton)=$param;
		if($jovenid==0)	$imagen=array('imagen'=>'','tipo'=>'joven','id'=>  $this->db->nextID('jovenes'));
		else $imagen=array('imagen'=>"joven_$jovenid",'tipo'=>'joven','id'=>  $jovenid);
		$jovenid=$this->html->input('jovenid','hidden', 10, $jovenid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$apellido=$this->html->input('apellido','text', 50, $apellido, 50, 'textbox');
		$cedula=$this->html->input('cedula','text', 15, $cedula, 15, 'textbox');
		$celular=$this->html->input('celular','tel', 10, $celular, 10, 'textbox');
		$correo=$this->html->input('correo','email', 50, $correo, 50, 'textbox');
		$escuela=$this->html->input('escuela','text', 50, $escuela, 50, 'textbox');
		$facebook=$this->html->input('facebook','text', 30, $facebook, 30, 'textbox');
		$calle=$this->html->input('calle','text', 30, $calle, 30, 'textbox');
		$casa=$this->html->input('casa','text', 10, $casa, 10, 'textbox');
		$telefono=$this->html->input('telefono','tel', 10, $telefono, 10, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		if($this->db->isadmin($this->usuario)) $js="onclick=\"javascript:post('jovenes',this.form,'save','')\"";
		else $js="onclick=\"javascript:alert('No puedes modificar este registro')\"";
		$btn=$this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js);
		$labels='|Nombre|Apellido|Cedula|Celular|Correo|Escuela|Facebook|Catequista|Grupo|Nivel|Comunidad|Calle|Casa|Telefono|Activo|Nota|';
		$fields="$jovenid|$nombre|$apellido|$cedula|$celular|$correo|$escuela|$facebook|$catequistaid|$grupoid|$nivelid|$comunidadid|$calle|$casa|$telefono|$activo|$nota|$btn";
		$names="jovenid|nombre|apellido|cedula|celular|correo|escuela|facebook|catequistaid|grupoid|nivelid|comunidadid|calle|casa|telefono|activo|nota|";
		$infos="|Es Obligatorio||||||||||||||||";
	
		$form=$this->html->form('jovenform',$fields,$labels,$names,$infos,$title,$imagen);
		return $form;

	}

	private function agregar($boton='Actualizar')
	{
		$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by nombre',$id=0,$js='',$addlist='Sin Grupo|0');
		$nivelid=$this->db->combosql('nivelid', 'select nivelid,nombre from niveles where activo=1 order by nombre',$id=0,$js='',$addlist='Sin Nivel|0');
		$catequistaid=$this->db->combosql('catequistaid', "select c.catequistaid,concat(c.nombre,' ',c.apellido) as nombre from catequistas c where c.activo=1 order by nombre",0,'','Sin Catequista|0');
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre');
		$param=array(0,'','','','','','','',$catequistaid,$grupoid,$nivelid,$comunidadid,'','','','checked','',$boton);
		$html=$this->joven($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($jovenid,$nombre,$apellido,$cedula,$celular,$correo,$escuela,$facebook,$catequistaid,$grupoid,$nivelid,$comunidadid,$calle,$casa,$telefono,$activo,$nota)=$this->html->datosform('jovenid,nombre,apellido,cedula,celular,correo,escuela,facebook,catequistaid,grupoid,nivelid,comunidadid,calle,casa,telefono,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$apellido=$this->html->mayuscula($apellido);
		$uc=$this->sesion->get('usuario');

		$msg='';
		if($nombre=='') return "nombreajax||".$this->html->input('nombre','text', 30, $nombre, 30, 'textbox obligado');
		if($apellido=='') return "apellidoajax||".$this->html->input('apellido','text', 30, $apellido, 30, 'textbox obligado');
		
		if($jovenid==0) $sql="Insert into jovenes (nombre,apellido,cedula,celular,correo,escuela,facebook,catequistaid,grupoid,nivelid,comunidadid,calle,casa,telefono,activo,nota,uc) values ('$nombre','$apellido','$cedula','$celular','$correo','$escuela','$facebook',$catequistaid,$grupoid,$nivelid,$comunidadid,'$calle','$casa','$telefono',$activo,'$nota','$uc');";
		else if($jovenid>0) $sql="Update jovenes set nombre='$nombre',apellido='$apellido',cedula='$cedula',celular='$celular',correo='$correo',escuela='$escuela',facebook='$facebook',catequistaid=$catequistaid,grupoid=$grupoid,nivelid=$nivelid,comunidadid=$comunidadid,calle='$calle',casa='$casa',telefono='$telefono',activo=$activo,nota='$nota',uc='$uc' where jovenid=$jovenid;";
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status) return "sqlerror||$msg";
		else return "pruebajax|statusajax|||".$this->html->msgbox($msg, 'ok');
	}

	private function borrar($jovenid)
	{
		$sql="delete from jovenes where jovenid=$jovenid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';
		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($jovenid)
	{
		$sql="select nombre,apellido,cedula,celular,correo,escuela,facebook,catequistaid,grupoid,nivelid,comunidadid,calle,casa,telefono,activo,nota from jovenes where jovenid=$jovenid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$apellido,$cedula,$celular,$correo,$escuela,$facebook,$catequistaid,$grupoid,$nivelid,$comunidadid,$calle,$casa,$telefono,$activo,$nota))=$res;

		$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by nombre',$id=$grupoid,$js='',$addlist='Sin Grupo|0');
		$nivelid=$this->db->combosql('nivelid', 'select nivelid,nombre from niveles where activo=1 order by nombre',$id=$nivelid,$js='',$addlist='Sin Nivel|0');
		$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) from catequistas where activo=1 order by nombre",$id=$catequistaid,$js='',$addlist='Sin Catequista|0');
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=$comunidadid);

		$activo=$this->html->tochecked($activo);
		$param=array($jovenid,$nombre,$apellido,$cedula,$celular,$correo,$escuela,$facebook,$catequistaid,$grupoid,$nivelid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,'Actualizar');
		$html=$this->joven($param,"Actualizando al Joven: $nombre $apellido");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardjoven()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('jovenes','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('jovenes','view')");
		$items.=$this->html->dashboarditem('Busqueda','find',"newgotofunction('jovenes','find')");

		return $this->html->dashdiv('Jovenes',"loaddashboard('jovenes&fnt=dashboard')",$this->html->dashboard($items));
	}


	private function pdf()
	{
		include ('fpdf.php');
		$pdf = new FPDF('P','mm','Letter');
		$pdf->AddPage();

		$pdf->SetFont('Helvetica','',14);
		$pdf->SetFontSize(18);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Write(5, 'Lista de jovenes');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(50,7,'Fecha',1);
		$pdf->Cell(80,7,'joven',1);
		$pdf->Cell(50,7,'Referencia',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select DATE_FORMAT(fecha, '%d/%m/%Y') as dia,nombre,nombrecorto from jovenes order by fecha;";
		list($status,$rows)=$this->db->runSQL($sql);

		if(!$status)
		{
			$div='sqlerror';
			echo "$div||$rows";

		}
		else
		{
			$count=1;
			foreach ($rows as $key => $value)
			{
				$pdf->Cell(10,7,$count,1);
				foreach ($value as $id => $data)
				{
					if($id=='nombre') $pdf->Cell(80,7,$data,1);
					else $pdf->Cell(50,7,$data,1);
				}
				$count++;
				$pdf->Ln();
			}
		}
		$pdf->Output('jovenes.pdf','I');

	}



	private function buscar($req)
	{
		list($zonaid,$comunidadid,$capillaid,$catequistaid,$grupoid,$nivelid,$nombre,$origenid)=$this->html->datosform('zonaid,comunidadid,capillaid,catequistaid,grupoid,nivelid,nombre,origenid', $req);

		if ($origenid=='')
		{
				$zonaid=$this->db->combosql('zonaid', 'select zonaid,nombre from zonas where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','zonaid');\"",$addlist="Todos|0");
				$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
				$capillaid=$this->db->combosql('capillaid', 'select capillaid,nombre from capillas where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','capillaid');\"",$addlist="Todos|0");
				$catequistaid=$this->db->combosql('catequistaid', 'select catequistaid,concat(nombre," ",apellido) as nombre from catequistas where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");
				$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");
				$nivelid=$this->db->combosql('nivelid', 'select nivelid,nombre from niveles where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");

				$nombre=$this->html->input('nombre','search',50,'',50,'search');
				$btn=$this->html->input($name='btnbuscar', $type='button', $size='', $value=$this->html->mayuscula('buscar'), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('jovenes',this.form,'find','','boton')\"");
				$labels='Zona|Comunidad|Capilla|Grupo|Catequista|Nivel|Nombre|';
				$fields="$zonaid|$comunidadid|$capillaid|$grupoid|$catequistaid|$nivelid|$nombre|$btn";
				$names="zonaid|comunidadid|capillaid|grupoid|catequistaid|nivelid|nombre|";
				$infos="||||||Nombre o Apellido|";
				$form=$this->html->form('buscarjoven',$fields,$labels,$names,$infos,'Busqueda de Jovenes').$this->html->div('resultados', '');
				return "statusajax|formulario|pruebajax|||$form|";
		}
		else
		{
			$zonaid=$this->db->numerico($zonaid);
			$comunidadid=$this->db->numerico($comunidadid);
			$capillaid=$this->db->numerico($capillaid);
			$catequistaid=$this->db->numerico($catequistaid);
			$grupoid=$this->db->numerico($grupoid);
			$nivelid=$this->db->numerico($nivelid);
			$nombre=$this->db->comillas($nombre);
				if($origenid=='capillaid')
				{
					if($capillaid==0) $capillaid="";
					else $capillaid="and capillaid=$capillaid";

					$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) as nombre from catequistas where activo=1 $capillaid order by nombre",$id=0,$js='',$addlist="Todos|0");
					$comunidadid=$this->db->combosql('comunidadid', "select comunidadid,nombre from comunidades where activo=1 and comunidadid in (select comunidadid from capillas where activo=1 $capillaid) order by nombre",$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
					return "catequistaidajax|comunidadidajax||$catequistaid|$comunidadid";
				}
				if($origenid=='comunidadid')
				{
					if($comunidadid==0) $comunidadid="";
					else $comunidadid="and comunidadid=$comunidadid";

					$capillaid=$this->db->combosql('capillaid', "select capillaid,nombre from capillas where activo=1 $comunidadid order by nombre",$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','capillaid');\"",$addlist="Todos|0");
					$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) as nombre from catequistas where activo=1 $comunidadid order by nombre",$id=0,$js='',$addlist="Todos|0");
					return "catequistaidajax|capillaidajax||$catequistaid|$capillaid";
				}
				if($origenid=='zonaid')
				{
					if($zonaid==0) $zonaid="";
					else $zonaid="and zonaid=$zonaid";

					$capillaid=$this->db->combosql('capillaid', "select capillaid,nombre from capillas where activo=1 and comunidadid in (select comunidadid from comunidades where activo=1 $zonaid) order by nombre",$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','capillaid');\"",$addlist="Todos|0");
					$catequistaid=$this->db->combosql('catequistaid', "select catequistaid,concat(nombre,' ',apellido) as nombre from catequistas where activo=1 and comunidadid in (select comunidadid from comunidades where activo=1 $zonaid) order by nombre",$id=0,$js='',$addlist="Todos|0");
					$comunidadid=$this->db->combosql('comunidadid', "select comunidadid,nombre from comunidades where activo=1 $zonaid  order by nombre",$id=0,$js="onchange=\"javascript:post('jovenes',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
					return "catequistaidajax|capillaidajax|comunidadidajax||$catequistaid|$capillaid|$comunidadid";
				}
				if($origenid=='boton')
				{
						if($grupoid==0) $grupoid='j.grupoid in(0,'.$this->db->listaID('grupoid','grupos').')';
						else $grupoid="j.grupoid=$grupoid";

						if($nivelid==0) $nivelid='';
						else $nivelid="j.nivelid=$nivelid";

						if($comunidadid==0)
						{
							if($zonaid==0) $zonaid="";
							else $zonaid="and zonaid=$zonaid";
							$comunidadid="j.comunidadid in (select comunidadid from comunidades where activo=1 $zonaid )";
						}
						else $comunidadid="j.comunidadid=$comunidadid";

						if($catequistaid==0)
						{
								if($capillaid==0) $catequistaid='j.catequistaid in (0,'.  $this->db->listaID('catequistaid', 'catequistas', 'activo=1').')';
								else $catequistaid="j.catequistaid in (select catequistaid from catequistas where activo=1 and capillaid=$capillaid)";
						}
						else $catequistaid="j.catequistaid=$catequistaid";

						if($nombre=='NULL') $nombre='';
						else $nombre="(j.nombre like '%$nombre%' or j.apellido like '%$nombre%' or concat(j.nombre,' ',j.apellido) like '%$nombre%')";

						$l=array($nombre,$comunidadid,$nivelid,$grupoid,$catequistaid);
						$w=$this->db->makeWhere($l,'and');

						$nombre='concat( "<a href=\"javascript:setfunctionid(\'jovenes\',\'edit\',\'",j.jovenid,"\')\">",j.nombre,\' \',j.apellido , "</a>" ) as editar';
						$sql=$this->db->newactionselect($this->usuario,'j.jovenid', "$nombre,(case when j.celular='' then j.telefono else j.celular end) as jcelular", 'jovenes j', "j.activo=1 $w order by j.nombre,j.apellido", 'jovenes');
						list($status,$rows)=$this->db->runSQL($sql);
						if(!$status) return"statusajax||$sql::$rows";
						if (empty($rows)) $jovens='<h2>NO HAY DATOS</h2>';
						else $jovens=$this->html->db2htmltable2('joven',$rows, '','Lista de Jovenes');

						return"resultados|statusajax||$jovens|";
				}
				return"resultados|statusajax||Esto no debe salir|";
		}

	}

	public  function jovenmenu($req)
	{
		$this->html->sethtmllog(print_r($this->html->datosform('fnt,id', $req),true));
		list($fnt,$id)=$this->html->datosform('fnt,id', $req);
		$this->sesion->set("ultimoacceso",date("Y-n-j H:i:s"));
		if($this->login==true)
		{
			switch ($fnt)
			{
				case 'add':
					return $this->agregar('Guardar');
					break;
				case 'save':
					return $this->grabar($req);
					break;
				case 'edit':
					return $this->editar($id);
					break;
				case 'delete':
					return $this->borrar($id);
					break;
				case 'view':
					return $this->view();
					break;
				case 'find':
					return $this->buscar($req);
					break;
				case 'pdf':
					return $this->pdf();
					break;
				case 'dashboard':
					return $this->dashboardjoven();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index.php')");
	}
}

?>
