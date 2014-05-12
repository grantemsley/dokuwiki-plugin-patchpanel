// 20090608.0: function renamed with rack_ prefix to avoid collision with a dokuwiki builtin
function rack_getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\\\s)"+searchClass+"(\\\\s|\$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}
function rack_ie6fix() {
	/* IE can't do "display:inline-table", but "inline" works, so we fix this client-side */
	//alert(navigator.userAgent);
	if(/MSIE/.test(navigator.userAgent)) {
		var tables = rack_getElementsByClass('rack');
		for (var i=0; i<tables.length; i++) {
			//alert(i);
			tables[i].style.display = "inline";
		}
	}
}

function rack_toggle_vis(element,vis_mode) {
	element.style.display = rack_toggle(element.style.display,"none",vis_mode);
	return element.style.display!="none";
}

function rack_toggle(v,a,b) {
	return (v==a)?b:a;
}