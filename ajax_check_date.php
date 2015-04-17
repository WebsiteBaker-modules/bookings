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
//if(!defined('WB_PATH')) die(header('Location: index.php'));  

require('../../config.php');
include_once('functions.php');

global $MOD_BOOKINGS;
global $output;

// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php')) {
  	// no module language file exists for the language set by the user, include default module language file EN.php
	  require_once(WB_PATH .'/modules/bookings_v2/languages/EN.php');
} else {
	  // a module language file exists for the language defined by the user, load it
		require_once(WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php');
}

$section     = is_numeric( $_REQUEST['section'] )    ? $_REQUEST['section']    : NULL;
$bookings_id = is_numeric( $_REQUEST['id'] )         ? $_REQUEST['id']         : NULL;
$beginyear   = is_numeric( $_REQUEST['beginyear'] )  ? $_REQUEST['beginyear']  : NULL;
$beginmonth  = is_numeric( $_REQUEST['beginmonth'] ) ? $_REQUEST['beginmonth'] : NULL;
$beginday    = is_numeric( $_REQUEST['beginday'] )   ? $_REQUEST['beginday']   : NULL;
$endyear     = is_numeric( $_REQUEST['endyear'] )    ? $_REQUEST['endyear']    : NULL;
$endmonth    = is_numeric( $_REQUEST['endmonth'] )   ? $_REQUEST['endmonth']   : NULL;
$endday      = is_numeric( $_REQUEST['endday'] )     ? $_REQUEST['endday']     : NULL;

$beginhour   = ( isset( $_REQUEST['beginhour']   ) && is_numeric( $_REQUEST['beginhour'] ) ) ? $_REQUEST['beginhour']   : 0;
$beginminute = ( isset( $_REQUEST['beginminute'] ) && is_numeric($_REQUEST['beginminute']) ) ? $_REQUEST['beginminute'] : 0;
$endhour     = ( isset( $_REQUEST['endhour']     ) && is_numeric( $_REQUEST['endhour'] )   ) ? $_REQUEST['endhour']     : 0;
$endminute   = ( isset( $_REQUEST['endminute']   ) && is_numeric( $_REQUEST['endminute'] ) ) ? $_REQUEST['endminute']   : 0;
    
$begin = mktime($beginhour, $beginminute, 0, $beginmonth, $beginday, $beginyear);
$end   = mktime($endhour  , $endminute  , 0, $endmonth  , $endday  , $endyear  );

// send the posted date through date() to get a correct date
$begindate = date( 'Y-m-d H:i:s', $begin );
$enddate   = date( 'Y-m-d H:i:s', $end   );

$output = Bookings_DateOverlap( $begindate, $enddate, $section, $bookings_id, 1 );

if ( ! empty ( $output ) ) {
    echo $MOD_BOOKINGS['ERR_DATES_OVERLAP'], $output;
}

?>
