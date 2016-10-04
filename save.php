<?php

######################################################################################################################
#
#	PURPOSE OF THIS FILE:
#	This file reads the output text specified by the user (formular in modify.php). The user input is filterd to avoid
#	security vulnerabilities like: SQL-injection, XSS, parsing of code, defacing of layout...
#	Once the input is filtered, it is written to the module DB-table. Depending on the result, a status message is 
#	shonw. If no database error occured, the user is redirected to the modify.php file.
#
#	INVOKED BY:
# 	This file is invoked when a user clicks on the Save button in the module page backend (modify.php)
#	Pages -> Page of your module type -> Click on Page Title link -> Save
#
#	LIST OF VARIABLES AND FUNCTIONS USED IN THIS FILE:
#		CONSTANTS AND VARIABLES USED:
#		WB_PATH:					path to the WB root directory (set during installation; stored in config.php)
#		ADMIN_URL:				URL to the WB backend (set during installation; stored in config.php)
#		TABLE_PREFIX:			optional prefix for all database tables defined during Installation (stored in config.php)
#		$database:				provides database functions (instance of class database defined in framework/class.database.php)
#		$page_id:				ID of the current displayed page (automatically set by Website Baker)
#		$section_id:			ID of the currents page section (automatically set by Website Baker)
#		$sql_query:				string for the MySQL database query
#		$simple_output:		contains the filterd text string which will be written to the database
#		$update_when_modified:	tells the admin script to add the update information
#
#		VARIABLES DEFINED IN LANGUAGE FILES:
#		$MESSAGE['PAGES']['SAVED']:	status messsage (defined in /languages/XX.php)
#
#		FUNCTIONS USED:
#		$database->query():			function to create a database query
#		$database->is_error():		function to create a database query
#		$database->get_error():		function to create a database query
#		$admin->print_error():		function to create a database query
#		$admin->print_success():	function to create a database query
#
#		PLEASE NOTE: 
#		Module tables have to stick to the following naming convention: TABLE_PREFIX_mod_MODULE_DIRECTORY 
#		Don´t use $module_directory! Use the string defined via $module_directory in info.php instead (here: helloworld)
#
######################################################################################################################

/**
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) year, Authors name
  Contact me: author(at)domain.xxx, http://authorwebsite.xxx

  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
  GNU General Public License for more details.
**/

// manually include the config.php file (defines the required constants)
require('../../config.php');

/**
*	INCLUDE THE WB-ADMIN WRAPPER SCRIPT 
*	The admin wrapper script provides functions to add the look & feel of WB-Backend pages
*	to the save.php file (backend header, last modified, backend footer...).
*	The admin wrapper also takes care about the users permissions to view and change the files.
*/
// tell the admin wrapper to actualize the DB settings when this page was last updated
$update_when_modified = true;
// include the admin wrapper script (includes framework/class.admin.php)
require(WB_PATH.'/modules/admin.php');

/**
*	GET DATA SEND BY THE FORM (POST METHOD)
*	This part is very important to prevent security vulnerabilities like cross-site scripting (XSS),
*	SQL-Injection or PHP security issues.
*
*	KEEP IN MIND:
*	All user inputs should be treated as dangerous. You need to apply the required measures to
*	prevent that harmful code is added to the MySQL database or executed by PHP. 
*	Always think about filtering HTML/CSS/Javascript code from the user input. 
*
*	Some aspects on security:
*	Tags like <strong> can be used to deface your layout (<strong style="font-size:200px">some text</strong>)
*	Text fields can also be used for Javascript hacks: <script language="javascript">alert("You can be hacked!!")</script>
* To prevent SQL-injection, escape special characters before writting to the database. Use WB function add_slashes() 
* when dealing with data from POST, GET, COOKIES to prevent double quoting if magic_quotes is enabled in your php.ini.
*
*	Make use of: strip_tags, add_slashes, or htmlspecialchars to prevent such kind of things
*/
// get data send via POST using function defined in framework/class.wb.php (equivalent to $_POST['simple_output'])
// $admin->get_post prevents output of warnings if the specified value does not exist
$simple_output = $admin->get_post('simple_output');

// remove all HTML, PHP and Javascript tags from the string (we are only interested in the text; nothing else)
$simple_output = strip_tags($simple_output);

// escape special characters (', ", \, NULL byte) before  writing to the database to prevent SQL-injections!!!
// make use of add_slashes (defined in framework/class.wb.php) to prevent double escaping of data derived 
// via GET, POST or COOKIES, if magic_quotes_gpc is enabled in the php.ini!!!
$simple_output = $admin->add_slashes($simple_output);

// now write the text to the database, add unix timestamp to store modification date and time
$sql_query = "UPDATE `" .TABLE_PREFIX ."mod_bookings` SET `simple_output` = '$simple_output', `modified_when` = '" 
	.time() ."' WHERE `section_id` = '$section_id'";
$database->query($sql_query);

// check if there is a database error, otherwise say successful (functions defined or included via modules/admin.php)
if($database->is_error()) {
	$admin->print_error($database->get_error(), $js_back);
} else {
	$admin->print_success($MESSAGE['PAGES']['SAVED'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// print admin footer
$admin->print_footer()

?>