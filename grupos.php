<?php
require_once 'mysql.php';
require_once 'html.php';
require_once 'sesion.class.php';

$sesion = new sesion();
$login=$sesion->issesion();

$fnt='';
$req=$_POST;
$fnt=$req['fnt'];
if ($fnt=='')
    $req=$_GET;
    $fnt=$req['fnt'];

switch ($fnt)
{
    case 'add':
        if($login==true) echo agregar('Guardar');
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
    case 'save':
        if($login==true) echo grabar($req);
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
    case 'edit':
        list($id)=datosform('id', $req);
        if($login==true) echo editar($id);
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
    case 'delete':
        list($id)=datosform('id', $req);
        if($login==true) echo borrar($id);
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
    case 'view':
        if($login==true) echo view();
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
    case 'pdf':
        if($login==true) echo pdf();
        else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
        break;
   	case 'dashboard':
       	if($login==true) echo dashboardgrupo();
       	else  echo urlscript($url='funciones.js').onload ("gotopage('index.php')");
       	break;
}
function view($msg='')
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));
    $sql=actionselect('grupoid', "nombre", 'grupos', 'activo=1 order by nombre', 'grupos');

    list($status,$rows)=runSQL($sql);
    if(!$status) return "statusajax||$rows";
    else
    {
        $grupos=db2htmltable2('grupos',$rows, '','Lista de Grupos');
		return "pruebajax|statusajax||$grupos|$msg";
    }
}

function grupo($param,$title='Registro de Nuevo Grupo')
{
    list($grupoid,$nombre,$activo,$nota,$boton)=$param;
    $grupoid=input('grupoid','hidden', 10, $grupoid, 10, 'textbox');
    $nombre=input('nombre','text', 50, $nombre, 50, 'textbox');
    $activo=input('activo','checkbox', 10, $activo, 10, 'textbox');
    $nota=textarea($nota,$class='textbox');
    $btn=  input($name=$boton, $type='button', $size='', $value=mayuscula($boton), $maxlength='',$class='button medium blue',$js="onclick=\"javascript:postformgo('grupos.php',this.form,'save','')\"");
    $labels='|Nombre|Grupo|Activo|Nota|';
    $fields="$grupoid|$nombre|$activo|$nota|$btn";
    $names="grupoid|nombre|activo|nota|";
    $info="|Es Obligatorio|||";
    $form=form('grupoform',$fields,$labels,$names,$infos,$title);
    return $form;

}

function agregar($boton='actualizar')
{
    $param=array(0,'','checked','',$boton);
    $html=grupo($param);
    return "pruebajax|formulario|||$html";
}

function grabar($post)
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

    list($grupoid,$nombre,$activo,$nota)=datosform('grupoid,nombre,activo,nota', $post);
    $nombre=mayuscula($nombre);
    $uc=$sesion->get('usuario');

    $msg='';
    if($nombre=='')
    {
            $msg=input('nombre','text', 40, $nombre, 50, 'textbox obligado');
            return "nombreajax||$msg";
    }

    if($grupoid==0) $sql="Insert into grupos (nombre,activo,nota,uc) values ('$nombre',$activo,'$nota','$uc');";
    else if($grupoid>0) $sql="Update grupos set nombre='$nombre',activo=$activo,nota='$nota',uc='$uc' where grupoid=$grupoid;";

    list($status,$msg)=exeSQL($sql);
    if(!$status)
    {
        $div='sqlerror';
        return "$div||$msg";

    }
    else
    {

        return "pruebajax|statusajax|||".  msgbox($msg, 'ok');
    }
}

function borrar($grupoid)
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

    $sql="delete from grupos where grupoid=$grupoid;";
    list($status,$res)=exeSQL($sql);
    $class='ok';
    if(!$status) $class='error';

    $msg=msgbox("Eliminado: $res",$class);
    return view($msg);
}

function editar($grupoid)
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

    $sql="select nombre,activo,nota from grupos where grupoid=$grupoid;";
    list($status,$res)=runSQL($sql,2);
    list(list($nombre,$activo,$nota))=$res;
    $activo=tochecked($activo);
    $param=array($grupoid,$nombre,$activo,$nota,'Actualizar');
    $html=grupo($param,"Actualizando el grupo: $nombre");
    return "pruebajax|formulario|statusajax|||$html|";
}

function dashboardgrupo()
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

    $items=dashboarditem('Agregar','new',"gotofunction('grupos.php','add')");
    $items.=dashboarditem('Lista','view',"gotofunction('grupos.php','view')");

	return dashdiv('Grupos',"loaddashboard('grupos.php?fnt=dashboard')",dashboard($items));
}


function pdf()
{
    global $sesion;
    $sesion->set("ultimoacceso",date("Y-n-j H:i:s"));

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
    $pdf->Output('grupos.pdf','I');

}

?>
