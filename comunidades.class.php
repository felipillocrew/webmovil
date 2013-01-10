<?php

class COMUNIDADES
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
		$sql=$this->db->actionselect($this->usuario,'c.comunidadid', 'coalesce(c.nombre,c.nombrecorto) as nombre', 'comunidades c', 'c.activo=1 order by nombre', 'comunidades');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||$rows";
		else
		{
			$comunidades=$this->html->db2htmltable2('comunidades',$rows, '','Lista de Comunidades');
			return "pruebajax|statusajax||$comunidades|$msg";
		}
	}

	private function comunidad($param,$title='Registro de Nueva comunidad')
	{
		list($comunidadid,$nombre,$nombrecorto,$zonaid,$activo,$nota,$boton)=$param;
		$comunidadid=$this->html->input('comunidadid','hidden', 10, $comunidadid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$nombrecorto=$this->html->input('nombrecorto','text', 50, $nombrecorto, 50, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn=  $this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('comunidades',this.form,'save','')\"");
		$labels='|Nombre|Ref|Zona|Activo|Nota|';
		$fields="$comunidadid|$nombre|$nombrecorto|$zonaid|$activo|$nota|$btn";
		$names="comunidadid|nombre|nombrecorto|zonaid|activo|nota|";
		$info="|Es Obliglatorio||||||";
		$form=$this->html->form('comunidadform',$fields,$labels,$names,$infos,$title);
		return $form;

	}

	private function agregar($boton='actualizar')
	{
		$zonaid=$this->db->combosql('zonaid', 'select zonaid,nombre from zonas where activo=1');
		$param=array(0,'','',$zonaid,'checked','',$boton);
		$html=$this->comunidad($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($comunidadid,$nombre,$nombrecorto,$zonaid,$activo,$nota)=$this->html->datosform('comunidadid,nombre,nombrecorto,zonaid,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$uc=$this->usuario;

		$msg='';
		if($nombre=='') return "nombreajax||".$this->html->input('nombre','text', 40, $nombre, 50, 'textbox obligado');

		if($comunidadid==0) $sql="insert into comunidades (nombre,nombrecorto,zonaid,activo,nota,uc) values ('$nombre','$nombrecorto',$zonaid,$activo,'$nota','$uc');";
		else if($comunidadid>0) $sql="update comunidades set nombre='$nombre',nombrecorto='$nombrecorto',zonaid=$zonaid,activo=$activo,nota='$nota',uc='$uc' where comunidadid=$comunidadid;";
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status) return "statusajax||$msg";
		else return "pruebajax|statusajax|||{$this->html->msgbox($msg, 'ok')}";
	}

	private function borrar($comunidadid)
	{
		$sql="delete from comunidades where comunidadid=$comunidadid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';
		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($comunidadid)
	{
		$sql="select nombre,nombrecorto,zonaid,activo,nota from comunidades where comunidadid=$comunidadid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$nombrecorto,$zonaid,$activo,$nota))=$res;
		$activo=$this->html->tochecked($activo);
		$zonaid=$this->db->combosql('zonaid', 'select zonaid,nombre from zonas where activo=1',$id=$zonaid);
		$param=array($comunidadid,$nombre,$nombrecorto,$zonaid,$activo,$nota,'Actualizar');
		$html=$this->comunidad($param,"Actualizando la comunidad: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardcomunidad()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('comunidades','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('comunidades','view')");

		return $this->html->dashdiv('Comunidades',"loaddashboard('comunidades&fnt=dashboard')",$this->html->dashboard($items));
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
		$pdf->Write(5, 'Lista de comunidades');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(80,7,'comunidad',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select nombre from comunidades order by nombre;";
		list($status,$rows)=runSQL($sql);

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
					$pdf->Cell(80,7,$data,1);
				}
				$count++;
				$pdf->Ln();
			}
		}
		$pdf->Output('comunidades.pdf','I');

	}


	public function comunidadmenu($req)
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
				case 'pdf':
					return $this->pdf();
					break;
				case 'dashboard':
					return $this->dashboardcomunidad();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index')");
	}
}

?>
