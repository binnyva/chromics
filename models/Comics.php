<?php
require_once(joinPath($GLOBALS['config']['iframe_folder'], 'includes/classes/ORM.php'));

class Comics extends DBTable {
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
	function create($name, $feed, $url, $description='', $type='linked', $fetch_regexp='', $title_match_regexp='', $update_frequency=1) {
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
		$this->field['update_frequency'] = $update_frequency;
		$this->field['added_on'] = 'NOW()';
		return $this->save();
	}
	
	/**
	 * You can edit an existing Comic using this function. The first argument 
	 * 		must be the id of the row to be edited
	 */
	function edit($id, $name, $feed, $url, $description=false, $type=false, $fetch_regexp=false, $title_match_regexp=false, $update_frequency=false) {
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
		if($update_frequency !== false) $this->field['update_frequency'] = $update_frequency;

		return $this->save();
	}
	
	/**
	 * Delete the Comic whose id is given
	 * Argument : $id	- The Id of the row to be deleted.
	 */
	function remove($id) {
		if(!$id) return -1;
		$this->newRow($id);
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
			showMessage("You have already subscribed to '$comic[name]'", $config['site_url'] . "comics/",'error');
		}
		
		return $sql->execQuery("INSERT INTO ComicTerm(comic_id, comic_name, term_id, user_id, added_on)"
			." VALUES('$comic_id', '" . addslashes($comic['name']) . "', 1, '$user_id', NOW())");
	}
	
	function unsubscribe($comic_id, $user_id=0) {
		global $sql;
		if(!$comic_id) return false;
		if(!$user_id) $user_id = $_SESSION['user_id'];
		
		return $sql->execQuery("DELETE FROM ComicTerm WHERE user_id=$user_id AND comic_id=$comic_id");
	}
}
$GLOBALS['Comic'] = new Comics;
 
/*
Controllor Constructor Code(JSON):
{"title":"Comic","class_name":"Comic","object_name":"$Comic","table":"Comic","name_single":"Comic","name_plural":"Comics","controller_name":"comic","model_file":"Comic.php","add_funcionality":"1","edit_funcionality":"1","delete_funcionality":"1","status_funcionality":"1","field_name_1":"id","field_auto_handle_1":"primary_key","field_title_1":"Id","field_type_1":"text","field_date_format_1":"","field_password_encrypt_1":"","field_password_salt_1":"","field_filetype_1":"","list_values_1":"","field_foreign_key_reference_1":"id","field_validation_1":["must","unique"],"field_name_2":"name","field_title_2":"Name","field_list_2":"1","field_type_2":"text","field_date_format_2":"","field_password_encrypt_2":"","field_password_salt_2":"","field_filetype_2":"","list_values_2":"","field_foreign_key_reference_2":"name","field_validation_2":["must"],"field_name_3":"feed","field_title_3":"Feed","field_list_3":"1","field_type_3":"text","field_date_format_3":"","field_password_encrypt_3":"","field_password_salt_3":"","field_filetype_3":"","list_values_3":"","field_foreign_key_reference_3":"feed","field_validation_3":["must"],"field_name_4":"url","field_title_4":"Site Url","field_list_4":"1","field_type_4":"text","field_date_format_4":"","field_password_encrypt_4":"","field_password_salt_4":"","field_filetype_4":"","list_values_4":"","field_foreign_key_reference_4":"url","field_validation_4":["must"],"field_name_5":"description","field_title_5":"Description","field_type_5":"textarea","field_date_format_5":"","field_password_encrypt_5":"","field_password_salt_5":"","field_filetype_5":"","list_values_5":"","field_foreign_key_reference_5":"description","field_name_6":"type","field_title_6":"Type","field_type_6":"list","field_date_format_6":"","field_password_encrypt_6":"","field_password_salt_6":"","field_filetype_6":"","list_values_6":"'embedded'=>'Embedded','linked'=>'Linked',","field_foreign_key_reference_6":"type","field_name_7":"fetch_regexp","field_title_7":"Fetch Regexp","field_type_7":"text","field_date_format_7":"","field_password_encrypt_7":"","field_password_salt_7":"","field_filetype_7":"","list_values_7":"","field_foreign_key_reference_7":"fetch.regexp","field_name_8":"title_match_regexp","field_title_8":"Title Match Regexp","field_type_8":"text","field_date_format_8":"","field_password_encrypt_8":"","field_password_salt_8":"","field_filetype_8":"","list_values_8":"","field_foreign_key_reference_8":"title.match.regexp","field_name_9":"last_downloaded_on","field_title_9":"Last Downloaded On","field_type_9":"date","field_date_format_9":"%d %b %Y, %h:%i %p","field_show_time_9":"1","field_password_encrypt_9":"","field_password_salt_9":"","field_filetype_9":"","list_values_9":"","field_foreign_key_reference_9":"last.downloaded.on","total_fields":"10","generate_files":["model.php","template\/_form.php","template\/edit.php","template\/index.php","template\/add.php","controller\/_form.php","controller\/edit.php","controller\/index.php","controller\/add.php","controller\/delete.php"],"action":"Create Code","error":"","success":""}
*/
