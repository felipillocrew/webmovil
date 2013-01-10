<?php
class USUARIOS
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
		$editar='concat( "<a href=\"javascript:setfunctionid(\'usuarios\',\'edit\',\'",id,"\')\">",nombre,\' \',apellido , "</a>" ) as editar';
		$sql=$this->db->newactionselect($this->usuario,'id', "$editar,usuario", 'usuarios', 'activo=1', 'usuarios');
		
		list($status,$rows)=$this->db->runSQL($sql);
		if(!$status) return "sqlerror||$rows";
		else
		{
			$usuarios=$this->html->db2htmltable2('usuarios',$rows, '','Lista de Usuarios');
			return "pruebajax|statusajax||$usuarios|$msg";
		}
	}

	private function usuario($param,$title='Registro de Nuevo Evento')
	{
		list($usuarioid,$nombre,$apellido,$correo,$usuario,$clave,$cpclave,$usertype,$activo,$boton)=$param;
		$usuarioid=$this->html->input('usuarioid','hidden', 10, $usuarioid, 10, 'textbox');
		$nombre=$this->html->input('nombre','text', 50, $nombre, 20, 'textbox');
		$apellido=$this->html->input('apellido','text', 50, $apellido, 20, 'textbox');
		$correo=$this->html->input('correo','email', 50, $correo, 50, 'textbox');
		$usuario=$this->html->input('usuario','text', 20, $usuario, 20, 'textbox');
		$clave=$this->html->input('clave','password', 20, $clave, 20, 'textbox');
		$cpclave=$this->html->input('cpclave','password', 20, $cpclave, 20, 'textbox');
		$activo=$this->html->input('activo','checkbox', 10, $activo, 10, 'textbox');
		$btn=$this->html->input($name=$boton, $type='button', $size='', $value=$this->html->mayuscula($boton), $maxlength='',$class='button big',$js="onclick=\"javascript:post('usuarios',this.form,'save','')\"");

//		$labels='|Nombre|Apellido|Correo|Usuario|Contraseña|Repita la Contraseña||Tipo de Usuario|Activo|';
//		$fields="$usuarioid|$nombre|$apellido|$correo|$usuario|$clave|$cpclave|$usertype|$activo|$btn";
//		$names="usuarioid|nombre|apellido|correo|usuario|clave|cpclave|usertype|activo|";
//		$info="|Es Obligatorio|||||debe coincidir con la Contraseña|||";

		$labels= array('','Nombre','Apellido','Correo','Usuario','Contraseña','Repita la Contraseña','Tipo de Usuario','Activo','');
		$fields= array($usuarioid,$nombre,$apellido,$correo,$usuario,$clave,$cpclave,$usertype,$activo,$btn);
		$names=array('usuarioid','nombre','apellido','correo','usuario','clave','cpclave','usertype','activo','');
		$info=array('','Es Obligatorio','','','','','debe coincidir con la Contraseña','','','');
		
		$form=$this->html->form2('usuarioform',$fields,$labels,$names,$infos,$title);
		return $form;
	}

	private function agregar($boton='actualizar')
	{
		$usertype=$this->html->combostring('usertype','Registrado,Administrador,SUDO','0,5,6');
		$param=array(0,'','','','','','',$usertype,'checked',$boton);
		$html=$this->usuario($param);
		return "pruebajax|formulario|||$html";
	}

	private function editar($usuarioid)
	{
		$sql="select id,nombre,apellido,correo,usuario,usertype,activo from usuarios where id=$usuarioid;";
		list($status,$res)=$this->db->runSQL($sql,2);
		list(list($usuarioid,$nombre,$apellido,$correo,$usuario,$usertype,$activo))=$res;
		$activo=$this->html->tochecked($activo);
		$usertype=$this->html->combostring('usertype','Registrado,Administrador,SUDO','0,5,6',$usertype);
		$clave=$cpclave='';
		$param=array($usuarioid,$nombre,$apellido,$correo,$usuario,$clave,$cpclave,$usertype,$activo,'Actualizar');
		$html=$this->usuario($param,"Actualizando al Usuario: $nombre");
		return "pruebajax|formulario|statusajax|||$html|";
	}
	
	private function grabar($post)
	{
		list($usuarioid,$nombre,$apellido,$correo,$usuario,$clave,$cpclave,$usertype,$activo)=$this->html->datosform('usuarioid,nombre,apellido,correo,usuario,clave,cpclave,usertype,activo', $post);
		$nombre=$this->html->mayuscula($nombre);
		$apellido=$this->html->mayuscula($apellido);
		$uc=$this->sesion->get('usuario');

		if($nombre=='') return "nombreajax||".$this->html->input('nombre','text', 50, $nombre, 20, 'textbox obligado');
		if($apellido=='') return "apellidoajax||".$this->html->input('apellido','text', 50, $apellido, 20, 'textbox obligado');
		if($usuario=='') return "usuarioajax||".$this->html->input('usuario','text', 20, $usuario, 20, 'textbox obligado');

		if($clave!='')
		{
			if($clave!=$cpclave) return "claveajax|cpclaveajax||".$this->html->input('clave','password', 20,'', 20, 'textbox obligado').'|'.$this->html->input('cpclave','password', 20,'', 20, 'textbox obligado');
			$clave=md5(trim($clave));
		}

		if($usuarioid==0)
		{
			if($clave!='') $sql="insert into usuarios (nombre,apellido,correo,usuario,clave,usertype,activo,uc) values ('$nombre','$apellido','$correo','$usuario','$clave',$usertype,$activo,'$uc');";
			else $sql="insert into usuarios (nombre,apellido,correo,usuario,usertype,activo,uc) values ('$nombre','$apellido','$correo','$usuario',$usertype,$activo,'$uc');";
		}
		else if($usuarioid>0)
		{
			if($clave!='') $sql="update usuarios set nombre='$nombre',apellido='$apellido',correo='$correo',usuario='$usuario',clave='$clave',usertype=$usertype,activo=$activo,uc='$uc' where id=$usuarioid;";
			else $sql="update usuarios set nombre='$nombre',apellido='$apellido',correo='$correo',usuario='$usuario',usertype='$usertype',activo='$activo',uc='$uc' where id=$usuarioid;";
		}
		list($status,$msg)=$this->db->exeSQL($sql);
		if(!$status) return "sqlerror||$msg";
		else return "pruebajax|statusajax|||".$this->html->msgbox($msg, 'ok');
	}


	private function borrar($usuarioid)
	{
		/*$sql="delete from usuarios where id=$usuarioid;";*/
		$sql="update usuarios set activo=0 where id=$usuarioid;";
		list($status,$res)=$this->db->exeSQL($sql);
		$class='ok';
		if(!$status) $class='error';

		$msg=$this->html->msgbox("Eliminado: $res",$class);
		return $this->view($msg);
	}

	private function dashboardzona()
	{
		$items=$this->html->dashboarditem('Agregar','new',"newgotofunction('usuarios','add')");
		$items.=$this->html->dashboarditem('Lista','view',"newgotofunction('usuarios','view')");

		return $this->html->dashdiv('Usuarios',"loaddashboard('usuarios&fnt=dashboard')",$this->html->dashboard($items));
	}
	
	public  function usuariomenu($req)
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
				case 'dashboard':
					return $this->dashboardzona();
					break;
			}
		}
		else  return $this->html->urlscript($url='funciones.js').$this->html->onload ("gotopage('index.php')");
	}

}

?>
