<?php
class ZONAS
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
		$sql=$this->db->actionselect($this->usuario,'zonaid', 'nombre', 'zonas', 'activo=1 order by nombre', 'zonas');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$rows";

		}
		else
		{
			$zonas=$this->html->db2htmltable2('zonas',$rows, '','Lista de Zonas');
			$div='pruebajax|statusajax';
			return "$div||$zonas|$msg";
		}
	}

	private function zona($param,$title='Registro de Nueva Zona')
	{
		list($zonaid,$nombre,$activo,$nota,$boton)=$param;
		$zonaid=$this->html->input('zonaid','hidden', 10, $zonaid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn= $this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('zonas',this.form,'save','')\"");
		$labels='|Nombre|Activo|Nota|';
		$fields="$zonaid|$nombre|$activo|$nota|$btn";
		$names="zonaid|nombre|activo|nota|";
		$info="|Es Obligatoria|||";
		$form=$this->html->form('zonaform',$fields,$labels,$names,$infos,$title);
		return $form;

	}

	private function agregar($boton='actualizar')
	{
		$param=array(0,'','checked','',$boton);
		$html=$this->zona($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($zonaid,$nombre,$activo,$nota)=$this->html->datosform('zonaid,nombre,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$uc=$this->sesion->get('usuario');

		$msg='';
		if($nombre=='')
		{
				$msg=$this->html->input('nombre','text', 40, $nombre, 50, 'textbox obligado');
				return "nombreajax||$msg";
		}

		if($zonaid==0) $sql="insert into zonas (nombre,activo,nota,uc) values ('$nombre',$activo,'$nota','$uc');";
		else if($zonaid>0) $sql="update zonas set nombre='$nombre',activo=$activo,nota='$nota',uc='$uc' where zonaid=$zonaid;";
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$msg";

		}
		else return "pruebajax|statusajax|||".$this->html->msgbox($msg, 'ok');
	}

	private function borrar($zonaid)
	{
		$sql="delete from zonas where zonaid=$zonaid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($zonaid)
	{
		$sql="select nombre,activo,nota from zonas where zonaid=$zonaid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($nombre,$activo,$nota))=$res;
		$activo=$this->html->tochecked($activo);
		$param=array($zonaid,$nombre,$activo,$nota,'Actualizar');
		$html=$this->zona($param,"Actualizando: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardzona()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('zonas','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('zonas','view')");

		return $this->html->dashdiv('Zonas',"loaddashboard('zonas&fnt=dashboard')",$this->html->dashboard($items));
	}

	public  function zonamenu($req)
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
					return $this->dashboardzona();
					break;
			}
		}
		else return $this->html->urlscript('funciones.js').$this->html->onload ("gotopage('index.php')");
	}


	private function pdf()
	{
		$this->sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

		include ('fpdf.php');
		$pdf = new FPDF('P','mm','Letter');
		$pdf->AddPage();

		$pdf->SetFont('Helvetica','',14);
		$pdf->SetFontSize(18);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Write(5, 'Lista de zonas');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(80,7,'zona',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select nombre from zonas order by nombre;";
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
					$pdf->Cell(80,7,$data,1);
				}
				$count++;
				$pdf->Ln();
			}
		}
		$pdf->Output('zonas.pdf','I');

	}
}
?>
