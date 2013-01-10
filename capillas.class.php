<?php
class CAPILLAS
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
		$sql=$this->db->actionselect($this->usuario,'c.capillaid', 'coalesce(c.nombre,c.nombrecorto) as nombre', 'capillas c', 'c.activo=1 order by nombre', 'capillas');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||".$this->html->msgbox($rows, 'ok');
		else
		{
			return "pruebajax|statusajax||{$this->html->db2htmltable2('capillas',$rows, '','Lista de capillas')}|$msg";
		}
	}

	private function capilla($param,$title='Registro de Nueva Capilla')
	{
		list($capillaid,$nombre,$nombrecorto,$parroquiaid,$comunidadid,$activo,$nota,$boton)=$param;
		$capillaid=$this->html->input('capillaid','hidden', 10, $capillaid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$nombrecorto=$this->html->input('nombrecorto','text', 50, $nombrecorto, 50, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn=$this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('capillas',this.form,'save','')\"");
		$labels='|Nombre|Ref|Parroquia|Comunidad|Activo|Nota|';
		$fields="$capillaid|$nombre|$nombrecorto|$parroquiaid|$comunidadid|$activo|$nota|$btn";
		$names="capillaid|nombre|nombrecorto|parroquiaid|comunidadid|activo|nota|";
		$infos="|Es Obligatorio|||||||";
		$form=$this->html->form('capillaform',$fields,$labels,$names,$infos,$title);
		return $form;

	}

	private function agregar($boton='actualizar')
	{
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1');
		$parroquiaid=$this->db->combosql('parroquiaid', 'select parroquiaid,nombre from parroquias where activo=1');
		$param=array(0,'','',$parroquiaid,$comunidadid,'checked','',$boton);
		$html=$this->capilla($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($capillaid,$nombre,$nombrecorto,$parroquiaid,$comunidadid,$activo,$nota)=$this->html->datosform('capillaid,nombre,nombrecorto,parroquiaid,comunidadid,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$uc=$this->sesion->get('usuario');

		$msg='';
		if($nombre=='')
		{
				$msg=$this->html->input('nombre','text', 40, $nombre, 50, 'textbox obligado');
				return "nombreajax||$msg";
		}

		if($capillaid==0) $sql="insert into capillas (nombre,nombrecorto,parroquiaid,comunidadid,activo,nota,uc) values ('$nombre','$nombrecorto',$parroquiaid,$comunidadid,$activo,'$nota','$uc');";
		else if($capillaid>0) $sql="update capillas set nombre='$nombre',nombrecorto='$nombrecorto',parroquiaid=$parroquiaid,comunidadid=$comunidadid,activo=$activo,nota='$nota',uc='$uc' where capillaid=$capillaid;";
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$msg";

		}
		else return "pruebajax|statusajax|||".  $this->html->msgbox($msg, 'ok');
	}

	private function borrar($capillaid)
	{
		$sql="delete from capillas where capillaid=$capillaid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($capillaid)
	{
		$sql="select nombre,nombrecorto,parroquiaid,comunidadid,activo,nota from capillas where capillaid=$capillaid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$nombrecorto,$parroquiaid,$comunidadid,$activo,$nota))=$res;
		$activo=$this->html->tochecked($activo);
		$comunidadid=$this->db->combosql('comunidadid', 'select comunidadid,nombre from comunidades where activo=1',$id=$comunidadid);
		$parroquiaid=$this->db->combosql('parroquiaid', 'select parroquiaid,nombre from parroquias where activo=1',$id=$parroquiaid);
		$param=array($capillaid,$nombre,$nombrecorto,$parroquiaid,$comunidadid,$activo,$nota,'Actualizar');
		$html=$this->capilla($param,"Actualizando la Capilla: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardcapilla()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('capillas','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('capillas','view')");

		return $this->html->dashdiv('Capillas',"loaddashboard('capillas&fnt=dashboard')",$this->html->dashboard($items));
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
		$pdf->Write(5, 'Lista de Capillas');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(80,7,'capilla',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select nombre from capillas order by nombre;";
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
		$pdf->Output('capillas.pdf','I');

	}
	
	public function capillamenu($req)
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
					return $this->dashboardcapilla();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index.php')");
	}
}

?>
