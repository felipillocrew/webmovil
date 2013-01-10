<?php

 require_once 'html.class.php';
 require_once 'mysql.class.php';
 require_once 'sesion.class.php';
 require_once 'zonas.class.php';
 require_once 'eventos.class.php';
 require_once 'grupos.class.php';
 require_once 'capillas.class.php';
 require_once 'comunidades.class.php';
 require_once 'jovenes.class.php';
 require_once 'catequistas.class.php';
 require_once 'usuarios.class.php';
 require_once 'asistenciaeventos.class.php';
 require_once 'autenticacion.class.php';

 class INDEX
 {
	private $sesion;
	private $db;
	private $html;
	private $login;
	private $usuario;
	public $pagina='';

	public $prueba='';
	public $form='';
	public $status='';

	public $header;
	public $divid='container';
	public $dashboard='';

	public function __construct()
	{
		$this->db= mysql::getInstance();
		$this->html=new html($this->db);
		$this->sesion=new sesion($this->db);
		$this->login=$this->sesion->issesion();
		$this->usuario=$this->sesion->usuario();
		$this->header=$this->html->_dasheader();
	}

	public function menu($req)
	{
		list($this->pagina)=$this->html->datosform('page', $req);
		if($this->login==true)
		{
			$this->prueba='';
			switch ($this->pagina)
			{
				case 'dashboard':
					echo $this->dashboardmenu();
					break;
				case 'jovenes':
					$this->jovenes($req);
					break;
				case 'catequistas':
					$this->catequistas($req);
					break;
				case 'capillas':
					$this->capillas($req);
					break;
				case 'comunidades':
					$this->comunidades($req);
					break;
				case 'zonas':
					$this->zonas($req);
					break;
				case 'eventos':
					$this->eventos($req);
					break;
				case 'grupos':
					$this->grupos($req);
					break;
				case 'asistenciaeventos':
					$this->asistenciaeventos($req);
					break;
				case 'autenticacion':
					$this->autenticacion($req);
					break;
				case 'usuarios':
					$this->usuarios($req);
					break;
				default:
					$this->dashboardheader();
					break;
			}
		}
		else
		{
			switch ($this->pagina)
			{
				case 'autenticacion':
					$this->autenticacion($req);
					break;
				case 'eventos':
					$this->eventos($req);
					break;
				default:
					$this->loginform();
					break;
			}
		}
	}

	function dashboardmenu()
	{
		$dashboarditems=$this->html->dashboarditem('Eventos','dashboard/eventos',"loaddashboard('eventos&fnt=dashboard')",55);
		$dashboarditems.=$this->html->dashboarditem('Jovenes','dashboard/jovenes',"loaddashboard('jovenes&fnt=dashboard')",55);
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Catequistas','dashboard/catequistas',"loaddashboard('catequistas&fnt=dashboard')");
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Comunidades','dashboard/comunidades',"loaddashboard('comunidades&fnt=dashboard')",70);
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Capillas','dashboard/capillas',"loaddashboard('capillas&fnt=dashboard')");
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Grupos','dashboard/grupos',"loaddashboard('grupos&fnt=dashboard')",80);
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Niveles','dashboard/niveles',"loaddashboard('niveles&fnt=dashboard')");
		if($this->db->isadmin($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Zonas','dashboard/zonas',"loaddashboard('zonas&fnt=dashboard')",70);
		if($this->db->issudo($this->usuario)) $dashboarditems.=$this->html->dashboarditem('Usuarios','user',"loaddashboard('usuarios&fnt=dashboard')",60);

		return $this->html->dashdiv('DashBoad',"loaddashboard('dashboard')",$this->html->dashboard($dashboarditems));
	}

	function index()
	{
		$ajax= $this->html->urlscript($url='js/funciones.js');
		$ajax.= $this->html->urlscript($url='js/jquery.min.js');
		$ajax.= $this->html->urlscript($url='js/custom.js');
		$ajax.= $this->html->urlscript($url='js/datepicker.js');
		$ajax.= $this->html->urlscript($url='js/jquery.si.js');
		$ajax.= $this->html->urlscript($url='js/ajaxupload.js');

		$js='function initfake(){$("input.file").si();}';
		$ajax.= $this->html->script($js);
		$ajax.= $this->html->script('window.onload = function() { setTimeout(function(){window.scrollTo(0, 1);}, 100);}');

		$css= $this->html->urlcss('css/style.css','');
		$css.= $this->html->urlcss('css/datepicker.css');
		$css.= $this->html->urlcss('css/jquery.si.css');
		$icon=$this->html->icon('img/favicon.ico');

		$head=  $this->html->head($ajax,$css,'FelipilloCrew',$icon,$this->html->meta());
		$div=$this->header.$this->html->div('page',$this->html->div('dashboard',$this->dashboard).$this->html->div('content',$this->html->div('statusajax',$this->status).$this->html->div('formulario',$this->form).$this->html->div('pruebajax',  $this->prueba)),'','style="display: block; "');
		$content=$this->html->div($this->divid,$div);
		$body=  $this->html->body($content, 'up');
		$html=  $this->html->htmlpage($head, $body);

		echo $html;
	}

	private function catequistas($req)
	{
		$catequistas= new CATEQUISTAS($this->sesion, $this->db, $this->html);
		echo $catequistas->catequistamenu($req);
	}

	private function jovenes($req)
	{
		$jovenes= new JOVENES($this->sesion, $this->db, $this->html);
		echo $jovenes->jovenmenu($req);
	}

	private function comunidades($req)
	{
		$comunidades= new COMUNIDADES($this->sesion, $this->db, $this->html);
		echo $comunidades->comunidadmenu($req);
	}

	private function capillas($req)
	{
		$capillas= new CAPILLAS($this->sesion, $this->db, $this->html);
		echo $capillas->capillamenu($req);
	}

	private function zonas($req)
	{
		$zona= new ZONAS($this->sesion, $this->db, $this->html);
		echo $zona->zonamenu($req);
	}

	private function eventos($req)
	{
		$eventos= new EVENTOS($this->sesion, $this->db, $this->html);
		echo $eventos->eventomenu($req);
	}
	private function grupos($req)
	{
		$grupos= new GRUPOS($this->sesion, $this->db, $this->html);
		echo $grupos->grupomenu($req);
	}

	private function asistenciaeventos($req)
	{
		$asistenciaeventos= new ASISTENCIAEVENTOS($this->sesion, $this->db, $this->html);
		echo $asistenciaeventos->asistenciaeventomenu($req);
	}

	private function autenticacion($req)
	{
		$autenticacion= new LOGIN($this->sesion, $this->db, $this->html);
		echo $autenticacion->loginmenu($req);
	}

	private function usuarios($req)
	{
		$usuarios= new USUARIOS($this->sesion, $this->db, $this->html);
		echo $usuarios->usuariomenu($req);
	}
	
	private function loginform()
	{
		$this->form=$this->html->loginmodule();
		$this->header=$this->html->logoadmin ();
		$this->index();
	}

	private function dashboardheader()
	{
		$this->header=$this->html->_dasheader();
		$this->dashboard=$this->dashboardmenu();
		$this->index();
	}

 }

$req=$_POST;
if (empty ($req)) $req=$_GET;
$index=new INDEX();
$index->menu($req);

?>
