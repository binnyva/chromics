function init() {	
	$("feed").value = "";

	$("fetch_details").click(function() {
		var url = $("feed").value;
		JSL.ajax("fetch_details.php?feed="+escape(url)).load(function(json) {
			if(json.error) {
				alert(json.error);
				return;
			}
			var data = json.success;
			$("fetch_details").hide();
			$("details").show();
			
			$("description").value = data.description;
			$("name").value = data.name;
			$("url").value = data.url;
			$("type").value = data.type;
			
		},'json');
	});
}