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

// prevent this file from being accessed directly
if ( ! defined('WB_PATH') ) {
    #require('../../config.php');
    die(header('Location: index.php'));
}  

global $js_back;

$debug = false;
if ( true === $debug ) {
  	ini_set('display_errors', 1);
  	error_reporting(E_ALL);
}

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
@include WB_PATH .'/framework/module.functions.php';

include dirname(__FILE__).'/functions.php';

/**
*	MODULE LANGUAGE SUPPORT IS INTRODUCED WITH THE LINES BELOW
*	NOTE: IF YOU ADD LANGUAGE SUPPORT TO YOUR MODULE, MAKE SURE THAT THE DEFAULT LANGUAGE
*	EN.. ENGLISH IS AVAILABLE IN ANY CASE
*/
// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php')) {
  	// no module language file exists for the language set by the user, include default module language file EN.php
  	require WB_PATH .'/modules/bookings_v2/languages/EN.php';
} else {
	// a module language file exists for the language defined by the user, load it
	require_once WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php';
}

Bookings_Header();

if ( isset( $_REQUEST['save'] ) && $section_id === $_REQUEST['section_id'] ) {
    $error = Bookings_save_Entry( $_REQUEST['bookings_id'], $section_id );
    if ( ! empty ( $error ) ) {
        $admin->print_error( $error, $js_back );
    }
    Bookings_Show_List();
}
elseif ( isset( $_REQUEST['add'] ) && $section_id === $_REQUEST['section_id'] ) {
    Bookings_add_Entry( $section_id );
}
elseif ( isset( $_REQUEST['settings'] ) && $section_id === $_REQUEST['section_id'] ) {
    Bookings_edit_Settings( $section_id );
}
elseif ( isset( $_REQUEST['editgroups'] ) && $section_id === $_REQUEST['section_id'] ) {
    Bookings_edit_Groups( $section_id );
}
elseif ( isset( $_REQUEST['save_settings'] ) && $section_id === $_REQUEST['section_id'] ) {
    Bookings_save_Settings( $section_id );
}
elseif ( isset( $_REQUEST['edit'] ) && $section_id === $_REQUEST['section_id'] ) {
    Bookings_edit_Entry( $_REQUEST['bookings_id'], $section_id );
}
elseif ( isset( $_REQUEST['delete'] ) && $section_id === $_REQUEST['section_id'] ) {

    $error = Bookings_delete_Entry();
    if ( ! empty ( $error ) ) {
        $admin->print_error( $error, $js_back );
    }
    Bookings_Show_List( $section_id );
    
}
else {
    Bookings_Show_List();
}

?>
