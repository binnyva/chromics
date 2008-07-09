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
	if(window.init && typeof window.init == "function") init(); //If there is a function called init(), call it on load
}
window.onload=siteInit;