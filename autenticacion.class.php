<?php
class LOGIN
{
	private $sesion;
	private $db;
	private $html;
	private $login;

	public function __construct($sesion,$db,$html)
    {
        $this->sesion=$sesion;
		$this->login=$sesion->issesion();
		$this->db=$db;
		$this->html=$html;
    }

	function loginmenu($req)
	{
		list($fnt)=$this->html->datosform('fnt', $req);
		if($this->login==true)
		{
		 switch ($fnt)
		 {
			case 'logout':
				return $this->logout();
				break;
			default:
				echo $this->html->urlscript($url='js/funciones.js').$this->html->onload ("gotopage('index.php')");
				break;
		 }
		}
		else
		{
			switch ($fnt)
			{
				case 'login':
					return $this->login($req);
					break;
				default:
					echo $this->html->urlscript($url='js/funciones.js').$this->html->onload ("gotopage('index.php')");
					break;
			}
		}
	}

	function login($req,$usuario='',$clave='')
	{
		list($usuario,$clave)=$this->html->datosform('usuario,clave', $req);
		$user=$usuario;
		$pass=$clave;
		if($user=='') return "statusajax||".  $this->html->msgbox("Debe indicar un Usuario valido",'alert');
		if($pass=='') return "statusajax||".  $this->html->msgbox("Debe especificar una Clave",'alert');
		$sql="select usuario,clave from usuarios where usuario='$user'";
		list($status,$res)=$this->db->runSQL($sql,2);
		if(!$status) return "sqlerror||".$this->html->msgbox($res,'error');
		else
		{
			if(empty ($res)) return "statusajax||".  $this->html->msgbox("El Usuario <b>$user</b>, no existe",'alert');
			else
			{
				list(list($userdb,$passdb))=$res;
				list($status,$res)=$this->validar(array($user,$pass),array($userdb,$passdb));
				if(!$status) return "statusajax||".$this->html->msgbox($res,'error');
				else
				{
					$this->sesion->set("usuario",utf8_decode($userdb));
					$this->sesion->set("login",'si');
					$this->sesion->set("ultimoacceso",date("Y-n-j H:i:s"));
					return "pagina||index.php";
				}
			}
		}
	}

	function validar($user,$db)
	{
		if($user[0]==$db[0])
		{
			if(md5(trim($user[1]))==$db[1]) return array(true,'Correcto');
			 else return array(false,"Password Incorrecto");
		}
		else return array(false,'Usuario Incorrecto');
	}

	function logout()
	{
		$this->sesion->termina_sesion();
		return $this->html->urlscript($url='js/funciones.js').$this->html->onload ("gotopage('index.php')");
	}
}

?>
