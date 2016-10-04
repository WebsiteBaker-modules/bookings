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
if(!defined('WB_PATH')) die(header('Location: index.php'));

global $day, $month, $week, $quart, $year, $monthplus, $delim, $ranges;

require realpath( dirname(__FILE__) . '/config.inc.php' );

/* this is for WB 2.8! */
if ( ! defined( 'THEME_URL' ) ) {
    define( 'THEME_URL', ADMIN_URL );
}

$delim = ' &raquo; ';

unset($day);
unset($month);
unset($week);
unset($quart);
unset($monthplus);
$continue = true;

$debug = false;
#$debug = true;
if ( true === $debug ) {
  	ini_set('display_errors', 1);
  	error_reporting(E_ALL);
  	echo "<pre>SECTION ID: $section_id<br />";
    print_r($_REQUEST);
    echo "</pre>";
}

include_once('functions.php');

global $MOD_BOOKINGS;

if(!file_exists(WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php')) {
    require WB_PATH .'/modules/bookings_v2/languages/EN.php';
} else {
    // a module language file exists for the language defined by the user, load it
	require WB_PATH .'/modules/bookings_v2/languages/' .LANGUAGE .'.php';
}

// check for numeric section_id
if ( ! preg_match( "/^(\d+)$/", $section_id, $matches ) ) {
    return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'] );
}
if ( isset( $_REQUEST['section_id'] ) && ! preg_match( "/^(\d+)$/", $_REQUEST['section_id'], $matches ) ) {
    return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'] );
}

if ( isset( $_REQUEST['section_id'] ) && $section_id !== $_REQUEST['section_id'] ) {
    return;
}

// validate params
$bookings_id = NULL;
if ( isset ( $_REQUEST['bookings_id'] ) && is_numeric( $_REQUEST['bookings_id'] ) ) {
    $bookings_id = $_REQUEST['bookings_id'];
}

/**
 * get the settings
**/
$set = _Bookings_Settings($section_id);

/**
*	INLCUDE FRONTEND.CSS INTO THE HTML BODY OF THE PAGE IF WB < 2.6.7 OR REGISTER_MODFILES FUNCTION
* IN THE INDEX.PHP OF YOUR TEMPLATE IS NOT USED
*	NOTE: THIS WAY MODULES BECOME DOWNWARD COMPATIBLE WITH OLDER WB RELEASES
*/
// check if frontend.css file needs to be included into the <body></body> of view.php
if((!function_exists('register_frontend_modfiles') || !defined('MOD_FRONTEND_CSS_REGISTERED')) &&
  	file_exists(WB_PATH .'/modules/bookings_v2/frontend.css')) {
  	echo '<style type="text/css">';
    include(WB_PATH .'/modules/bookings_v2/frontend.css');
    echo "\n</style>\n";
}

if ( $set['stylesheet'] ) {
    echo $set['stylesheet'];
}

Bookings_Header();

if ( isset( $_SESSION['USER_ID'] ) ) {

    if ( Bookings_user_can( 'add', '', $section_id )
      || Bookings_user_can( 'mod', '', $section_id )
      || Bookings_user_can( 'del', '', $section_id )
    ) {

        if ( isset ( $_REQUEST['save'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_save_Entry( $_REQUEST['bookings_id'], $section_id );
            $continue = false;
            Bookings_Show_List( $section_id );
        }
        elseif ( isset ( $_REQUEST['modify'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_Show_List( $section_id );
            $continue = false;
        }
        elseif ( isset ( $_REQUEST['add'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_add_Entry( $section_id );
            $continue = false;
        }
        elseif ( isset ( $_REQUEST['edit'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_edit_Entry( $bookings_id, $section_id );
            $continue = false;
        }
        elseif ( isset ( $_REQUEST['settings'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_edit_Settings( $section_id );
            $continue = false;
        }
        elseif ( isset ( $_REQUEST['save_settings'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_save_Settings( $section_id );
            $continue = false;
        }
        elseif ( isset ( $_REQUEST['delete'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_delete_Entry( $section_id );
            $continue = false;
            Bookings_Show_List( $section_id );
        }
        elseif ( isset ( $_REQUEST['editgroups'] ) && $section_id === $_REQUEST['section_id'] ) {
            Bookings_edit_Groups( $section_id );
            $continue = false;
        } 
        else {
            echo "<p><a id=\"bookings_modlink\" href=\"?modify=1&amp;section_id=$section_id\" title=\"",
                 $MOD_BOOKINGS['FRONTEND_MOD_LINK'],
                 "\"><img src=\"",
                 WB_URL,
                 '/modules/bookings_v2/pages.png" alt="',
                 $MOD_BOOKINGS['FRONTEND_MOD_LINK'],
                 '" title="',
                 $MOD_BOOKINGS['FRONTEND_MOD_LINK'],
                 '" /></a></p>';
        }
    }
}

if ( $continue === true ) {

    // create nav links
    $params = array();
    // Defaults
    $year  = date("Y");

    if ( (isset($_GET['day'])) and ($_GET['day']!="") and ((int)$_GET['day']>=1) and ((int)$_GET['day']<=31) ) {
    	  $day = (int)$_GET['day'];
    }
    if ( (isset($_GET['month'])) and ($_GET['month']!="") and ((int)$_GET['month']>=1) and ((int)$_GET['month']<=12) ) {
    	  $month = (int)$_GET['month'];
    }
    if ( (isset($_GET['week'])) and ($_GET['week']!="") and ((int)$_GET['week']>=1) and ((int)$_GET['week']<=53) ) {
    	  $week = (int)$_GET['week'];
    }
    if ( (isset($_GET['quart'])) and ($_GET['quart']!="") and ((int)$_GET['quart']>=1) and ((int)$_GET['quart']<=4) ) {
    	  $quart = (int)$_GET['quart'];
    }
    if ( (isset($_GET['year'])) and ((int)$_GET['year']!="-") ) {
    	  $year  = (int)$_GET['year'];
    }
    if ( (isset($_GET['monthplus'])) ) {
        if ( isset($month) ) {
            $monthplus = $month;
        }
        else {
    	      $monthplus = date("m");
 	      }
    }

    if ( ! isset($_GET['day'])  && ! isset($_GET['month']) 
      && ! isset($_GET['week']) && ! isset($_GET['quart'])
      && ! isset($_GET['year']) && ! isset($_GET['monthplus'])
      && ! empty ( $set['default_view'] ) 
    ) {
        if ( $set['default_view']     === 'day' ) {
            $day   = ( empty($day) )   ? date("d") : $day;
        }
        elseif ( $set['default_view'] === 'month' ) {
            $month = ( empty($month) ) ? date("m") : $month;
        }
        elseif ( $set['default_view'] === 'week' ) {
            $week = ( empty($week) )   ? date("W") : $week;
        }
        elseif ( $set['default_view'] === 'quart' ) {
            $quart = ( empty($quart) ) ? intval(( date("m") - .1)/3 + 1) : $quart;
        }
    }

    echo $set['bookingsheader'],
		 ( ( $legend_on == 'top' || $legend_on == 'both' ) ? Bookings_Legend()   . "<br /><br />\n" : NULL ),
         ( ( $nav_on    == 'top' || $nav_on    == 'both' ) ? Bookings_Navlinks() . "<br /><br />\n" : NULL ),
         "<br /><br />\n";

	// Range?
    if ( isset( $_GET['range'] ) && is_numeric( $_GET['range'] ) && $_GET['range'] <= $max_range ) {
        $day   = ( empty($day)   ? date('d') : $day   );
        $month = ( empty($month) ? date('m') : $month );
        $year  = ( empty($year)  ? date('Y') : $year  );
        echo Bookings_create_Breadcrumb('day'),
			 Bookings_View_Day_Range( $day, $year, $month, $_GET['range'], $section_id, $set );
    }
    // Show single day only?
    elseif ( isset( $day ) ) {
        $month = ( empty($month) ) ? date("m") : $month;
        $week  = ( empty($week) )  ? date("W", mktime(0,0,0,$month,$day,$year)) : $week;
		echo Bookings_create_Breadcrumb('day'),
	         Bookings_View_Day( $year, $month, $day, $section_id, $set );
    }
    elseif ( isset( $monthplus ) ) {
        echo Bookings_create_Breadcrumb('mplus'),
             Bookings_Three_Months( $year, $monthplus, $section_id );
    }
    elseif ( isset( $month ) && ! isset( $week ) ) {
        echo Bookings_create_Breadcrumb('month'),
             Bookings_Month_Sheet( $year, $month, $section_id, true );
    }
    elseif ( isset( $quart ) && ! isset( $week ) ) {
        echo Bookings_Show_Quarter( $year, $quart, $section_id, $set );
    }
    elseif ( isset( $week ) ) {
        if ( empty($month) ) { 
            $date = getdate( strtotime("1.1.$year + ".($week-1)." weeks") );
            $month = $date['mon'];
        }
      	echo Bookings_create_Breadcrumb('week'),
             Bookings_Week_Sheet( $year, $week, $section_id, $set );
    }
    else { // full year
    
        $prev_year_link = Bookings_create_link ( ( $year - 1 ), NULL, NULL, NULL, NULL, true );
        $next_year_link = Bookings_create_link ( ( $year + 1 ) );
        
        echo "<!-- begin div bookings_yearnav --><div class=\"bookings_yearnav\">\n",
             "<span class=\"bookings_left\">$prev_year_link</span>\n",
             "<span class=\"bookings_right\">$next_year_link</span>\n",
             "<br class=\"bookings_yearnav_clear\" /><br />\n";

        for ( $m = 1; $m <= 12; $m++ ) {
            if ( isset( $set['breakafter'] ) && $set['breakafter'] > 0 && $m % $set['breakafter'] == 1 ) {
                echo "<br class=\"bookings_clear\" /><p></p>\n";
            }
            echo Bookings_Month_Sheet( $year, $m, $section_id, false );
        }

        echo "</div><!-- end div bookings_yearnav -->\n";

    }

}

echo ( $legend_on != 'top' ? "<br /><br />\n" . Bookings_Legend()   : NULL );
echo ( $nav_on    != 'top' ? "<br /><br />\n" . Bookings_Navlinks() : NULL );

Bookings_Footer();

function Bookings_Legend() {
	global $MOD_BOOKINGS;
	echo "<div id=\"legend\">\n",
         "<span class=\"bookings_reserved\">&nbsp;&nbsp;</span> = ",
         $MOD_BOOKINGS['STATE_RESERVED'],
         "<br />\n",
         "<span class=\"bookings_booked\">&nbsp;&nbsp;</span> = ",
         $MOD_BOOKINGS['BOOKED'],
         "<br />\n",
         "<span class=\"bookings_partially\">&nbsp;&nbsp;</span> = ",
         $MOD_BOOKINGS['BOOKED_PARTIALLY'],
         "<br />",
         "<span class=\"bookings_past\">&nbsp;&nbsp;</span> = ",
         $MOD_BOOKINGS['IN_PAST'],
         "<br />\n",
         "<span class=\"bookings_today\">&nbsp;&nbsp;</span> = ",
         $MOD_BOOKINGS['TODAY'],
         "</div>\n";
}

function Bookings_Navlinks() {
	global $MOD_BOOKINGS;
    $links  = array();
    foreach ( array( 'year' => 'Y', 'quart' => 'y', 'month' => 'm', 'week' => 'W', 'day' => 'd' ) as $view => $char ) {
        $params[] = $view . "=" . ( $view == "quart" ? intval(( date("m") - .1)/3 + 1) : date($char) );
        $links[]  = "<a href=\"?"
                  . implode( '&', $params )
                  . "\">"
                  . $MOD_BOOKINGS['DEFAULT_VIEW_'.strtoupper($view)]
                  . "</a>\n";
    }
    $links[] = "<a href=\"".$_SERVER['SCRIPT_NAME']."?range=1\">".$MOD_BOOKINGS['DAYSPAN']."</a>";
	echo "<div id=\"navlinks\">[ ",
         implode( ' | ', $links ),
         " ]</div>\n";
}

?>
