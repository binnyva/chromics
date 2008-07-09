<?php
require_once(joinPath($GLOBALS['config']['iframe_folder'], 'includes/classes/ORM.php'));

class Comic extends DBTable {
	/**
	 * Constructor
	 * Arguments : None
	 */
	function __construct() {
		parent::__construct("Comic");
	}

	/**
	 * This will create a new Comic and returns the id of the newly created row.
	 */
	function create($name, $feed, $url, $description='', $type='linked', $fetch_regexp='', $title_match_regexp='') {
		$validation_rules = $this->getValidationRules();
		$validation_errors = check($validation_rules,2);
		if($validation_errors) {
			$GLOBALS['QUERY']['error'] =  "Please correct the errors before continuing...<br />" . $validation_errors;
			return false;
		}
		
		$this->newRow();
		$this->field['name'] = $name;
		$this->field['feed'] = $feed;
		$this->field['url'] = $url;
		$this->field['description'] = $description;
		$this->field['type'] = $type;
		$this->field['fetch_regexp'] = $fetch_regexp;
		$this->field['title_match_regexp'] = $title_match_regexp;
		return $this->save();
	}
	
	/**
	 * You can edit an existing Comic using this function. The first argument 
	 * 		must be the id of the row to be edited
	 */
	function edit($id, $name, $feed, $url, $description=false, $type=false, $fetch_regexp=false, $title_match_regexp=false) {
		if(!$id) return -1;
		
		$validation_errors = check($this->getValidationRules(),2);
		if($validation_errors) {
			$GLOBALS['QUERY']['error'] =  "Please correct the errors before continuing...<br />" . $validation_errors;
			return false;
		}
		
		$this->newRow($id);
		$this->field['name'] = $name;
		$this->field['feed'] = $feed;
		$this->field['url'] = $url;
		if($description !== false) $this->field['description'] = $description;
		if($type !== false) $this->field['type'] = $type;
		if($fetch_regexp !== false) $this->field['fetch_regexp'] = $fetch_regexp;
		if($title_match_regexp !== false) $this->field['title_match_regexp'] = $title_match_regexp;

		return $this->save();
	}
	
	/**
	 * Delete the Comic whose id is given
	 * Argument : $id	- The Id of the row to be deleted.
	 */
	function remove($id) {
		if(!$id) return -1;
		
		$this->where("id=$id");
		$this->delete();
	}
	
	/**
	 * Checks to make sure that there is no other row with the same value in the specified name.
	 * Example: Comic.checkDuplicate("username", "binnyva", 4);
	 * 			Comic.checkDuplicate("email", "binnyva@email.com");
	 */
	function checkDuplicate($field, $value, $not_id=0) {
		global $config;
		//See if an item with that name is already there.
		$others = $this->find(array(
				"select"	=> 'id',
				'where'		=> array("$field='$value'", "id!=$not_id")));
		if($others) {
			showMessage("Comic '$new_name' already exists!",$config['site_url'] . "index.php",'error');
		}
		return false;
	}
	
	function getValidationRules() {
		return array(
			array('name'=>'name', 'is'=>'empty', 'error'=>'The Name cannot be empty'),
			array('name'=>'feed', 'is'=>'empty', 'error'=>'The Feed cannot be empty'),
			array('name'=>'url', 'is'=>'empty', 'error'=>'The Url cannot be empty'),
		);
	}
	
	
	function subscribe($comic_id, $user_id=0) {
		global $sql, $config;
		if(!$user_id) $user_id = $_SESSION['user_id'];
		$comic = $this->select('name')->find($comic_id);
		
		// Make sure that the user is not already subscribed.
		if($sql->getOne("SELECT term_id FROM ComicTerm WHERE comic_id='$comic_id' AND user_id='$user_id' AND term_id=1")) {
			showMessage("You have already subscribed to '$comic[name]'", $config['site_url'] . "directory.php",'error');
		}
		
		return $sql->execQuery("INSERT INTO ComicTerm(comic_id, comic_name, term_id, user_id) VALUES('$comic_id', '$comic[name]', 1, '$user_id')");
	}
}
$GLOBALS['Comic'] = new Comic;
 
