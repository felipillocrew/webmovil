<?php

class GRUPOS
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
		$sql=$this->db->actionselect($this->usuario,'grupoid', "nombre", 'grupos', 'activo=1 order by nombre', 'grupos');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||$rows";
		else
		{
			$grupos=$this->html->db2htmltable2('grupos',$rows, '','Lista de Grupos');
			return "pruebajax|statusajax||$grupos|$msg";
		}
	}

	private function grupo($param,$title='Registro de Nuevo Grupo')
	{
		list($grupoid,$nombre,$activo,$nota,$boton)=$param;
		$grupoid=$this->html->input('grupoid','hidden', 10, $grupoid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn=  $this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('grupos',this.form,'save','')\"");

		$labels='|Nombre|Activo|Nota|';
		$fields="$grupoid|$nombre|$activo|$nota|$btn";
		$names="grupoid|nombre|activo|nota|";
		$info="|Es Obligatorio|||";
		$form=$this->html->form('grupoform',$fields,$labels,$names,$infos,$title);
		return $form;

	}

	private function agregar($boton='actualizar')
	{
		$param=array(0,'','checked','',$boton);
		$html=$this->grupo($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($grupoid,$nombre,$activo,$nota)=$this->html->datosform('grupoid,nombre,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$uc=$this->usuario;

		$msg='';
		if($nombre=='')
		{
				$msg=$this->html->input('nombre','text', 40, $nombre, 50, 'textbox obligado');
				return "nombreajax||$msg";
		}

		if($grupoid==0) $sql="Insert into grupos (nombre,activo,nota,uc) values ('$nombre',$activo,'$nota','$uc');";
		else if($grupoid>0) $sql="Update grupos set nombre='$nombre',activo=$activo,nota='$nota',uc='$uc' where grupoid=$grupoid;";

		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status)return "sqlerror||$msg";
		else return "pruebajax|statusajax|||".  $this->html->msgbox($msg, 'ok');
	}

	private function borrar($grupoid)
	{
		$sql="delete from grupos where grupoid=$grupoid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($grupoid)
	{
		$sql="select nombre,activo,nota from grupos where grupoid=$grupoid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$activo,$nota))=$res;
		$activo=$this->html->tochecked($activo);
		$param=array($grupoid,$nombre,$activo,$nota,'Actualizar');
		$html=$this->grupo($param,"Actualizando el grupo: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	function dashboardgrupo()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('grupos','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('grupos','view')");

		return $this->html->dashdiv('Grupos',"loaddashboard('grupos?fnt=dashboard')",$this->html->dashboard($items));
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
		$pdf->Write(5, 'Lista de grupos');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(80,7,'Grupo',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select nombre from grupos order by fecha;";
		list($status,$rows)=$this->db->runSQL($sql);

		if(!$status) echo "sqlerror||$rows";
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
		$pdf->Output('grupos.pdf','I');

	}

	public  function grupomenu($req)
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
					return $this->dashboardgrupo();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index')");
	}
}

?>
