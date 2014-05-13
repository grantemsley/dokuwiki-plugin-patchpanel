function patchpanel_create_tooltip_div() {
	var tooltip = document.getElementById("patchpanel_tooltip");
	if (!tooltip) {
		tooltip = document.createElement('div');
		tooltip.setAttribute("id", "patchpanel_tooltip");
		document.body.appendChild(tooltip);
	}
}