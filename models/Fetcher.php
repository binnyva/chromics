<?php
class Fetcher {
	
	function fetchComics() {
		global $sql;
		$all_comics = $sql->getAll("SELECT id, name, feed, url,type, fetch_regexp, title_match_regexp,last_downloaded_on FROM Comic");
		
		foreach($all_comics as $feed) {
			// Get the feed.
			$feed_details = load($feed['feed'],array(
					'return_info'	=>	true, 					 		//'cache'=>true,
					'modified_since'=> $feed['last_downloaded_on'],
				));
			$feed_contents = $feed_details['body'];
			$headers = $feed_details['headers'];
			$info = $feed_details['info'];
			
			if(!$feed_contents) continue; //No content - means it have not been modified.
			
			// Save last_modified to the db so that we don't have to download unnecessary stuff.
			if(isset($headers['Last-Modified'])) $last_modified = date('Y-m-d H:i:s', strtotime($headers['Last-Modified']));
			else $last_modified = date('Y-m-d H:i:s');
			$sql->execQuery("UPDATE Comic SET last_downloaded_on='$last_modified'");
			
			print "\n$feed[name]\n";
			
			// Only RSS supported right now.
			$data = xml2array($feed_contents);
			$items = $data['rss']['channel']['item'];
			if(!isset($items[0])) $items = array($items); // Just 1 item in the feed. This is a ugly workaround for that.
			
			// Get the GUID and Image URL of all the latest strips in this comic. We can use this array to make sure that duplicates are not included.
			$last_strip = count($items) - 1;
			$last_time = $this->getMysqlTime($items[$last_strip]['pubDate']);
			list($guids_of_latest_strips, $image_url_of_latest_strips) = $this->getStripList($feed['id'], $last_time);
			
			// Go thru all the posts in the feed and find the necessary details for the strip.
			foreach($items as $strip) {
				if($feed['title_match_regexp'] and !preg_match("/$feed[title_match_regexp]/", $strip['title'])) continue; // Make sure that this feed item is a comic - some comics have content and comic in the same feed - but they usually have a word in the title like 'Comic' to specify that its a comic.
				
				if(isset($strip['guid']) and $strip['guid']) { // Make sure we dont have this comic already.
					if(in_array($strip['guid'], $guids_of_latest_strips)) continue; // Go to the next comic.
				} else {
					$strip['guid'] = $strip['link'];
				}
				
				$image_url = ''; //The comic image url.
				$contents = '';
				
				if(i($strip, 'content:encoded')) $contents = i($strip, 'content:encoded');
				elseif(i($strip, 'content')) $contents = i($strip, 'content');
				else $contents = i($strip, 'description');
				
				if($feed['type'] == 'embedded') {
					$image_url = $this->findFirstImage($contents);
				}
				
				if(!$image_url) { // Most likely the image is available only on the site - not in the feed.
					$strip_contents = load($strip['link']);
					$image_url = $this->findComicImage($strip_contents);
				}
				
				if($image_url) {
					if(strpos($image_url, 'http://') !== 0) $image_url = joinPath($feed['url'], $image_url); //Its a relative path. Make it absolute.
				} else {
					print "NO IMAGE FOUND FOR $strip[link]\n";
					continue;
				}
				
				$time = date('Y-m-d H:i:s', strtotime($strip['pubDate']));
				if($time < $last_time) { // The posts are not in order - get all the strips of this comic - not just the lastest.
					list($guids_of_latest_strips, $image_url_of_latest_strips, $last_time) = $this->getStripList($feed['id']);
				}
				
				$image_url = trim($image_url); // REALLY?!
				$already_have = in_array($image_url, $image_url_of_latest_strips); // Make sure that the strip is not duplicated.
				if(!$already_have) {
					print "Inserting $strip[title] ($image_url)\n";
					
					$sql->execQuery("INSERT INTO Strip(name, image_url, url, contents, guid, added_on, comic_id) "
						. " VALUES('" . mysql_real_escape_string($strip['title']) . "',"
						. "'" . mysql_real_escape_string($image_url) . "'," 
						. "'" . mysql_real_escape_string($strip['link']) . "'," 
						. "'" . mysql_real_escape_string($contents) . "'," 
						. "'" . mysql_real_escape_string($strip['guid']) . "','$time','$feed[id]')");
				}
			}
		}
	}

	/// If HTML is given as the argument, this function will find the URL of the image thats most likely to be the comic.
	function findComicImage($html) {
		$images = $this->findAllImages($html);
		
		foreach($images as $img) {
			$url = $img->getAttribute("src"); 
			if(preg_match('/\/(comics?)?[\-\_\d]+.(jpg|jpeg|png|gif)$/', $url)) {
				return $url;
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
		$query_end = '';
		if($last_time) $query_end = " AND added_on>='$last_time'";
		else $query_end = " ORDER BY added_on";
		
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
	
	function getMysqlTime($time) {
		return date('Y-m-d H:i:s', strtotime($time)-1);
	}
}

$GLOBALS['Fetcher'] = new Fetcher;