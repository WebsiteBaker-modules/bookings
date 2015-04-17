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

$debug = false;
if ( true === $debug ) {
  	ini_set('display_errors', 1);
  	error_reporting(E_ALL);
}


require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
require('functions.php');

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

Bookings_Header();

if ( isset( $_REQUEST['save'] ) ) {
    $error = Bookings_save_Entry( $_REQUEST['bookings_id'], $section_id );
    if ( ! empty ( $error ) ) {
        $admin->print_error( $error, $js_back );
    }
    Bookings_Show_List( $section_id );
}
else {
    Bookings_add_Entry( $section_id );
}

// Print admin footer
$admin->print_footer();

?>
