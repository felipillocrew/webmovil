<?php
class sesion
{
	private $db;
	public  function __construct($db)
    {
        session_start ();
		$this->db=$db;
    }

    public function set($nombre, $valor)
    {
        $_SESSION [$nombre] = $valor;
    }

    public function get($nombre)
    {
        if (isset ( $_SESSION [$nombre] ))
        {
            return $_SESSION [$nombre];
        }
        else
        {
            return false;
        }
    }

    public function elimina_variable($nombre)
    {
        unset ( $_SESSION [$nombre] );
    }
    
    public function issesion()
    {
        if($this->get('login')=='si') return true;
        else return false;
    }

    public function issesion2()
    {
        if($this->get('login')=='si')
        {
            $recordarme=$_SESSION["recordarme"];
            if($recordarme=='si') return true;
            else
            {
            $ultimoacceso = $_SESSION["ultimoacceso"];
            $ahora = date("Y-n-j H:i:s");
            //comparamos el tiempo transcurrido
            $tiempo_transcurrido = (strtotime($ahora)-strtotime($ultimoacceso));
            //si pasaron 15 minutos o más
            if($tiempo_transcurrido >= 900)
            {
                $this->termina_sesion();
                return false;
            }
            //sino, actualizo la fecha de la sesión
            else
            {
                $_SESSION["ultimoAcceso"] = $ahora;
                return true;
            }
            }
        }
        else
        {
            return false;
        }
    }
	
    public function termina_sesion()
    {
        $_SESSION = array();
        session_destroy ();
    }

	public function usuario()
	{
		return $this->get('usuario');
	}

}
?>
