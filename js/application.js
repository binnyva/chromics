//Framework Specific
function showMessage(data) {
	if(data.success) $("success-message").innerHTML = stripSlashes(data.success);
	if(data.error) $("error-message").innerHTML = stripSlashes(data.error);
}
function stripSlashes(text) {
	if(!text) return "";
	return text.replace(/\\([\'\"])/,"$1");
}
function siteInit() {

	$("a.confirm").click(function(e) { //If a link has a confirm class, confrm the action
		var action = (this.title) ? this.title : "do this";
		action = action.substr(0,1).toLowerCase() + action.substr(1); //Lowercase the first char.
		
		if(!confirm("Are you sure you want to " + action + "?")) JSL.event(e).stop();
	});

	if(window.init && typeof window.init == "function") init(); //If there is a function called init(), call it on load
}
$(window).load(siteInit);