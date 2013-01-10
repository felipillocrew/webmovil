/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var elementoconfoco=-1;
function objeto(file)
{
	xmlhttp=false;
	this.AjaxFailedAlert = "Su navegador no soporta las funciónalidades de este sitio.";
	this.requestFile = file;
	this.encodeURIString = true;
	this.execute = false;
	if (window.XMLHttpRequest) {
		this.xmlhttp = new XMLHttpRequest();
		if (this.xmlhttp.overrideMimeType) {
			this.xmlhttp.overrideMimeType('text/xml');
		}
	}
	else if (window.ActiveXObject) { // IE
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				this.xmlhttp = null;
			}
		}
		if (!this.xmlhttp && typeof XMLHttpRequest!='undefined') {
			this.xmlhttp = new XMLHttpRequest();
			if (!this.xmlhttp){
				this.failed = true;
			}
		}
	}
	return this.xmlhttp ;
}

function replaceAll( text, busca, reemplaza ){
  while (text.toString().indexOf(busca) != -1){
      text = text.toString().replace(busca,reemplaza);
  }
  return text;
}

function statechanged()
{
	var ln=0;
	var divid;
	if (xmlHttp.readyState==4 && xmlHttp.status == 200)
	{

		s=xmlHttp.responseText;
        console.log(s);
		if (s=='' | s==undefined) return;
		if (s.search("##")>0) vResp=s.split('##');
		else vResp=s.split('||');
		if (vResp[0]=='ALERT' | vResp[0]=='@ALERT')  alert(vResp[1]);
		else
		{
			if  (vResp[0]!='NULL' && vResp[0]!=undefined)
			{
				c=0;
				vDivID=vResp[0].split('|');
				vDato=vResp[1].split('|');
				for (n in vDivID)
				{
					var data=vDato[c];
					if  (vDato[c]!='NULL' && vDato[c]!=undefined)
					{
						/*alert('data.len=' + data.length + '\ndiv=' + vDivID[n])*/
						el=document.getElementById(vDivID[n]);

						if(el.type=="text") el.value=vDato[c];
						else if (el.type=="select-one") el.value=vDato[c];
						else el.innerHTML=vDato[c];
					}
					c=c+1;
					if(data.length>ln)
					{
						divid=vDivID[n];
						ln=data.length
					}
				}

				if  (vDato[c]!='NULL' && vDato[c]!=undefined) alert(vDato[c])
			}
		}
		//alert(divid);
		gotodiv(divid);
	}
}

function statechangedgo()
{
    var pagina='';
	var ln=0;
	var divid;

	if (xmlHttp.readyState==4 && xmlHttp.status == 200)
	{
            s=xmlHttp.responseText;
			console.log(s);
            if (s=='' | s==undefined) return;
            if (s.search("##")>0) vResp=s.split('##');
            else vResp=s.split('||');

            if (vResp[0]=='ALERT' | vResp[0]=='@ALERT') alert(vResp[1]);
            else if (vResp[0]=='pagina' | vResp[0]=='@pagina')pagina=vResp[1];
            else {
                    if  (vResp[0]!='NULL' && vResp[0]!=undefined)
                    {
						c=0;
						vDivID=vResp[0].split('|');
						vDato=vResp[1].split('|');
						for (n in vDivID)
						{
							var data=vDato[c];
							if  (vDato[c]!='NULL' && vDato[c]!=undefined)
							{
								/*alert('data.len=' + data.length + '\ndiv=' + vDivID[n])*/
									el=document.getElementById(vDivID[n]);

									if(el.type=="text") el.value=vDato[c];
									else if (el.type=="select-one") el.value=vDato[c];
									else el.innerHTML=vDato[c];
							}
							c=c+1;
								if(data.length>ln)
								{
									divid=vDivID[n];
									ln=data.length
								}
						}
						if  (vDato[c]!='NULL' && vDato[c]!=undefined) pagina=vDato[c];
                    } 
            }
            if(pagina!='')
            {
                window.location = pagina;
                location.href=pagina;
			}
			else gotodiv(divid);
    }
	else if(xmlHttp.status == 404)
	{
		alert('error');
		window.location = 'index.php';
        location.href='index.php';
	}
}


function postform(_pagina,frm,dfunction,elementos,origenid)
{
	if (!frm)
	{
		alert('ERROR: no frm en postform');
	}

	xmlHttp=objeto(_pagina);
	if (typeof origenid == 'undefined')
		origenid='';
	if(params = getForm(frm,elementos))
	{
		xmlHttp.open("POST",_pagina, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.onreadystatechange = statechanged;
		iev_t=new Date().getTime();
		params=params+'&fnt='+dfunction+'&origenid='+origenid+'&iev_t='+iev_t+"&req=1";
		xmlHttp.send(params);
	}
	return false;
}

function post(_pagina,frm,dfunction,elementos,origenid)
{
	if (!frm)
	{
		alert('ERROR: no frm en postform');
	}

	var pagina='index.php'
	xmlHttp=objeto(pagina);
	if (typeof origenid == 'undefined')
		origenid='';
	if(params = getForm(frm,elementos))
	{
		xmlHttp.open("POST",pagina, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.onreadystatechange = statechangedgo;
		iev_t=new Date().getTime();
		params=params+'&page='+_pagina+'&fnt='+dfunction+'&origenid='+origenid+'&iev_t='+iev_t+"&req=1";
		xmlHttp.send(params);
	}
	return false;
}

function postformgo(_pagina,frm,dfunction,elementos,origenid)
{
	if (!frm)
	{
		alert('ERROR: no frm en postform');
	}

	xmlHttp=objeto(_pagina);
	if (typeof origenid == 'undefined')
		origenid='';
	if(params = getForm(frm,elementos))
	{
		xmlHttp.open("POST",_pagina, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.onreadystatechange = statechangedgo;
		iev_t=new Date().getTime();
		params=params+'&fnt='+dfunction+'&origenid='+origenid+'&iev_t='+iev_t+"&req=1";
		xmlHttp.send(params);
	}
	return false;
}


function ischecked(obj)
{
	var value=0;
	var o=document.getElementById(obj);
	if(o.checked)
	{
		value=1;
	}
	return value;
}

function getForm(fobj,elementsnames)
{
	var str = "";
	var ft = "";
	var fv = "";
	var fn = "";
	var els = "";

	var requerido = "";
	var id = "";



	if (elementsnames) {
		velements=elementsnames.split(',');


		for (n in velements) {

			els = fobj.elements[velements[n]];

			try {


				fv = els.value;
				fn = els.name;
				ft = els.title;
				id = els.id;


			}
			catch(err)  {
				alert(' Elemento no encontrado: '+velements[n]);
			}




//			alert(fn+' Tipo: '+els.type);
			switch(els.type) {
				case undefined://radioboton
					for(x = 0;els.length; x++)
					{
						if (els[x]==undefined) break;
						if (els[x].checked)
						{
							fv = els[x].value;
							fn = els[x].name;
							str += fn + "=" + encodeURI(fv) + "&";
							break;
						}

					}

					break;

				case "text":
				case "tel":
				case "email":
				case "search":
				case "hidden":
				case "password":
				case "textarea":
					// is it a required field?
					if(ft != "" && encodeURI(fv).length < 1) {
						alert(ft+":  es un campo requerido, favor llenarlo.");
						els.focus();
						return false;
					}
					str += fn + "=" + encodeURI(fv.replace(/&/g,"!ampersand")) + "&";
					break;

				case "checkbox":
				case "radio":

					if(els.checked)
					{
						str += fn + "=" + encodeURI(fv) + "&";
					}
					else
					{
						str += fn + "=" + 0 + "&";
					}
					break;


				case "select-one":
					try {

						if(ft != "" && encodeURI(fv).length < 1) {
							alert(ft+":  es un campo requerido, favor llenarlo.");
							els.focus();
							return false;
						}
						str += fn + "=" + els.options[els.selectedIndex].value + "&";
					}
					catch(err)  {}
					break;


				case "select-multiple":
					try {
						//alert('select-multiple:'+fn);
						var valor = "";
						for(i=0;i<els.length;i++)
						{
							if(els[i].selected)
							{
								//val += element[i].val+"¦";
								valor += els[i].value + ":";
							}
						}
						valor = valor.slice(0, -1);
						str += fn + "=" + valor + "&";

					}
					catch(err)  {}

					break;
			} // switch
		} // for
		str = str.substr(0,(str.length - 1));
		return str;




	}


	else {

		for(var i = 0;i < fobj.elements.length;i++) {
			els = fobj.elements[i];
			ft = els.title;
			fv = els.value;
			fn = els.name;


//			alert(fn+' Tipo: '+els.type);
			
			switch(els.type) {
				case undefined://radioboton


					for(x = 0;els.length; x++)
					{
						if (els[x]==undefined) break;
						if (els[x].checked)
						{
							fv = els[x].value;
							fn = els[x].name;
							str += fn + "=" + encodeURI(fv) + "&";
							break;
						}

					}

					break;

				case "text":
				case "tel":
				case "email":
				case "search":
				case "hidden":
				case "password":
				case "textarea":
					// is it a required field?
					if(ft != "" && encodeURI(fv).length < 1) {
						alert(ft+":  es un campo requerido, favor llenarlo.");
						els.focus();
						return false;
					}
					str += fn + "=" + encodeURI(fv.replace(/&/g,"!ampersand")) + "&";
					break;

				case "checkbox":
				case "radio":
					if(els.checked) str += fn + "=" + encodeURI(fv) + "&";
					else str += fn + "=" + 0 + "&";
					break;



				case "select-one":
					try {

						if(ft != "" && encodeURI(fv).length < 1) {
							alert(ft+":  es un campo requerido, favor llenarlo.");
							els.focus();
							return false;
						}
						str += fn + "=" + els.options[els.selectedIndex].value + "&";
					}
					catch(err)  {}


					break;


				case "select-multiple":
					try {
						//alert('select-multiple:'+fn);
						var valor = "";
						for(i=0;i<els.length;i++)
						{
							if(els[i].selected)
							{
								//val += element[i].val+"¦";
								valor += els[i].value + ":";
							}
						}
						valor = valor.slice(0, -1);
						str += fn + "=" + valor + "&";

					}
					catch(err)  {}

					break;
			} // switch
		} // for
		str = str.substr(0,(str.length - 1));
		return str;
	}
}

function _statechanged()
{
	var divid='statusajax';
	if (ajax.readyState==1)
	{
		document.getElementById(divid).innerHTML =" <h3>Aguarde por favor...</h3>"; /*<img src='/images/loader.gif' align='center'>*/
	}
	if (ajax.readyState==4)
	{
		if(ajax.status==200)
		{
			s=ajax.responseText;
			if (s=='' | s==undefined) return;
			if (s.search("##")>0)
			{
				vResp=s.split('##');
			}
			else
			{
				vResp=s.split('||');
			}
			if (vResp[0]=='ALERT' | vResp[0]=='@ALERT')
			{
				alert(vResp[1]);
			}
			else
			{
				if  (vResp[0]!='NULL' && vResp[0]!=undefined)
				{
					c=0;
					vDivID=vResp[0].split('|');
					vDato=vResp[1].split('|');
					for (n in vDivID)
					{
						if  (vDato[c]!='NULL' && vDato[c]!=undefined)
						{
							el=document.getElementById(vDivID[n]);
							alert(el);
							if (el.type=="text")
							{
								el.value=vDato[c];
							}
							else
							{
								el.innerHTML=vDato[c];
							}
						}//fin if
						c=c+1;
					}//fin for
					if  (vDato[c]!='NULL' && vDato[c]!=undefined)
					{
						alert(vDato[c])
					};
				}//fin if
			}//in else
		}//fin if
	}

}

function setfunctionid(pagina,fnt,id)
{
	var _pagina='index.php'
    xmlHttp=objeto(_pagina);
    var url='./'+_pagina;
    iev_t=new Date().getTime();

    url=url+"?page="+pagina+"&fnt="+fnt+"&id="+id+"&req=1";
    xmlHttp.onreadystatechange=getstatechanged;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}
function newgotofunction(pagina,fnt)
{
	var _pagina='index.php';
    xmlHttp=objeto(_pagina);
    var url='./'+_pagina;
    iev_t=new Date().getTime();

    url=url+'?'+"page="+pagina +'&'+"fnt="+fnt+"&req=1";
    xmlHttp.onreadystatechange=getstatechanged;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function gotopagefunction(pagina)
{
	var _pagina='index.php';
    xmlHttp=objeto(_pagina);
    var url='./'+_pagina;
    iev_t=new Date().getTime();

    url=url+"?page="+pagina+"&req=1";
    xmlHttp.onreadystatechange=getstatechanged;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function setparam(pagina,params)
{
	var _pagina='index.php';
    xmlHttp=objeto(_pagina);
    var url='./'+_pagina;

    url=url+"?page="+pagina+'&'+params+"&req=1";
    xmlHttp.onreadystatechange=statechangedgo;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function gotofunctiongo(pagina,fnt)
{
	var _pagina='index.php';
    xmlHttp=objeto(_pagina);
    var url='./'+_pagina;
    iev_t=new Date().getTime();

    url=url+"?page="+pagina+"fnt="+fnt;
    xmlHttp.onreadystatechange=statechangedgo;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
}

function getstatechanged()
{
	var ln=0;
	var divid;
	var data;
    if (xmlHttp.readyState==4)
    {
		borrardivs();
        s=xmlHttp.responseText;
        
        if (s=='' | s==undefined) return;
        if (s.search("##")>0) vResp=s.split('##');
        else vResp=s.split('||');
        if (vResp[0]=='ALERT' | vResp[0]=='@ALERT') alert(vResp[1]);
        else
        {
			if  (vResp[0]!='NULL' && vResp[0]!=undefined)
			{
				c=0;
				vDivID=vResp[0].split('|');
				vDato=vResp[1].split('|');
				for (n in vDivID)
				{
					data=vDato[c];
					if  (vDato[c]!='NULL' && vDato[c]!=undefined)
					{
							/*alert('data.len=' + data.length + '\ndiv=' + vDivID[n])*/
							el=document.getElementById(vDivID[n]);
							if (el.type=="text") el.value=vDato[c];
							else el.innerHTML=vDato[c];
					}//fin if
					c=c+1;
					if(data.length>ln)
					{
						divid=vDivID[n];
						ln=data.length
					}
				}//fin for
				if  (vDato[c]!='NULL' && vDato[c]!=undefined) alert(vDato[c])
			}//fin if
        }//in else
		//alert(divid);
		gotodiv(divid);
    }
}




function ocultarFila(num,ver)
{
  dis= ver ? '' : 'none';
  tab=document.getElementById('tabla');
  tab.getElementsByTagName('tr')[num].style.display=dis;
}
function ocultarColumna(num,ver)
{
  dis= ver ? '' : 'none';
  fila=document.getElementById('tabla').getElementsByTagName('tr');
  for(i=0;i<fila.length;i++)
    fila[i].getElementsByTagName('td')[num].style.display=dis;
}



function gotourl(frm,url,dfunction,origenid)
{
 if(params = getForm(frm))
 {
    iev_t=new Date().getTime();
    params=params+'&fnt='+dfunction+'&origenid='+origenid+'&iev_t='+iev_t;
    location.href=url+'?'+params;
    window.location = url+'?'+params;
 }
  return false;
}

function gotourlfnt(url,dfunction,id)
{
    location=url+'?fnt='+dfunction+'&id='+id;
}

function onlygo(page,dfunction)
{
	var _pagina='index.php';
    location=_pagina+'?page='+page+'&fnt='+dfunction;
}

function gotopage(url)
{
    window.location = url;
    location.href=url;
}

function jsconfirmeliminar(pagina,fnt,id,evento)
{
	var _pagina='index.php';
	if(confirm('Desea ' +evento+ ' este elemento?'))
	{
		xmlHttp=objeto(_pagina);
		var url='./'+_pagina;
		iev_t=new Date().getTime();

		url=url+"?page="+pagina+"&fnt="+fnt+"&id="+id+"&req=1";
		xmlHttp.onreadystatechange=getstatechanged;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}
}

function borrardivs()
{
	$('#statusajax').empty();
	$('#formulario').empty();
	$('#pruebajax').empty();
}

function gotodiv(divid)
{
	$('html,body').animate({scrollTop: $("#"+divid).offset().top},'slow');
}

