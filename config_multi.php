<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MANAGE_MULTI);

include(AT_INCLUDE_PATH . 'install/install.inc.php');
include(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
include('include/config_multisite_template.php');

if ($_POST['submit']){
	global $msg;
	if (!file_exists(AT_MULTISITE_CONFIG_FILE)) {
		$msg->addError(array('CONFIG_FILE_NOT_EXIST', AT_MULTISITE_CONFIG_FILE));
	} else if (!is_writeable(AT_MULTISITE_CONFIG_FILE)) {
		$msg->addError(array('FILE_NOT_WRITABLE', AT_MULTISITE_CONFIG_FILE));
	}
	
	if (!$msg->containsErrors()) {
		// create database to manage multisite
		$db = create_and_switch_db($_POST['db_host'], $_POST['db_port'], $_POST['db_login'], $_POST['db_password'], $_POST['tb_prefix'], $_POST['db_name']);
		
		$sqlUtility = new SqlUtility();
		$sqlUtility->queryFromFile('include/atutor_multisite_schema.sql', $addslashes($_POST['tb_prefix']), false);
		
		// switch to the main database
		@mysql_select_db(DB_NAME, $db);
	
		// database is created successfully
		if (!$msg->containsErrors()) {
			// write config file for managing multisite
			$comments = '/*'.str_pad(' This file was generated by the ATutor '.VERSION. ' managing multi-site script.', 70, ' ').'*/' . "\n" .
			            '/*'.str_pad(' File generated '.date('Y-m-d H:m:s'), 70, ' ').'*/';
			
			if (!write_multisite_config_file(AT_MULTISITE_CONFIG_FILE, $_POST['db_login'], $_POST['db_password'], 
			    $_POST['db_host'], $_POST['db_port'], $_POST['db_name'], $_POST['tb_prefix'], $comments)) {
				$msg->addError(array('FILE_NOT_WRITABLE', AT_MULTISITE_CONFIG_FILE));
			}
			
			@chmod(AT_MULTISITE_CONFIG_FILE, 0444);
			
			$msg->addFeedback(array('FILE_CREATED', AT_MULTISITE_CONFIG_FILE));
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}
} else if (file_exists(AT_MULTISITE_CONFIG_FILE)) {
	
	include(AT_MULTISITE_CONFIG_FILE);
	if (defined('DB_NAME_MULTISITE')) {
		$msg->addWarning(array('MANAGE_MULTISITE_DB_EXISTS', DB_NAME_MULTISITE));
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

?>

<div class="input-form">
<h2>Setup Subsite Database</h2>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  name="setup_multi" method="post" class="form_input">
	<div class="row">
		<p><?php echo _AT('config_multi_desc'); ?></p><br />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="db"><?php echo _AT(db_host);?></label><br />
		<input type="text" name="db_host" id="db" value="<?php if (!empty($_POST['db_host'])) { echo stripslashes(htmlspecialchars($_POST['db_host'])); } else { echo DB_HOST; } ?>" class="formfield" /><br />
		<small><?php echo _AT(db_host_notes);?></small>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="db"><?php echo _AT(db_port);?></label><br />
		<input type="text" name="db_port" id="port" value="<?php if (!empty($_POST['db_port'])) { echo stripslashes(htmlspecialchars($_POST['db_port'])); } else { echo DB_PORT; } ?>" class="formfield" /><br />
		<small><?php echo _AT(db_port_notes);?></small>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="db"><?php echo _AT(db_user);?></label><br />
		<input type="text" name="db_login" id="username" value="<?php echo stripslashes(htmlspecialchars($_POST['db_login'])); ?>" class="formfield" /><br />
		<small><?php echo _AT(db_user_notes);?></small>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="db"><?php echo _AT(db_pwd);?></label><br />
		<input type="text" name="db_password" id="pass" value="<?php echo stripslashes(htmlspecialchars($_POST['db_password'])); ?>" class="formfield" /><br />
		<small><?php echo _AT(db_pwd_notes);?></small>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="db"><?php echo _AT(db_name);?></label><br />
		<input type="text" name="db_name" id="name" value="<?php if (!empty($_POST['db_name'])) { echo stripslashes(htmlspecialchars($_POST['db_name'])); } else { echo 'ATutor_manage_multisite'; } ?>" class="formfield" /><br />
		<small><?php echo _AT(db_name_notes);?></small>
	</div>

	<div class="row">
		<label for="db"><?php echo _AT(tb_prefix);?></label><br />
		<input type="text" name="tb_prefix" id="prefix" value="<?php if (!empty($_POST['tb_prefix'])) { echo stripslashes(htmlspecialchars($_POST['tb_prefix'])); } else { echo TABLE_PREFIX; } ?>" class="formfield" /><br />
		<small><?php echo _AT(tb_prefix_notes);?></small>
	</div>

	<input type="submit" name="submit" />
</form>
</div>


<?php
require (AT_INCLUDE_PATH.'footer.inc.php');
?>