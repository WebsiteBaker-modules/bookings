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

// prevent this file from being accessed directly
if(!defined('WB_PATH')) die(header('Location: index.php'));

require('info.php');

global $MOD_BOOKINGS;

// default header
$bookingsheader = addslashes('<div class="bookings_header"><h1>'.$MOD_BOOKINGS['BOOKINGS_TITLE'].'</h1></div>');
// default footer
$bookingsfooter = addslashes( '<div class="bookings_footer">Powered by '
                            . $module_name
                            . ' v'
                            . $module_version
                            . '</div><!-- bookings_footer -->');

$sql = "INSERT INTO ".TABLE_PREFIX."mod_bookings_settings 
           (   page_id,     section_id, breakafter, stylesheet,   bookingsheader,    bookingsfooter,    dayview, daysheetheader, daystarthour, dayendhour, admin_groups, add_groups, mod_groups, del_groups )
    VALUES ( '$page_id', '$section_id', 3,          '',           '$bookingsheader', '$bookingsfooter', 'list',  '',             8,            18        , 1           , 1         , 1         , 1          )";

$database->query($sql);

if( $database->is_error() ) {
	  $admin->print_error( 
        $database->get_error(), 
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id
   );
} else {
    // Get the id
    $section_id = $database->get_one("SELECT LAST_INSERT_ID()");
	  $admin->print_success(
        $TEXT['SUCCESS'], 
        ADMIN_URL.'/pages/modify.php?page_id='.$page_id
   );
}

// Print admin footer
$admin->print_footer();

?>
