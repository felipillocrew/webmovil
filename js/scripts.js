function updateOrder(){
	var options = { 
					method: 'post',
					parameters: Sortable.serialize('menus'),
					onComplete: getResponseUpdateOrder
				};
	new Ajax.Request('server-side/update_menu_order.php', options);
}

function getResponseUpdateOrder(oReq){
	clearTimer;
	document.getElementById('messagecenter').innerHTML = 'System Message Center: ' + oReq.responseText;
	mctime = setTimeout(resetMessageCenter,5000);
}

function updateMenuItemsOrder(){
	var options = { 
					method: 'post',
					parameters: Sortable.serialize('menuitems'),
					onComplete: getResponseUpdateMenuItemsOrder
				};
	new Ajax.Request('server-side/update_menu_items_order.php', options);
}

function getResponseUpdateMenuItemsOrder(oReq){
	clearTimer;
	document.getElementById('messagecenter').innerHTML = 'System Message Center: ' + oReq.responseText;
	mctime = setTimeout(resetMessageCenter,5000);
}

function updateMenuCatsOrder(){
	var options = { 
					method: 'post',
					parameters: Sortable.serialize('menucats'),
					onComplete: getResponseUpdateMenuCatsOrder
				};
	new Ajax.Request('server-side/update_menu_cats_order.php', options);
}

function getResponseUpdateMenuCatsOrder(oReq){
	clearTimer;
	document.getElementById('messagecenter').innerHTML = 'System Message Center: ' + oReq.responseText;
	mctime = setTimeout(resetMessageCenter,5000);
}

function updatePriceColOrder(){
	var options = { 
					method: 'post',
					parameters: Sortable.serialize('priceCols'),
					onComplete: getResponseUpdatePriceColOrder
				};
	new Ajax.Request('server-side/update_price_col_order.php', options);
}

function getResponseUpdatePriceColOrder(oReq){
	clearTimer;
	document.getElementById('messagecenter').innerHTML = 'System Message Center: ' + oReq.responseText;
	mctime = setTimeout(resetMessageCenter,5000);
}
function ajaxUpdateAllPriceCol(allids){
	document.getElementById('processALL').disabled = true;
	var curText = document.getElementById('processALL').innerHTML;
	document.getElementById('processALL').disabled = true;
    document.getElementById('processALL').innerHTML = 'Please Wait...';
    var data = '';
    for(i=0;i<allids.length;i++){
        data = data+'id[]='+encodeURIComponent(allids[i])+'&name[]='+document.getElementById(allids[i]).value+'&';
  	}
	var options = { 
					method: 'post',
					parameters: data,
					onComplete: getResponseAjaxUpdateAllPriceCol
				};
	new Ajax.Request('server-side/ajax_price_col_all.php', options);
    function getResponseAjaxUpdateAllPriceCol(oReq){
        var theID = oReq.responseText;
        if(theID > 0){
            document.getElementById('messagecenter').innerHTML = 'System Message Center: Name Updated';
            mctime = setTimeout(resetMessageCenter,5000);
        }else{
            document.getElementById('messagecenter').innerHTML = 'System Message Center: Updated Failed';
            mctime = setTimeout(resetMessageCenter,5000);
        }
        document.getElementById('processALL').innerHTML = curText;
	    document.getElementById('processALL').disabled = false;
    }
    
}
function ajaxPriceCol(id){
	var passName = document.getElementById(id).value;
	var passID = encodeURIComponent(id);
	var options = { 
					method: 'post',
					parameters: 'id='+passID+'&name='+passName,
					onComplete: getResponseAjaxPriceCol
				};
	new Ajax.Request('server-side/ajax_price_col.php', options);
}
function getResponseAjaxPriceCol(oReq){
	var theID = oReq.responseText;
    if(theID > 0){
        document.getElementById('messagecenter').innerHTML = 'System Message Center: Name Updated';
		mctime = setTimeout(resetMessageCenter,5000);
    }else{
    	document.getElementById('messagecenter').innerHTML = 'System Message Center: Updated Failed';
		mctime = setTimeout(resetMessageCenter,5000);
    }
}
function resetMessageCenter(){
	document.getElementById('messagecenter').innerHTML = 'System Message Center: ';
}

function clearTimer(){
	clearTimeout(mctime);
}

function initF(){
	InitializeTimer();
}
function showLoading(theelement){
	document.getElementById(theelement).style.display = "none";
	document.getElementById('loadmessage').style.display = "block";
}
function imgSelect(imgSwitch){
	switch(imgSwitch){
		case '1':
			document.getElementById('img_var_holder').style.display = 'block';
		break;
		case '0':
			document.getElementById('img_var_holder').style.display = 'none';
		break;
	}
}
function printSelect(printSwitch){
	switch(printSwitch){
		case '1':
			document.getElementById('print_var_holder').style.display = 'block';
		break;
		case '0':
			document.getElementById('print_var_holder').style.display = 'none';
		break;
	}
}
function $m(theVar){
	return document.getElementById(theVar)
}
function remove(theVar){
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}
function removeEvent(obj, type, fn){
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}
function isWebKit(){
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}
function ajaxUpload(form,url_action,id_element,html_show_loading,html_error_http)
{
	var detectWebKit = isWebKit();
	form = typeof(form)=="string"?$m(form):form;
	var erro="";
	if(form==null || typeof(form)=="undefined"){
		erro += "The form of 1st parameter does not exists.\n";
	}else if(form.nodeName.toLowerCase()!="form"){
		erro += "The form of 1st parameter its not a form.\n";
	}
	if($m(id_element)==null){
		erro += "The element of 3rd parameter does not exists.\n";
	}
	if(erro.length>0){
		alert("Error in call ajaxUpload:\n" + erro);
		return;
	}
	var iframe = document.createElement("iframe");
	iframe.setAttribute("id","ajax-temp");
	iframe.setAttribute("name","ajax-temp");
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	form.parentNode.appendChild(iframe);
	window.frames['ajax-temp'].name="ajax-temp";
	var doUpload = function(){
		removeEvent($m('ajax-temp'),"load", doUpload);
		var cross = "javascript: ";
		cross += "window.parent.$m('"+id_element+"').innerHTML = document.body.innerHTML; void(0);";
		$m(id_element).innerHTML = html_error_http;
		$m('ajax-temp').src = cross;
		if(detectWebKit){
        	remove($m('ajax-temp'));
        }else{
        	setTimeout(function(){ remove($m('ajax-temp'))}, 250);
        }
    }
	addEvent($m('ajax-temp'),"load", doUpload);
	form.setAttribute("target","ajax-temp");
	form.setAttribute("action",url_action);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");
	form.submit();
	if(html_show_loading.length > 0){
		$m(id_element).innerHTML = html_show_loading;
	}
}
function deleteImage(data){
	var answer = confirm("Are you sure you want this deleted?\nThis cannot be undone.")
    if(answer){
        var aj = new Ajax.Request('server-side/deleteimages.php', {
            method:'post',
            parameters: data,
            onComplete: getResponseDeleteImage
        }
        );
	}else{
    	$('dlogo').checked = false;
    }
}
function getResponseDeleteImage(oReq){
	clearTimer;
    var PHPResponse = oReq.responseText;
	if(PHPResponse!=""){
    	var resposeParts = PHPResponse.split('||');
    	if(resposeParts[0]=='1'){
			$(resposeParts[1]).innerHTML = "";
    	    $('delcontainer').innerHTML = "Deleted Successfully!";
            setTimeout(clearDelCon,3000);
		}else{
			 $('delcontainer').innerHTML = "Could not delete image.";
              setTimeout(clearDelCon,3000);
		}
	}else{
    	$('delcontainer').innerHTML = "Could not delete image.";
         setTimeout(clearDelCon,3000);
    }
}
function clearDelCon(){
	$('delcontainer').innerHTML = "";
}