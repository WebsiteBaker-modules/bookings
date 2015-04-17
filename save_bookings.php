<?php

/**
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) 2008, Bianka Martinovic
  Contact me: blackbird(at)webbird.de, http://www.webbird.de/

  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
  GNU General Public License for more details.
**/

require('../../config.php');
require('functions.php');

// Load Language file
if(LANGUAGE_LOADED) {
  	if(!file_exists(WB_PATH.'/modules/bookings_v2/languages/'.LANGUAGE.'.php')) {
  		  require_once(WB_PATH.'/modules/bookings_v2/languages/EN.php');
  	} else {
  		  require_once(WB_PATH.'/modules/bookings_v2/languages/'.LANGUAGE.'.php');
  	}
}

if(!method_exists($admin, 'register_backend_modfiles') && file_exists(WB_PATH .'/modules/bookings_v2/backend.css')) {
  	echo '<style type="text/css">';
  	include(WB_PATH .'/modules/bookings_v2/backend.css');
  	echo "\n</style>\n";
}

// Get id
if(!isset($_POST['bookings_id']) OR !is_numeric($_POST['bookings_id'])) {
	  header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	  $bookings_id = $_POST['bookings_id'];
}

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');



// Print admin footer
$admin->print_footer();

?>
