<?php
/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/*******
 * Note: the many options for these variables are used to decrease confusion.
 *       TRUE | FALSE | 1 will be the convention.
 *
 * $_course_privilege
 *     specifies the type of instructor privilege this module uses.
 *     set to empty | FALSE | 0   to disable any privileges.
 *     set to 1 | AT_PRIV_ADMIN   to use the instructor only privilege.
 *     set to TRUE | 'new'        to create a privilege specifically for this module:
 *                                will make this module available as a student privilege.
 *
 * $_admin_privilege
 *    specifies the type of ATutor administrator privilege this module uses.
 *    set to FALSE | AT_ADMIN_PRIV_ADMIN   to use the super administrator only privilege.
 *    set to TRUE | 'new'                  to create a privilege specifically for this module:
 *                                         will make this module available as an administrator privilege.
 *
 *
 * $_cron_interval
 *    if non-zero specifies in minutes how often the module's cron job should be run.
 *    set to 0 or not set to disable.
 */
$_admin_privilege  = TRUE; // possible values: FALSE | TRUE


// check if the directory is writeable

if (defined('IS_SUBSITE') && IS_SUBSITE) {
	$msg->addError(array('MODULE_INSTALL', '<li>This module cannot be installed on subsites.</li>'));
}


/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run any database queries it needs, ie. to create
 * its own tables.
 */
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility = new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>