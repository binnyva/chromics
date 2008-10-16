active_strip = 0; //The currently active strip

function init() {
	$(window).on("scroll", monitor);
}

function monitor() {
	var scroll_position = window.pageYOffset || document.body.scrollTop; //Get the scrolled position
	var biggest_owner = 0;
	var biggest_ownership = 0;
	
	$(".strip").each(function(el,i) {
		var ele = $(el);
		var id = ele.id.toString().replace(/[^\d]+/g,"");
		if(active_strip != id) {
			var strip_position = ele.getPosition()['y'];
			var elements_screen_ownership = 0;
			
			// There should be a single formula to calculate this.
			var strip_starts_before_window_starts=strip_position < scroll_position;
			var strip_starts_after_window_starts= strip_position > scroll_position;
			var strip_starts_before_window_ends	= strip_position < scroll_position + window.innerHeight;
			var strip_ends_after_window_starts	= strip_position + ele.clientHeight > scroll_position;
			var strip_ends_before_window_ends	= strip_position + ele.clientHeight < scroll_position + window.innerHeight;
			var strip_ends_after_window_ends	= strip_position + ele.clientHeight > scroll_position + window.innerHeight;
			
			// 6 Cases
			// 1st Case - The strip begins and ends before the window. Don't worry about this.
			
			// 2nd Case - The strip begin before the window and ends in the window.
			if(strip_starts_before_window_starts && strip_ends_after_window_starts && strip_ends_before_window_ends) {
				elements_screen_ownership = strip_position + ele.clientHeight - scroll_position;
			}
			
			// 3rd Case - The strip begins and ends inside the window.
			else if(strip_starts_after_window_starts && strip_ends_before_window_ends) {
				elements_screen_ownership = ele.clientHeight;
			}
			
			// 4th Case - Strip begins in the window but ends after the window ends
			else if(strip_starts_after_window_starts && strip_starts_before_window_ends && strip_ends_after_window_ends) {
				elements_screen_ownership = (window.innerHeight + scroll_position) - strip_position;
			}
			
			// 5th Case - strip begins and ends after the window ends. No need to worry about this.
			
			// 6th Case - Strip begins before the window and ends after it.
			else if(strip_starts_before_window_starts && strip_ends_after_window_ends) {
				elements_screen_ownership = window.innerHeight;
			}
			
			
			if(elements_screen_ownership == ele.clientHeight) return activateStrip(id, ele); // The strip is in the window.
			else if(elements_screen_ownership > (window.innerHeight*2/3)) return activateStrip(id, ele); //If a strip has the majority
		}
	});
}

// Set the given strip as the currently active strip. Also make other active strips inactive and gives the current one the 'read' status.
function activateStrip(id, ele) {
	if(active_strip == id) return false;
	
	active_strip = id;
	$(".active").each(function(old_active) { //Mark the last active comic as read.
		old_active.removeClass("active");
		old_active.addClass("read");
	});
	ele.addClass("active");
	if(ele.hasClass("read")) return true; //It already has read status - we dont have to set it manually.
	
	JSL.ajax("strip/read.php?action=mark_as_read&strip="+id).bind({
		"onSuccess": function(data) {
			ele.removeClass("unread");
		},
		"onError": ajaxError,
		format: "json",
		loading_indicator: "loading"
	});
	return true; //Returning something will exit the each loop
}