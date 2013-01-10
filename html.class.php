<?php

class html {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function input($name, $type, $size, $value, $maxlength, $class = '', $js = '') {
        $input = '';
        if ($value != '' and $type != 'checkbox')
            $value = "value=\"$value\"";
        $type = $type != '' ? "type=\"$type\"" : '';
        if ($size != '')
            $size = "size=\"$size\"";
        if ($name != '')
            $name = "id=\"$name\" name=\"$name\"";
        if ($maxlength != '')
            $maxlength = "maxlength=\"$maxlength\"";
        if ($class != '')
            $class = "class=\"$class \"";
        if (strpos($type, 'checkbox'))
            $input.="<input $type value=\"1\" $value $name></td>";
        else
            $input.="<input  $type $class  $name $size $maxlength  $value $js/>";
        return $input;
    }

    function input5($name, $type) {
//		@type
//		button,checkbox,color,date,datetime,datetime-local
//		email,file,hidden,image,month,number,password,radio
//		range,reset,search,submit,tel,text,time,url,week
        $name = $name != '' ? "name=\"$name\" id=\"$name\"" : '';
        $type = $type != '' ? "type=\"$type\"" : '';
        $input = "<input  $name $type  $size $maxlength  $value $js/>";
        return $input;
    }

    function inputdet($input, $inputname, $label = '', $info = '') {
        $inputdet = '';
        if ($label != '')
            $inputdet.="<label for=\"$inputname\">" . $this->nbsp($label) . "</label>";
        if ($info != '')
            $inputdet.="<div class=\"infobox\">$info</div>";
        $div = $inputname . 'ajax';
        $inputdet.="<div id=\"$div\">$input</div>";
        return $inputdet;
    }

    function textarea($text, $class = '', $name = 'nota', $rows = 8, $cols = 70) {
        $text = "<textarea class=\"$class\" rows=\"$rows\" cols=\"$cols\" name=\"$name\" id=\"$name\">$text</textarea>";
        return $text;
    }

    function a($text, $url, $js = '', $class = '') {
        if ($url != '')
            $url = 'href="' . $url . '"';
        if ($class != '')
            $class = 'class="' . $class . '"';
        $a = '<a ' . $class . ' ' . $url . ' ' . $js . '>' . $text . '</a>';
        return $a;
    }

    function nbsp($text) {
        return str_replace(' ', '&nbsp;', $text);
    }

    function msgbox($msg, $class = 'info') {
        return "<div class=\"$class\">$msg</div>"; /* info,ok,alert,error */
    }

    function form($fname, $fields, $labels, $names, $infos, $title = '', $imagen = array()) {
        $form = "<h2>$title</h2>";
        if (!empty($imagen)) {
            if ($imagen[imagen] != '')
                $form.=$this->uploadimagen($imagen[tipo], $imagen[id], $imagen[imagen]);
            else
                $form.=$this->uploadimagen($imagen[tipo], $imagen[id]);
        }

        $form.="<form name=\"$fname\" id=\"$fname\">";

        if ($labels <> '')
            $etiquetas = explode('|', $labels);
        if ($fields <> '')
            $inputs = explode('|', $fields);
        if ($names <> '')
            $name = explode('|', $names);
        if ($infos <> '')
            $info = explode('|', $infos);
        foreach ($this->zipped($etiquetas, $inputs, $name, $info) as $value) {
            list($label, $input, $inputname, $inf) = $value;
            $form.=$this->inputdet($input, $inputname, $label, $inf);
        }
        $form.="</form>";
        return $form;
    }

    function form2($fname, $fields, $labels, $names, $infos, $title = '', $imagen = array()) {
        $form = "<h2>$title</h2>";
        if (!empty($imagen)) {
            if ($imagen[imagen] != '')
                $form.=$this->uploadimagen($imagen[tipo], $imagen[id], $imagen[imagen]);
            else
                $form.=$this->uploadimagen($imagen[tipo], $imagen[id]);
        }

        $form.="<form name=\"$fname\" id=\"$fname\">";
        foreach ($this->zipped($labels, $fields, $names, $infos) as $value) {
            list($label, $input, $inputname, $inf) = $value;
            $form.=$this->inputdet($input, $inputname, $label, $inf);
        }
        $form.="</form>";
        return $form;
    }

    function dockitem($href, $span, $images) {
        return "<a class=\"dockB-item\" href=\"$href\"><span>$span</span><img src=\"$images\" alt=\"$span\" /></a>";
    }

    function htmlpage($head, $body) {
        $html = "<html>
				$head
				$body
				</html>";
        return $html;
    }

    function body($content, $class = '') {
        $body = "<body class=\"$class\">
				$content
			   </body>";
        return $body;
    }

    function head($js = '', $css = '', $titulo = 'FelipilloCrew', $icon = '', $meta = '') {
        $head = "<head>
				$meta
				$js
				$css
				$icon
				<title>$titulo</title>
			   </head>";
        return $head;
    }

    function urlscript($url) {
        $script = "<script type=\"text/javascript\" src=\"$url\"></script>\n";
        return $script;
    }

    function urlcss($url, $media = '') {
        $css = "<link rel=\"stylesheet\" href=\"$url\" type=\"text/css\" media=\"$media\" />\n";
        return $css;
    }

    function icon($url) {
        $icon = "<link rel=\"shortcut icon\" href=\"$url\" type=\"image/x-icon\" />";
        return $icon;
    }

    function meta() {
        return '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">';
    }

    function script($js) {
        $script = "<script type=\"text/javascript\">
			$js</script>";
        return $script;
    }

    function style($css) {
        $style = "<style type=\"text/css\">
			$css</style>";
        return $style;
    }

    function div($id, $content, $class = '', $style = '', $js = '') {
        if ($class != '')
            $class = 'class="' . $class . '"';
//		if($style!='') $style='style="'.$style.'"';
        $div = "<div id=\"$id\" $class $style $js>
			$content
		   </div>";
        return $div;
    }

    function datepicker($fecha = '', $class = '', $id = 'fecha', $name = 'fecha', $format = 'ymd', $sep = '-') {
        $value = date('Y-m-d');
        if (empty($fecha) == false) {
            $value = $fecha;
        }
        $fecha = "<input  class=\"$class\" type=\"text\" id=\"$id\" name=\"$name\" size=\"12\" maxlength=\"12\"  value=\"$value\" onclick=\"displayDatePicker('$id','','$format','$sep');\"/>";
        return $fecha;
    }

    function db2htmltable2($div, $rows, $header, $titulo = '', $border = 0, $cellspacing = 2, $cellpadding = 2, $align = 'left') {
        $html = "<h2>$titulo</h2>{$this->msgbox('No hay Datos')}";
        if (!empty($rows)) {
            $html = " <h2>$titulo</h2>
					<div id=\"$div\">
					<table border=\"$border\" cellspacing=\"$cellspacing\" cellpadding=\"$cellpadding\" style=\"width: 100%; text-align: left;\">
					";
            if ($header <> '') {
                $tilulos = explode(',', $header);
                $html.='<thead>';
                foreach ($tilulos as $key => $value) {
                    $html.="<th style=\"vertical-align:middle; text-align: $align;\">$value</th>";
                }
                $html.='</thead>';
            }

            $html.='<tbody>';
            $count = 0;
            foreach ($rows as $key => $value) {
                $html.="<tr>";
                $count++;
                $html.="<td style=\"vertical-align:middle; text-align: $align;\">$count</td>";
                foreach ($value as $id => $data) {
                    $html.="<td style=\"vertical-align:middle; text-align: $align;\">$data</td>";
                }
                $html.='</tr>';
            }
            $html.="</tbody>
					</table>
					</div>";
        }
        return $html;
    }

    function combostring($name, $nombrevalor, $values, $id = 0, $js = "", $addlist = "") {
        $combo = "<select name=\"$name\" id=\"$name\" $js \">\n";
        if ($addlist <> "") {
            $item = explode("|", $addlist);
            $combo.="<option value=\"" . $item[1] . "\">" . $item[0] . "</option>\n";
        }
        $nombres = explode(",", $nombrevalor);
        $valores = explode(",", $values);
        for ($i = 0; $i < count($valores); $i++) {

            if ($id == $valores[$i]) {
                $combo.= "<option value=\"" . $valores[$i] . "\" selected>" . $nombres[$i] . "</option>\n";
            } else {
                $combo.= "<option value=\"" . $valores[$i] . "\">" . $nombres[$i] . "</option>\n";
            }
        }
        $combo.= "</select>\n\n";
        return $combo;
    }

    function mayuscula($texto, $modo = 2) {
        if ($modo == 1) {
            $texto = strtoupper($texto);
        } else if ($modo == 2) {
            $texto = ucwords(strtolower($texto));
        } else if ($modo == 3) {
            $texto = ucfirst(strtolower($texto));
        } else if ($modo == 4) {
            $texto = strtolower($texto);
        } else {
            $texto = $texto;
        }
        return $texto;
    }

    function pagerefesh($url = '', $time = 1) {
        if ($url == '')
            $refresh = "<meta http-equiv=\"refresh\" content=\"$time\" >";
        else
            $refresh = "<meta http-equiv=\"refresh\" content=\"$time;url=$url\">";
        return $refresh;
    }

    function refresh($page, $time = 100) {
        $refresh = "setInterval( \"$page;\", $time );";
        return $refresh;
    }

    function onload($fnt) {
        $fnt = "<img src=\"img/1x1.gif\" onload=\"javascript:$fnt;\">";
        return $fnt;
    }

    function tochecked($valor) {
        return $valor == 1 ? 'value="1" checked' : 'value="0"';
    }

    /*     * ********DEL TEMPLATE****** */

    function loginmodule() {
        $fields = $this->input('usuario', 'text', 20, '', 20, 'username');
        $fields.=$this->input('clave', 'password', 20, '', 20, 'password');
        $fields.=$this->input('loginbtn', 'submit', '', 'Sign in', '', '', 'onclick="javascript:post(\'autenticacion\',this.form,\'login\',\'\')"');
        $labels = '||';
        $names = 'usuario|clave|';
        $infos = '||';
        $content = $this->form('login', $fields, $labels, $names, $infos, 'Login');
        $div = $this->div('login-content', $content);
        return $div;
    }

    function logoadmin() {
        $content = '<img src="img/felipillocrew.png" alt="logo">';
        $div = $this->div('login-header', $content);
        return $div;
    }

    function _dasheader() {
        $content = '<img alt="ajax" src="img/ajax.gif">';
        $div = $this->div('ajax-loading', $content, 'center', 'style="display: none;"');
        $content = $this->a('', '', 'onclick="return loaddashboard(\'dashboard\');"', 'home');
        $content.= $this->a('', 'javascript:onlygo(\'autenticacion\',\'logout\');', '', 'logout');
        $div.=$this->div('main-nav', $content, '', 'style="display: block;"');
        return $div;
    }

    function dashboarditem($title, $img, $fnt, $width = 48) {
        return '<li>
					<a class="radius" onclick="return ' . $fnt . ';">
						<div class="image">
							<img alt="' . $title . '" src="img/' . $img . '.png" width="' . $width . '">
						</div>
						<div class="title">' . $title . '</div>
					</a>
				</li>';
    }

    function dashboard($items) {
        return '<ul class="clearfix" id="nav">
				' . $items . '
				</ul>';
    }

    function dashdiv($title, $fnt, $content) {
        return $this->div('header', '<a class="headerlink" onclick="return ' . $fnt . ';">' . $title . '</a>') . $this->div('dbcontent', $content);
    }

    function popitem($title, $fnt) {
        return '<li>
			<a onclick="return ' . $fnt . '">' . $title . '</a>
		</li>';
    }

    function popmenu($items) {
        return '<ul>' . $items . '</ul>';
    }

    function popdiv($items) {
        $pop_menu = $this->div('pop_menu', $this->popmenu($items), 'pop_menu');
        $pop_toggle = $this->div('pop_toggle', $pop_menu, 'pop_toggle');
        $pop = $this->div('pop', $pop_toggle, 'pop', 'style="z-index: 1002; "');
        return $pop;
    }

    function actionform($fields, $name = '', $action = '', $method = '', $enctype = '') {
        if ($name != '')
            $name = 'name="' . $name . '" id="' . $name . '"';
        if ($action != '')
            $action = 'action="' . $action . '"';
        if ($method != '')
            $method = 'method="' . $method . '"';
        if ($enctype != '')
            $enctype = 'enctype="' . $enctype . '"';
        $form = "<form $action $method $name $id $enctype>";
        $form.=$fields;
        $form.="</form>";
        return $form;
    }

    function fieldset($legend, $content = '') {
        $fieldset = "<fieldset>
			<legend>$legend</legend>";
        $fieldset.=$content;
        $fieldset.='</fieldset>';
        return $fieldset;
    }

    function uploadimagen($tipo = 'joven', $id = 0, $imagen = '') {
        $imagen = $this->mayuscula($imagen, 4);
        $imagen = $this->buscarimagen($imagen);
        $imagen = '<img src="images/' . $imagen . '" border="0">';
        $foto = $this->div('upload_area', $imagen);
        $file = $this->input('file', 'file', '', '', '', 'file', "onchange=\"ajaxUpload(this.form,'ajaxupload.php?filename=file&tipo=$tipo&id=$id','upload_area','Subiendo...<br /><img src=\'img/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />','<img src=\'img/error.gif\' width=\'16\' height=\'16\' border=\'0\' /> Error al subir la imagen'); return false;\"");
        $form = $this->actionform($file, 'sleeker', 'index.php', 'post', 'multipart/form-data');
        $div = $this->div('imagen', $foto . $form);
        return $div . '<img onload="javascript:initfake()" src="/img/1x1.gif">';
    }

    function buscarimagen($imagen) {
        if ($imagen != '') {
            $_nombre = $imagen;
            $directorio = opendir("./images/");
            while ($archivo = readdir($directorio)) {
                $_archivo = $this->mayuscula($archivo, 4);
                list($nombre_, $ext_) = explode('.', $_archivo);
                if ($nombre_ == $_nombre) {
                    return $archivo;
                }
            }
            closedir($directorio);
        }
        return 'sinfoto.jpg';
    }

    /*     * ****************************METODOS INTELIGENTES************************************************ */

    function datosform($variables, $post) {
        $datos = array();
		$nofound = false;
        $variables = explode(',', $variables);
        foreach ($variables as $id => $var) {
            foreach ($post as $campo => $valor) {
                if ($var == $campo){
					$datos[] = $valor;
					$nofound = false;
					break;
				}
				else $nofound = true;
            }
			if($nofound) $datos[] = '';
        }
		if (empty($datos)){
			foreach ($variables as $id => $var){
				$datos[] = '';
			}
		}
        return $datos;
    }

    function zipped() {
        $args = func_get_args();

        $ruby = array_pop($args);
        if (is_array($ruby))
            $args[] = $ruby;

        $counts = array_map('count', $args);
        $count = ($ruby) ? min($counts) : max($counts);
        $zipped = array();

        for ($i = 0; $i < $count; $i++) {
            for ($j = 0; $j < count($args); $j++) {
                $val = (isset($args[$j][$i])) ? $args[$j][$i] : null;
                $zipped[$i][$j] = $val;
            }
        }
        return $zipped;
    }

    public function sethtmllog($text) {
        $fp = fopen("logs/htmllog.txt", "a");
        fwrite($fp, date('d/m/Y h:i:s A') . $text . PHP_EOL);
        fclose($fp);
    }

    public function output($output, $isURL = '') {
        if ($isURL == 1)
            return $output;
        else {
            $this->sethtmllog($output);
            list($divs, $datas) = explode('||', $output);
            $this->sethtmllog($divs);
            $this->sethtmllog($datas);
            $div = explode('|', $divs);
            $data = explode('|', $datas);
            return $this->zipped($div, $data);
        }
    }

}
?>
