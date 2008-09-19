<?php
class Fetcher {
	
	function fetchComics() {
		global $sql;
		$show_details = true;
		
		$image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		$all_comics = $sql->getAll("SELECT id, name, feed, url,type, last_downloaded_on FROM Comic");
		$total_comics = count($all_comics);
		$comic_count = 1;
		
		foreach($all_comics as $feed) {
																		  // if($feed['id'] != 3) continue;
			if($show_details) print "$comic_count/$total_comics) $feed[name] ... ";
			$comic_count++;
			
			// Get the feed.
			if(!$feed['feed']) continue;
			$feed_details = load($feed['feed'],array(
					'return_info'	=>	true, 					 		  // 'cache'=>true,
					'modified_since'=> $feed['last_downloaded_on'],
				));
			$feed_contents = $feed_details['body'];
			$headers = $feed_details['headers'];
			$info = $feed_details['info'];
						
			if(!$feed_contents) {
				if($show_details) print "No new items\n";
				continue; //No content - means it have not been modified.
			}
			
			// Save last_modified to the db so that we don't have to download unnecessary stuff.
			if(isset($headers['Last-Modified'])) $last_modified = date('Y-m-d H:i:s', strtotime($headers['Last-Modified']));
			else $last_modified = date('Y-m-d H:i:s');
			$sql->execQuery("UPDATE Comic SET last_downloaded_on='$last_modified' WHERE id=$feed[id]");
			
			if($show_details) print "downloaded ... ";
			
			// Only RSS supported right now.
			$data = xml2array($feed_contents);
			if(!isset($data['rss']['channel']['item'])) {
				print "Cannot parse\n";
				continue;
			}
			
			$items = $data['rss']['channel']['item'];
			if(!isset($items[0])) $items = array($items); // Just 1 item in the feed. This is a ugly workaround for that.
			

			
			// We use a different query to get the regexps - we don't want it to be stripslashed.
			$regexps = $sql->getAssoc("SELECT title_match_regexp, fetch_regexp FROM Comic WHERE id=$feed[id]", array('strip_slashes'=>false));
			$feed['title_match_regexp'] = $this->escapeRegExpChars($regexps['title_match_regexp']);
			$feed['fetch_regexp'] = $this->escapeRegExpChars($regexps['fetch_regexp']);
			
			// Get the GUID and Image URL of all the latest strips in this comic. We can use this array to make sure that duplicates are not included.
			$last_strip = count($items) - 1;
			$last_time = $this->getMysqlTime(i($items[$last_strip],'pubDate'));
			list($guids_of_latest_strips, $image_url_of_latest_strips) = $this->getStripList($feed['id'], $last_time);
			
			// Go thru all the posts in the feed and find the necessary details for the strip.
			foreach($items as $strip) {
				if($feed['title_match_regexp'] and !preg_match("$feed[title_match_regexp]", $strip['title'])) {
					continue; // Make sure that this feed item is a comic - some comics have content and comic in the same feed - but they usually have a word in the title like 'Comic' to specify that its a comic.
				}
				
				if(isset($strip['guid']) and $strip['guid']) { // Make sure we dont have this comic already.
					if(in_array($strip['guid'], $guids_of_latest_strips)) {
						if($show_details) print "Done\n";
						continue 2; // Go to the next comic(not next strip).
					}
				} else {
					$strip['guid'] = '';
				}
				
				$image_url = ''; //The comic image url.
				$contents = '';
				
				if(i($strip, 'content:encoded')) $contents = i($strip, 'content:encoded');
				elseif(i($strip, 'content')) $contents = i($strip, 'content');
				else $contents = i($strip, 'description');
				if(is_array($contents)) $contents = implode('', $contents); // Sometimes this happens.
				
				if($feed['type'] == 'embedded') {
					$image_url = $this->findFirstImage($contents);
				}
				
				if(!$image_url and isset($strip['link']) and $strip['link']) { // Most likely the image is available only on the site - not in the feed.
					// In some comics, the link is a direct link to the image.
					$ext_arr = split("\.",basename($strip['link']));
					$extension = '';
					if(count($ext_arr) == 2) $extension = $ext_arr[1];
					if(in_array($extension, $image_extensions)) { //Yes, its an image extension.
						$image_url = $strip['link'];
					
					} else {
						$strip_contents = load($strip['link']				/* * /, array('cache'=>true) /**/ );
						$image_url = $this->findComicImage($strip_contents, $feed['fetch_regexp']);
					}
				}
				
				if($image_url) {
					if(strpos($image_url, 'http://') !== 0) $image_url = joinPath($feed['url'], $image_url); //Its a relative path. Make it absolute.
				} else {
					if(isset($strip['link'])) print " NO IMAGE FOUND FOR $strip[link]\n";
					elseif(isset($strip['title'])) print " NO IMAGE FOR $strip[title]\n";
					else print " NO IMAGE FOUND\n";
					continue;
				}
				
				if(!isset($strip['pubDate']) or !$strip['pubDate']) {
					$strip['pubDate'] = $time = date('Y-m-d H:i:s');
				}
				else $time = date('Y-m-d H:i:s', strtotime(i($strip,'pubDate')));
				
				if($time < $last_time) { // The posts are not in order - get all the strips of this comic - not just the lastest.
					list($guids_of_latest_strips, $image_url_of_latest_strips, $last_time) = $this->getStripList($feed['id']);
				}
				
				$image_url = trim($image_url); // REALLY?!
				$already_have = in_array($image_url, $image_url_of_latest_strips); // Make sure that the strip is not duplicated.
				
				if(!$already_have) {
					if($show_details) print " Inserting $strip[title] ($image_url)\n";
					$title = i($strip,'title');
					if(is_array($title)) $title = implode('', $title);
					if(!isset($strip['link'])) $strip['link'] = '';
					if(!$title) $title = 'Comic for ' . date('jS M, Y', strtotime($time));
					
					$sql->execQuery("INSERT INTO Strip(name, image_url, url, contents, guid, added_on, comic_id) "
						. " VALUES('" . mysql_real_escape_string($title) . "',"
						. "'" . mysql_real_escape_string($image_url) . "'," 
						. "'" . mysql_real_escape_string($strip['link']) . "'," 
						. "'" . mysql_real_escape_string($contents) . "'," 
						. "'" . mysql_real_escape_string($strip['guid']) . "','$time','$feed[id]')");
					$image_url_of_latest_strips[] = $image_url;
					if($strip['guid']) $guids_of_latest_strips[] = $strip['guid'];
				} else {
					if($show_details) print "Done\n";
					continue 2; // We already have this comic - so we must have the stuff that came before it. So skip to te next comic - not just the next strip.
				}
			}
		}
	}

	/// If HTML is given as the argument, this function will find the URL of the image thats most likely to be the comic.
	function findComicImage($html, $regexp) {
		$images = $this->findAllImages($html);
		
		if($regexp) {
			$comic_regexps = array($regexp); // If there is a specific regexp for this comic, use that - 
		} else {
			$comic_regexps = array( //Else use a generic regexp.
				'/\/(comics?)?[\-\_\d]+\.(jpg|jpeg|png|gif)$/',
				'/\/[a-zA-Z\-\_]+[\-\_\d]{2,}\.(jpg|jpeg|png|gif)$/',
				'/\/[\-\_\d]{2,}[a-zA-Z\-\_]+\.(jpg|jpeg|png|gif)$/',
				'/\/comics?\/.+\.(jpg|jpeg|png|gif)$/', // image must be in a folder called 'comic' or 'comics' - Urls like 'http://www.giantitp.com/comics/images/3394XxpGeP1I1Y6NiI0.jpg'
			);
		}
		foreach($comic_regexps as $re) { // Go thru each regular expression
			foreach($images as $img) { //and thru every image
				$url = $img->getAttribute("src");
				if(preg_match($re, $url)) return $url; // And see if the image url matches the regexp.
			}
		}
		return '';   
	}
	
	/// Finds the first image url in the given html.
	function findFirstImage($html) {
		$images = $this->findAllImages($html);
		foreach($images as $img) {
			return $img->getAttribute("src");//Return the first image. $images[0]->getAttribute() will NOT work.
		}
		return '';
	}
	
	/// If the HTML is given, it will return all the img tags.
	function findAllImages($html) {
		error_reporting(0); // Suppress the invalid HTML warnings
		$dom = new domDocument;
		$dom->loadHTML($html);
		error_reporting(E_ALL);
		$dom->preserveWhiteSpace = false;
		return $dom->getElementsByTagName('img');
	}
	
	function getStripList($id, $last_time='') {
		global $sql;
		if($last_time) $query_end = " AND added_on>='$last_time'";
		else $query_end = " ORDER BY added_on DESC";
		
		$latest_strips = $sql->getAll("SELECT guid,image_url,added_on FROM Strip WHERE comic_id=$id $query_end");
		
		$guids_of_latest_strips = array();
		$image_url_of_latest_strips = array();
		foreach($latest_strips as $latest) {
			$guids_of_latest_strips[] = $latest['guid'];
			$image_url_of_latest_strips[] = $latest['image_url'];
		}
		// Get the time of the last comic strip.
		if(!$last_time) $last_time = $latest_strips[0]['added_on']; 
		
		return array($guids_of_latest_strips, $image_url_of_latest_strips, $last_time);
	}
	
	/// Escape the regexp chars if the not a regexp(not delimited by '/'s...
	function escapeRegExpChars($regexp) {
		if($regexp and ($regexp[0] != '/' or $regexp[strlen($regexp)-1] != '/')) {
			$regexp = '/' . preg_replace('/([\.\:\\/\?\&])/', '\\\$1', $regexp) . '/';
		}
		return $regexp;
	}
	
	function getMysqlTime($time) {
		if(!$time) return '2008-01-01 01:01:01';
		return date('Y-m-d H:i:s', strtotime($time)-1);
	}
}

$GLOBALS['Fetcher'] = new Fetcher;