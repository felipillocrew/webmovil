<?php

class EVENTOS
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
		$participantes='concat( "<a href=\"javascript:setfunctionid(\'asistenciaeventos\',\'participantes\',\'",e.eventoid,"\')\">", "<img src=\'img//user.png\'  width=\'32\' align=\'center\'>", "</a>" ) as participantes';
		$sql=$this->db->actionselect($this->usuario,'e.eventoid', "$participantes,e.nombrecorto", 'eventos e', 'e.activo=1 order by e.fecha  desc', 'eventos');

		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "statusajax||$rows";
		else
		{
			$eventos=$this->html->db2htmltable2('eventos',$rows, '','Lista de Eventos');
			return "pruebajax|statusajax||$eventos|$msg";
		}
	}

	private function evento($param,$title='Registro de Nuevo Evento')
	{
		list($eventoid,$fecha,$nombre,$nombrecorto,$grupoid,$costo,$activo,$nota,$boton)=$param;
		$eventoid=$this->html->input('eventoid','hidden', 10, $eventoid, 10, 'textbox');
		$fecha=$this->html->datepicker($fecha,$class='textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 50, 'textbox');
		$nombrecorto=$this->html->input('nombrecorto','text', 20, $nombrecorto, 20, 'textbox');
		$costo=$this->html->input('costo','number', 10, $costo, 10, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$nota=$this->html->textarea($nota,$class='textbox');
		$btn= $this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:post('eventos',this.form,'save','')\"");
		$labels='|Fecha|Nombre|Nombre Corto|Grupo|Costo|Activo|Nota|';
		$fields="$eventoid|$fecha|$nombre|$nombrecorto|$grupoid|$costo|$activo|$nota|$btn";
		$names='|fecha|nombre|nombrecorto|grupoid|costo|activo|nota|';
		$infos='|Fecha del Evento|Es Obligatorio|Nombre descriptivo|Grupos que pueden participar|Precio del evento|||';
		$form=$this->html->form('eventoform',$fields,$labels,$names,$infos,$title);
		return $form;

	}

	private function agregar($boton='actualizar')
	{
		$fecha=date('Y-m-d');
		$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by nombre',$id=0,$js='',$addlist='Todos|0');
		$param=array(0,$fecha,'','',$grupoid,'','checked','',$boton);
		$html=$this->evento($param);
		return "pruebajax|formulario|||$html";
	}

	private function grabar($post)
	{
		list($eventoid,$fecha,$nombre,$nombrecorto,$grupoid,$costo,$activo,$nota)=$this->html->datosform('eventoid,fecha,nombre,nombrecorto,grupoid,costo,activo,nota', $post);
		$nombre=$this->html->mayuscula($nombre);
		$uc=$this->sesion->get('usuario');

		$msg='';
		if($nombre=='')
		{
				$msg=$this->html->input('nombre','text', 40, $nombre, 50, 'textbox obligado');
				return "nombreajax||$msg";
		}

		if($eventoid==0) $sql="Insert into eventos (fecha,nombre,nombrecorto,grupoid,costo,activo,nota,uc) values ('$fecha','$nombre','$nombrecorto',$grupoid,$costo,$activo,'$nota','$uc');";
		else if($eventoid>0) $sql="Update eventos set fecha='$fecha',nombre='$nombre',nombrecorto='$nombrecorto',grupoid=$grupoid,costo=$costo,activo=$activo,nota='$nota',uc='$uc' where eventoid=$eventoid;";
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status)
		{
			$div='sqlerror';
			return "$div||$msg";

		}
		else return "pruebajax|statusajax|||".$this->html->msgbox($msg, 'ok');
	}

	private function borrar($eventoid)
	{
		$sql="delete from eventos where eventoid=$eventoid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function editar($eventoid)
	{
		$sql="select fecha,nombre,nombrecorto,grupoid,costo,activo,nota from eventos where eventoid=$eventoid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($fecha,$nombre,$nombrecorto,$grupoid,$costo,$activo,$nota))=$res;
		$grupoid=$this->db->combosql('grupoid', 'select grupoid,nombre from grupos where activo=1 order by nombre',$id=$grupoid,$js='',$addlist='Todos|0');
		$activo=$this->html->tochecked($activo);
		$param=array($eventoid,$fecha,$nombre,$nombrecorto,$grupoid,$costo,$activo,$nota,'Actualizar');
		$html=$this->evento($param,"Actualizando el evento: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}

	private function dashboardevento()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('eventos','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('eventos','view')");
		$items.=$this->html->dashboarditem('Participantes','addusers',"newgotofunction('asistenciaeventos','add')");

		return $this->html->dashdiv('Eventos',"loaddashboard('eventos&fnt=dashboard')",$this->html->dashboard($items));
	}

	public function eventos_index()
	{
		$datos='concat( "<a href=\"javascript:setfunctionid(\'eventos\',\'datoseventos\',\'",eventoid,"\')\">", "<img src=\'img/stats.png\'  width=\'32\' align=\'center\'>", "</a>" ) as datos';
		$sqlnew="select $datos,nombre from eventos where fecha>=now() and activo=1 order by fecha desc limit 3";
		$sqlpas="select $datos,nombre from eventos where fecha<now() and activo=1 order by fecha desc limit 3";

		list($status,$new)=$this->db->runSQL($sqlnew);
		list($status,$pas)=$this->db->runSQL($sqlpas);
		if(!$status) return $this->html->msgbox($rows,'error');
		else return "{$this->html->db2htmltable2('eventosnew',$new, '','Proximos Eventos')}<br>{$this->html->db2htmltable2('eventospas',$pas, '','Eventos Anteriores')}";
	}

	private function datoseventos($eventoid)
	{
		$sql="select e.fecha,e.nombre,e.nombrecorto,coalesce((select nombre from grupos where grupoid=e.grupoid),'Todos-Abierto') as grupo,(select count(jovenid) from asistenciaeventos where eventoid=e.eventoid) as participantes,e.costo,e.activo,e.nota from eventos e where e.eventoid=$eventoid;";
//		list($status,$res)=$this->db->runSQL($sql,2);
//		list(list($fecha,$nombre,$nombrecorto,$grupoid,$costo,$activo,$nota))=$res;
//		if(!$status) return "pruebajax||{$this->html->msgbox($rows,'error')}";
//		else
			return "pruebajax||$sql";
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
		$pdf->Write(5, 'Lista de Eventos');
		$pdf->Ln();

		$pdf->SetFontSize(10);

		$pdf->Ln(5);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->Cell(10,7,'#',1);
		$pdf->Cell(50,7,'Fecha',1);
		$pdf->Cell(80,7,'Evento',1);
		$pdf->Cell(50,7,'Referencia',1);
		$pdf->Ln();

		$pdf->SetFont('Helvetica','',10);

		$sql="select DATE_FORMAT(fecha, '%d/%m/%Y') as dia,nombre,nombrecorto from eventos order by fecha;";
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
		$pdf->Output('eventos.pdf','I');

	}

	public function eventomenu($req)
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
					return $this->dashboardevento();
					break;
				case 'datoseventos':
					return $this->datoseventos($id);
					break;
			}
		}
		else
			switch ($fnt)
			{
				case 'datoseventos':
					return $this->datoseventos($id);
					break;
				default :
					return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index.php')");
					break;
			}
	}
}

?>
