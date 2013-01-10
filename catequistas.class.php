<?php

class CATEQUISTAS
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
		$fbkicon='<img src="/img/fbk.png" alt="" width="16" height="16""/>';
		$fbk="'<a target=\"_blank\" href=\"http://m.facebook.com/', c.facebook, '\">', '$fbkicon', '</a>'";
		$sql=$this->db->actionselect($this->usuario,'c.catequistaid', "concat(c.nombre,' ',c.apellido) as nombre,(case when strcmp(c.facebook,'') then concat($fbk) else '' end) as facebook", 'catequistas c', "c.activo=1 order by nombre", 'catequistas');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||$rows";
		else return "pruebajax|statusajax||{$this->html->db2htmltable2('catequistas',$rows, '','Lista de catequistas')}|$msg";

	}

	private function catequista($param,$title='Registro de Nuevo Catequista')
	{
		list($catequistaid,$nombre,$apellido,$cedula,$celular,$correo,$facebook,$rolid,$capillaid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,$user,$boton)=$param;
		if($catequistaid==0)	$imagen=array('imagen'=>'','tipo'=>'catequista','id'=>  $this->db->nextID('catequistas'));
		else $imagen=array('imagen'=>"catequista_$catequistaid",'tipo'=>'catequista','id'=>  $catequistaid);
		$catequistaid=$this->html->input('catequistaid','hidden', 10, $catequistaid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$apellido=$this->html->input('apellido','text', 50, $apellido, 50, 'textbox');
		$cedula=$this->html->input('cedula','text', 15, $cedula, 15, 'textbox');
		$celular=$this->html->input('celular','tel', 10, $celular, 10, 'textbox');
		$correo=$this->html->input('correo','email', 50, $correo, 50, 'textbox');
		$facebook=$this->html->input('facebook','text', 30, $facebook, 30, 'textbox');
		$calle=$this->html->input('calle','text', 30, $calle, 30, 'textbox');
		$casa=$this->html->input('casa','text', 10, $casa, 10, 'textbox');
		$telefono=$this->html->input('telefono','tel', 10, $telefono, 10, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn=$this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('catequistas',this.form,'save','')\"");

		$labels='|Nombre|Apellido|Cedula|Celular|Correo|Facebook|Rol|Capilla|Comunidad|Calle|Casa|Telefono|Activo|Nota|Usuario|';
		$names="|nombre|apellido|cedula|celular|correo|facebook|rolid|capillaid|comunidadid|calle|casa|telefono|activo|nota|user|";
		$fields="$catequistaid|$nombre|$apellido|$cedula|$celular|$correo|$facebook|$rolid|$capillaid|$comunidadid|$calle|$casa|$telefono|$activo|$nota|$user|$btn";
		$infos="|es obligatorio||||||||||||||usuario del sistema|";
		$form=$this->html->form('catequistaform',$fields,$labels,$names,$infos,$title,$imagen);
		return $form;

	}

	private function agregar($boton='Actualizar')
	{
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=0,$js='',$addlist="Selecione una Comunidad|0");
		$capillaid=$this->db->combosql('capillaid', 'select capillaid,nombre from capillas where activo=1 order by nombre',$id=0,$js='',$addlist="Selecione una Capilla|0");
		$rolid=$this->db->combosql('rolid', 'select rolid,nombre from roles where activo=1 order by nombre',$id=0,$js='',$addlist="Selecione un Rol|0");
		$user=$this->db->combosql('user', 'select id,concat(nombre," ",apellido) from usuarios where activo=1 order by nombre',$id=0,$js='',$addlist="No Usuario|0");
		$param=array(0,'','','','','','',$rolid,$capillaid,$comunidadid,'','','','checked','',$user,$boton);
		$html=$this->catequista($param);
		return "formulario||$html";
	}

	private function grabar($post)
	{
		list($catequistaid,$nombre,$apellido,$cedula,$celular,$correo,$facebook,$rolid,$capillaid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,$user)=$this->html->datosform('catequistaid,nombre,apellido,cedula,celular,correo,facebook,rolid,capillaid,comunidadid,calle,casa,telefono,activo,nota,user', $post);
		$nombre=$this->html->mayuscula($nombre);
		$apellido=$this->html->mayuscula($apellido);
		$uc=$this->usuario;

		$msg='';
		if($nombre=='') return "nombreajax||".$this->html->input('nombre','text', 30, $nombre, 30, 'textbox obligado');
		if($apellido=='') return "apellidoajax||".$this->html->input('apellido','text', 30, $apellido, 30, 'textbox obligado');

		if($catequistaid==0) $sql="Insert into catequistas (nombre,apellido,cedula,celular,correo,facebook,rolid,capillaid,comunidadid,calle,casa,telefono,activo,nota,user,uc) values ('$nombre','$apellido','$cedula','$celular','$correo','$facebook',$rolid,$capillaid,$comunidadid,'$calle','$casa','$telefono',$activo,'$nota',$user,'$uc');";
		else if($catequistaid>0) $sql="Update catequistas set nombre='$nombre',apellido='$apellido',cedula='$cedula',celular='$celular',correo='$correo',facebook='$facebook',rolid=$rolid,capillaid=$capillaid,comunidadid=$comunidadid,calle='$calle',casa='$casa',telefono='$telefono',activo=$activo,nota='$nota',user=$user,uc='$uc' where catequistaid=$catequistaid;";
		list($status,$msg)=$this->db->exeSQL($sql);

		if(!$status) return "sqlerror||$msg";
		else return "pruebajax|statusajax|||". $this->html->msgbox($msg, 'ok');
	}

	private function borrar($catequistaid)
	{
		$sql="delete from catequistas where catequistaid=$catequistaid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';
		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($catequistaid)
	{
		$sql="select nombre,apellido,cedula,celular,correo,facebook,rolid,capillaid,comunidadid,calle,casa,telefono,activo,nota,user from catequistas where catequistaid=$catequistaid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$apellido,$cedula,$celular,$correo,$facebook,$rolid,$capillaid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,$user))=$res;

		$rolid=$this->db->combosql('rolid', 'select rolid,nombre from roles where activo=1 order by nombre',$id=$rolid);
		$capillaid=$this->db->combosql('capillaid', 'select capillaid,nombre from capillas where activo=1 order by nombre',$id=$capillaid);
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=$comunidadid);
		$user=$this->db->combosql('user', 'select id,concat(nombre," ",apellido) from usuarios where activo=1 order by nombre',$id=$user,$js='',$addlist="No Usuario|0");
		$activo=$this->html->tochecked($activo);

		$param=array($catequistaid,$nombre,$apellido,$cedula,$celular,$correo,$facebook,$rolid,$capillaid,$comunidadid,$calle,$casa,$telefono,$activo,$nota,$user,'Actualizar');
		$html=$this->catequista($param,"Actualizando al Catequista: $nombre $apellido");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardcatequista()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('catequistas','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('catequistas','view')");
		$items.=$this->html->dashboarditem('Busqueda','find',"newgotofunction('catequistas','find')");

		return $this->html->dashdiv('Catequistas',"loaddashboard('catequistas?fnt=dashboard')",$this->html->dashboard($items));
	}


	private function pdf()
	{
		include ('fpdf');
		$pdf = new FPDF('P','mm','Letter');
		$pdf->AddPage();

		$pdf->SetFont('Helvetica','',14);
		$pdf->SetFontSize(18);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Write(5, 'Lista de catequistas');
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

		$sql="select DATE_FORMAT(fecha, '%d/%m/%Y') as dia,nombre,nombrecorto from catequistas order by fecha;";
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
		$pdf->Output('catequistas.pdf','I');

	}



	private function buscar($req)
	{
		list($zonaid,$comunidadid,$capillaid,$rolid,$nombre,$origenid)=$this->html->datosform('zonaid,comunidadid,capillaid,rolid,nombre,origenid', $req);

		if ($origenid=='')
		{
				$zonaid=$this->db->combosql('zonaid', 'select zonaid,nombre from zonas where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','zonaid');\"",$addlist="Todos|0");
				$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
				$capillaid=$this->db->combosql('capillaid', 'select capillaid,nombre from capillas where activo=1 order by nombre',$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','capillaid');\"",$addlist="Todos|0");
				$rolid=$this->db->combosql('rolid', 'select rolid,nombre from roles where activo=1 order by nombre',$id=0,$js='',$addlist="Todos|0");

				$nombre=$this->html->input('nombre','text',50,'',50,'textbox');
				$btn=$this->html->input($name='btnbuscar', $type='button', $size='', $value=$this->html->mayuscula('buscar'), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('catequistas',this.form,'find','','boton')\"");
				$labels='Zona|Comunidad|Capilla|Rol|Nombre|';
				$fields="$zonaid|$comunidadid|$capillaid|$rolid|$nombre|$btn";
				$names='zonaid|comunidadid|capillaid|rolid|nombre|';
				$infos='|||||';
				$form=$this->html->form('buscarjoven',$fields,$labels,$names,$infos,'Busqueda de catequistas').$this->html->div('resultados', '');
				return "statusajax|formulario|pruebajax|||$form|";
		}
		else
		{
				$zonaid=$this->db->numerico($zonaid);
				$comunidadid=$this->db->numerico($comunidadid);
				$capillaid=$this->db->numerico($capillaid);
				$rolid=$this->db->numerico($rolid);
				$nombre=$this->db->comillas($nombre);

				if($origenid=='capillaid')
				{
					if($capillaid==0) $capillaid="";
					else $capillaid="and capillaid=$capillaid";

					$comunidadid=$this->db->combosql('comunidadid', "select comunidadid,nombre from comunidades where activo=1 and comunidadid in (select comunidadid from capillas where activo=1 $capillaid) order by nombre",$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
					return "comunidadidajax||$comunidadid";
				}
				if($origenid=='comunidadid')
				{
					if($comunidadid==0) $comunidadid="";
					else $comunidadid="and comunidadid=$comunidadid";

					$capillaid=$this->db->combosql('capillaid', "select capillaid,nombre from capillas where activo=1 $comunidadid order by nombre",$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','capillaid');\"",$addlist="Todos|0");
					return "capillaidajax||$capillaid";
				}
				if($origenid=='zonaid')
				{
					if($zonaid==0) $zonaid="";
					else $zonaid="and zonaid=$zonaid";

					$capillaid=$this->db->combosql('capillaid', "select capillaid,nombre from capillas where activo=1 and comunidadid in (select comunidadid from comunidades where activo=1 $zonaid) order by nombre",$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','capillaid');\"",$addlist="Todos|0");
					$comunidadid=$this->db->combosql('comunidadid', "select comunidadid,nombre from comunidades where activo=1 $zonaid  order by nombre",$id=0,$js="onchange=\"javascript:post('catequistas',this.form,'find','','comunidadid');\"",$addlist="Todos|0");
					return "capillaidajax|comunidadidajax||$capillaid|$comunidadid";
				}
				if($origenid=='boton')
				{
						if($rolid==0) $rolid='';
						else $rolid="c.rolid=$rolid";

						if($comunidadid==0)
						{
							if($zonaid==0) $zonaid="";
							else $zonaid="and zonaid=$zonaid";
							$comunidadid="c.comunidadid in (select comunidadid from comunidades where activo=1 $zonaid )";
						}
						else $comunidadid="c.comunidadid=$comunidadid";

						if($capillaid==0)$capillaid="";
						else $capillaid="c.capillaid=$capillaid";

						if($nombre=='NULL') $nombre='';
						else $nombre="(c.nombre like '%$nombre%' or c.apellido like '%$nombre%' or concat(c.nombre,' ',c.apellido) like '%$nombre%')";

						$l=array($nombre,$comunidadid,$rolid,$capillaid);
						$w=$this->db->makeWhere($l,'and');
						$editar='concat( "<a href=\"javascript:setfunctionid(\'catequistas\',\'edit\',\'",c.catequistaid,"\')\">",c.nombre,\' \',c.apellido , "</a>" ) as editar';
						$sql=$this->db->newactionselect($this->usuario,'c.catequistaid', "$editar", 'catequistas c', " c.activo=1 $w order by nombre", 'catequistas');
						list($status,$rows)=$this->db->runSQL($sql);
						if (empty($rows)) $catequistas='<h2>NO HAY DATOS</h2>'.$sql;
						else $catequistas=$this->html->db2htmltable2('catequista',$rows, ',Nombre','Lista de Catequistas');
						return"resultados|statusajax||$catequistas|";

				}
				return"resultados|statusajax||Esto no debe salir|";
		}

	}
	
	public  function catequistamenu($req)
	{
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
					return $this->dashboardcatequista();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index')");
	}
}

?>
