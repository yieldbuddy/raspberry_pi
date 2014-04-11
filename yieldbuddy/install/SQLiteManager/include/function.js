function setCheckBox(form, name, value){
	elts = document.forms[form].elements;
	if(typeof(elts)!='undefined'){
		for(var i=0 ; i < elts.length ; i++) {
			if(elts[i].name.indexOf(name)!=-1) elts[i].checked = value; 
		}
	}
}

function cleanNullField(form, name){
	elts = document.forms[form].elements;
	if(typeof(elts)!='undefined'){
		for(var i=0 ; i < elts.length ; i++) {
			if(elts[i].name.indexOf(name)!=-1) elts[i].value = ''; 
		}
	}
}

function setTableAction(form, index, action){
	document.forms[form].elements['modify['+index+']'].checked=true;
	document.forms[form].action.value=action;
	document.forms[form].submit();
}

function afficheCalque(calque){
	if (document.getElementById){
    	document.getElementById(calque).style.visibility="visible";
	} else {
		eval(layerRef + '["' + calque +'"]' + styleRef + '.visibility = "visible"');
	}
}

function cacheCalque(calque){
	if (document.getElementById){
		document.getElementById(calque).style.visibility="hidden";
	} else {
		eval(layerRef + '["' + calque +'"]' + styleRef + '.visibility = "hidden"');
	}
}

function ftype(){
	if(document.functprop.FunctType.selectedIndex==0){
		cacheCalque('Pfinal1');
		cacheCalque('Pfinal2');
	} else {
		afficheCalque('Pfinal1');
		afficheCalque('Pfinal2');
	}
}

function checkPath()
{
	if(document.database.dbRealpath.value)
	{
		document.database.dbpath.value = document.database.dbRealpath.value.replace("\\", "/");
		document.database.dbRealpath.value = '';
	}
}

var tabRow = new Array;
function setRowColor(RowObj, numRow, Action, OrigColor, OverColor, ClickColor, bUseClassName){
    if (typeof(document.getElementsByTagName) != 'undefined') TheCells = RowObj.getElementsByTagName('td');
    else return false;
    if(!in_array(numRow, tabRow)){
		if(Action=='over') setColor = OverColor;
		else if(Action == 'out') setColor = OrigColor;
		else if(Action == 'click') {
			setColor = ClickColor;
			tabRow.push(numRow);
		}		
	} else if(Action == 'click'){
		tabIndex = in_array(numRow, tabRow);
		if(tabIndex>0) {
			tabRow[(tabIndex-1)] = '';
			setColor = OrigColor;
		}
	} else return;
	for(i=0 ; i<TheCells.length ; i++)
    if (bUseClassName) {
      if (bUseClassName && TheCells[i].className != setColor) 
        TheCells[i].className = setColor; 
    } else
      if (TheCells[i].style.backgroundColor != setColor)
        TheCells[i].style.backgroundColor = setColor; 
	return;
}

function in_array(needle, haystack){
	for(i=0 ; i<haystack.length ; i++) 
		if(haystack[i] == needle) return (i+1);
	return false;
}

function insertColumn(){
	sourceSel = document.sql.columnTable;
	destSQL = document.sql.DisplayQuery;
	var i=sourceSel.options.length;
	var first = true;
	var stringToDisplay='';
	while(i >= 0){
		if(sourceSel.options[i] && sourceSel.options[i].selected){
			if(first) {
				stringOut = '';
				first = false;
			} else {
				stringOut = ', ';			
			}
			stringToDisplay += stringOut+sourceSel.options[i].value;
			sourceSel.options[i].selected = false;
		}	
		i--;		
	}
	if(document.selection){
		destSQL.focus();
		selection = document.selection.createRange();
		if (selection.findText('*'))
		  selection.text = stringToDisplay;
		else if (selection.findText(' FROM'))
		  selection.text = ', '+stringToDisplay+' FROM';
    else
      selection.text = stringToDisplay;
    selection.empty();
 		document.sql.insertButton.focus();
	} else if(destSQL.selectionStart || destSQL.selectionStart == '0'){
		destSQL.value = destSQL.value.substring(0, destSQL.selectionStart)
						+ stringToDisplay
						+ destSQL.value.substring(destSQL.selectionEnd, destSQL.value.length);
	} else {
		destSQL += stringToDisplay;
	}
}
