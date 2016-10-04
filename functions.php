<?php

/* this is for WB 2.8! */
if ( ! defined( 'THEME_URL' ) ) {
    define( 'THEME_URL', ADMIN_URL );
}

global $settings;
global $admin_groups;
global $add_groups;
global $mod_groups;
global $del_groups;

global $day, $month, $week, $quart, $year, $monthplus, $delim;

global $DEFAULTS;
$DEFAULTS = array(
    'startminhour' => 8,
    'endminhour'   => 12,
    'show_until'   => true
);

require_once( WB_PATH.'/framework/module.functions.php' );
require_once( WB_PATH.'/framework/functions.php'        );

global $can;
$can = array( 'admin' => 0, 'add' => 0, 'mod' => 0, 'del' => 0 );

global $debug, $debug_to_browser;
$debug = false;
$debug_to_browser = false;
//$debug = true;
//$debug_to_browser = true;
if ( true === $debug ) {
  	ini_set('display_errors', 1);
  	error_reporting(E_ALL);
}

global $wb, $admin;

// workaround for PHP4 --- known to work with 4.4.9
if ( ! function_exists('date_parse') ) {

    $eval_func =
    'function date_parse ( $date ) {
        $date_array = getdate(strtotime($date));
        $date_array[\'day\']    = $date_array[\'mday\'];
        $date_array[\'month\']  = $date_array[\'mon\'];
        $date_array[\'hour\']   = $date_array[\'hours\'];
        $date_array[\'minute\'] = $date_array[\'minutes\'];
        $date_array[\'second\'] = $date_array[\'seconds\'];
        return $date_array;
    }';

    $ret = eval (
        $eval_func
    );
}

function Bookings_Header () {
    global $MOD_BOOKINGS, $set;
    echo "<!-- begin Bookings --><div id=\"mod_bookings\">\n";
}

function Bookings_Footer () {
    global $set;
    echo "<br class=\"bookings_clear\" /><br />\n",
         $set['bookingsfooter'],
         "\n</div><!-- end Bookings -->\n";
}

/**
 * error message
 **/
function Bookings_Error( $msg = NULL, $debugmsg = NULL ) {

    global $wb, $admin, $debug, $debug_to_browser;

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    if ( $debug && $debug_to_browser && $debugmsg ) {
        $msg = $msg . '<br /><br />Debug info:<br />' . $debugmsg;
    }

    echo $class->print_error(
         $msg,
         'index.php',
         false
    );


}  // end function Bookings_Error()

/**
 * day view
 **/
function Bookings_View_Day( $year, $month, $day, $section_id, $set )
{
    if ( isset( $_REQUEST['bygroup'] ) ) {
        return Bookings_View_ByGroup( $year, $month, $day, $section_id, $set );
    }
    if ( $set['dayview'] == 'sheet' ) {
        return Bookings_Day_Sheet( $year, $month, $day, $section_id, $set );
    }
    else {
        return Bookings_Day_List(  $year, $month, $day, $section_id, $set );
    }
}

/**
 * range view
 **/
function Bookings_View_Day_Range( $day, $year, $month, $range, $section_id, $set )
{
    return Bookings_Day_List_Range(  $day, $year, $month, $range, $section_id, $set );
}


/**
 * shows bookings as list
 **/
function Bookings_Day_List( $year, $month, $day, $section_id, $set )
{
    global $MOD_BOOKINGS, $settings;

    $bookings = _GetBookingsForDay( $year, $month, $day, $section_id );

    $dateformat = ( empty ($set['dateformat'] ) )
                ? $MOD_BOOKINGS['DEFAULT_DATEFORMAT']
                : $set['dateformat'];

    $header = $set['daysheetheader']
            ? $set['daysheetheader'].' - '.strftime ( $dateformat, mktime( 0, 0, 0, $month, $day, $year ) )
            : strftime ( $dateformat, mktime( 0, 0, 0, $month, $day, $year ) );

    $prev_ts = mktime( 0, 0, 0, $month, ($day-1), $year );
    $next_ts = mktime( 0, 0, 0, $month, ($day+1), $year );
    $prev   = '?day=' . date( 'd', $prev_ts ) . '&amp;month=' . date( 'm', $prev_ts ) . '&amp;year=' . date( 'Y', $prev_ts );
    $next   = '?day=' . date( 'd', $next_ts ) . '&amp;month=' . date( 'm', $next_ts ) . '&amp;year=' . date( 'Y', $next_ts );

    $r  = "<table class=\"bookings_daysheet\">\n"
        . "<tr><th class=\"bookings_daysheet_header\" colspan=\"3\">"
        . $header
        . "</th></tr>\n"
        . "<tr><td class=\"bookings_daysheet_header\"><a href=\"".$settings['base_url']."$prev\">&laquo;</a></td>"
        . "<td class=\"bookings_daysheet_header\"></td>"
        . "<td class=\"bookings_daysheet_header right\"><a href=\"".$settings['base_url']."$next\">&raquo;</a></td></tr>\n";

    if ( count($bookings) > 0 ) {
        foreach ( $bookings as $item ) {
            $r .= "<tr><td>" . $item['time'] . "</td><td>" . $item['what'] . "</td><td style=\"background-color:"
               .  (
                      (
                           isset($item['color'])
                        && $item['color'] != ''
                      )
                      ? $item['color']
                      : '#fff'
                  )
               .  " !important;\">"
               .  ( isset ( $item['group'] ) ? $item['group'] : '' )
               .  "</td></tr>\n";
        }
    }
    else {
        $r .= "<tr><td colspan=\"2\">"
           .  $MOD_BOOKINGS['NO_BOOKINGS']
           .  "</td></tr>\n";
    }

    $r .= "</table>\n";

    return $r;
}

/**
 * creates a list for a range of days
 **/
function Bookings_Day_List_Range( $day, $year, $month, $range, $section_id, $set )
{
    global $MOD_BOOKINGS, $settings;

	$count      = 0;
    $dateformat = ( empty ($set['dateformat'] ) )
                ? $MOD_BOOKINGS['DEFAULT_DATEFORMAT']
                : $set['dateformat'];

    $r  = "<table class=\"bookings_daysheet\">\n";

    for( $i=0; $i<=$range; $i++ ) {
		// mktime automagically 'fixes' invalid date (31.02....)
		$time     = mktime( 0, 0, 0, $month, ( $day + $i ), $year );
        $bookings = _GetBookingsForDay( date( 'Y', $time ), date( 'm', $time ), date( 'd', $time ), $section_id );
        if ( ! count( $bookings ) ) {
			continue;
		}
		$count += count($bookings);
		$now    = $day + $i;
	    $header = $set['daysheetheader']
	            ? $set['daysheetheader'].' - '.strftime ( $dateformat, mktime( 0, 0, 0, $month, $now, $year ) )
	            : strftime ( $dateformat, mktime( 0, 0, 0, $month, $now, $year ) );

	    $prev_ts = mktime( 0, 0, 0, $month, ($now-1), $year );
	    $next_ts = mktime( 0, 0, 0, $month, ($now+1), $year );
	    $prev   = '?day=' . date( 'd', $prev_ts ) . '&amp;month=' . date( 'm', $prev_ts ) . '&amp;year=' . date( 'Y', $prev_ts );
	    $next   = '?day=' . date( 'd', $next_ts ) . '&amp;month=' . date( 'm', $next_ts ) . '&amp;year=' . date( 'Y', $next_ts );
	    $r     .= "<tr><td class=\"bookings_daysheet_header bookings_daysheet_header_range\" colspan=\"3\">"
	           . $header
	           . "</td></tr>\n";
	    foreach ( $bookings as $item ) {
            $r .= "<tr><td>" . $item['time'] . "</td><td>" . $item['what'] . "</td><td style=\"background-color:"
               .  (
                      (
                           isset($item['color'])
                        && $item['color'] != ''
                      )
                      ? $item['color']
                      : '#fff'
                  )
               .  " !important;\">"
               .  ( isset ( $item['group'] ) ? $item['group'] : '' )
               .  "</td></tr>\n";
        }

    }

    if ( ! $count ) {
		$r .= "<tr><td colspan=\"3\">"
		    . $MOD_BOOKINGS['NO_BOOKINGS']
		    . "</td></tr>\n";
	}

    $r .= "</table>\n";

    $r  = "<table class=\"bookings_daysheet\">\n"
        . "<tr><th class=\"bookings_daysheet_header\">"
		  . strftime ( $dateformat, mktime( 0, 0, 0, $month, $day, $year ) )
		  . " - "
		  . strftime ( $dateformat, mktime( 0, 0, 0, $month, ($day+$range), $year ) )
		. "</th></tr>"
        . "</table>\n"
        . $r;

    return Bookings_Ranges_Form() . $r;
}   // end function Bookings_Day_List_Range()

/**
 * creates a "calendar sheet" for a chosen day
 **/
function Bookings_Day_Sheet( $year, $month, $day, $section_id, $set )
{
    global $MOD_BOOKINGS, $DEFAULTS, $settings;

    $start      = $set['daystarthour'];
    $stop       = $set['dayendhour'];
    $dateformat = $set['dateformat'];

    $stop = ( $stop == 0 ) ? 24 : $stop;

    if ( ! ( $start >= 0                       && $start <= $DEFAULTS['startminhour'] ) ) {
        $start = $DEFAULTS['startminhour'];
    }
    if ( ! ( $stop  >= $DEFAULTS['endminhour'] && $stop  <= 24                        ) ) {
        $stop  = $DEFAULTS['endminhour'];
    }

    if ( ! $dateformat ) { $dateformat = '%x'; }

    $bookings = _GetBookingsForDay( $year, $month, $day, $section_id, 'sheet' );

    $header = $set['daysheetheader']
            ? $set['daysheetheader'].' - '.strftime ( $dateformat, mktime( 0, 0, 0, $month, $day, $year ) )
            : strftime ( $dateformat, mktime( 0, 0, 0, $month, $day, $year ) );

    $prev_ts = mktime( 0, 0, 0, $month, ($day-1), $year );
    $next_ts = mktime( 0, 0, 0, $month, ($day+1), $year );
    $prev    = '?day=' . date( 'd', $prev_ts ) . '&amp;month=' . date( 'm', $prev_ts ) . '&amp;year=' . date( 'Y', $prev_ts );
    $next    = '?day=' . date( 'd', $next_ts ) . '&amp;month=' . date( 'm', $next_ts ) . '&amp;year=' . date( 'Y', $next_ts );

    $r  = "<table class=\"bookings_daysheet\">\n"
        . "<tr><th class=\"bookings_daysheet_header\" colspan=\"3\">"
        . $header
        . "</th></tr>\n"
        . "<tr><td class=\"bookings_daysheet_header\"><a href=\"".$settings['base_url']."$prev\">&laquo;</a></td>"
        . "<td class=\"bookings_daysheet_header\"></td>"
        . "<td class=\"bookings_daysheet_header right\"><a href=\"".$settings['base_url']."$next\">&raquo;</a></td></tr>\n";

    $TIMES = _GetTimeOffsets($section_id);

    for ( $i=$start; $i<=$stop; $i++ ) {

        $i = sprintf( "%02d", $i );

        foreach( $TIMES as $min ) {

            if ( $i == $stop && $min > 0 ) {
                break;
            }

            $min = sprintf( "%02d", $min );
            $css = "bookings_daysheet_free";
            $ts  = strtotime( "$year-$month-$day $i:$min:00" );

            if ( is_in_past( $year, $month, $day, $i, $min ) ) {
                $css .= " bookings_past";
            }

            if ( ! empty ( $bookings[$ts] ) ) {
                $css .= " bookings_daysheet_".$bookings[$ts]['state'];
            }

            $r .= "<tr><td class=\"bookings_daysheet_hour\">$i:$min</td>"
               .  "<td class=\"".$css."\">"
               .  ( isset ( $bookings[$ts]['what'] )  ? $bookings[$ts]['what']  : ''           )
               /* This is where I need to add more booking details. Mike E*/
               .  "</td><td class=\"".$css."\"  style=\"background-color:"
               .  (
                      (
                           isset($bookings[$ts])
                        && isset($bookings[$ts]['color'])
                        && $bookings[$ts]['color'] != ''
                      )
                      ? $bookings[$ts]['color']
                      : '#fff'
                  )
               .  " !important;\">"
               .  ( isset ( $bookings[$ts]['group'] ) ? $bookings[$ts]['group'] : '' )
               .  "</td></tr>\n"
               ;

        }

    }

    $r .= "</tr></table>\n";

    return $r;

}

/**
 * create week sheet
 **/
function Bookings_Week_Sheet( $year, $week, $section_id, $set )
{
    global $MOD_BOOKINGS, $DEFAULTS, $settings;

    $start      = $set['daystarthour'];
    $stop       = $set['dayendhour'];
    $dateformat = $set['dateformat'];

    $stop = ( $stop == 0 ) ? 24 : $stop;

    if ( ! ( $start >= 0                       && $start <= $DEFAULTS['startminhour'] ) ) {
        $start = $DEFAULTS['startminhour'];
    }
    if ( ! ( $stop  >= $DEFAULTS['endminhour'] && $stop  <= 24                        ) ) {
        $stop  = $DEFAULTS['endminhour'];
    }

    $first_day_of_week = strftime( "%d", _WeekToDay( $week, $year ) );
    $month             = date( "n", _WeekToDay( $week, $year ) );
    $TIMES 	  		     = _GetTimeOffsets($section_id);
    $lines             = array();
    $last_day_of_month = strftime( "%d", mktime(0,0,0,($month+1),0,$year));
    $dateline          = '';

    $lastweek = date( "W", _WeekToDay( ($week-1), $year ) );
    $nextweek = ( $week + 1 );
    $lastyear = date( "Y", _WeekToDay( ($week-1), $year ) );;
    $nextyear = $year;

    if ( $week == 1 ) {
#### FIXME
        #$lastyear = date("Y", mktime(0,0,0,12,30,($year-1)));
    }

    # fix by "jacobi22"; thank you!
    # http://forum.websitebaker.org/index.php/topic,28801.msg201767.html#msg201767
    if ( $week == 52 ) {
        $nextweek = intval(date("W",mktime(0,0,0,1,1,($year+1))));
        $nextyear = date("Y", mktime(0,0,0,1,1,($year)));
    }
    if ( $week == 53 ) {
        $nextweek = intval(date("W",mktime(0,0,0,1,1,($year))));
        $nextyear = date("Y", mktime(0,0,0,1,1,($year+1)));
    }

    for ( $current=$first_day_of_week; $current<=($first_day_of_week+6); $current++ )
    {

        $day        = $current;
        $this_month = $month;
        $this_year  = $year;

        if ( $day > $last_day_of_month ) {
            $day        = $current - $last_day_of_month;
            $this_month = date("n", mktime(0,0,0,($month+1),1,$year));
            $this_year  = date("Y", mktime(0,0,0,($month+1),1,$year));
        }

        $dateline .= "    <th class=\"bookings_week_header\">"
                  .  "<a href=\"".$settings['base_url']."?year=$this_year&month=$this_month&day=$day\">"
                  .  sprintf( "%02d", $day )
                  .  "</a></th>\n";

        $bookings = _GetBookingsForDay( $this_year, $this_month, $day, $section_id, 'sheet' );

        for ( $i=$start; $i<=$stop; $i++ ) {

  	        $i = sprintf( "%02d", $i );

  	        foreach( $TIMES as $min ) {

  	            if ( $i == $stop && $min > 0 ) {
  	                break;
  	            }

  	            $min  = sprintf( "%02d", $min );
  	            $css  = "bookings_daysheet_free";
  	            $ts   = strtotime( "$this_year-$this_month-$day $i:$min:00" );

  	            if ( is_in_past( $this_year, $this_month, $day, $i, $min ) ) {
  	                $css .= " bookings_past";
  	            }

  	            if ( ! empty ( $bookings[$ts] ) ) {
  	                $css .= " bookings_tooltip bookings_daysheet_".$bookings[$ts]['state'];
  	            }

  	            $temp = "    <td class=\"".$css."\">"
                      . "        <a href=\"#\" class=\"bookings_tooltip\"><span>"
                      . ( isset( $bookings[$ts]['what'] ) ? $bookings[$ts]['what'] : '' )
                      . "</span>"
                      . "</a>&nbsp;</td>\n";

               $lines[ "$i:$min" ][] = $temp;

            }

        }

    }

    $r  = "<table class=\"bookings_sheet bookings_week\">\n"
        . "<tr>\n"
        . "    <th class=\"bookings_week_header\" colspan=\"9\">"
        . "<a href=\"".$settings['base_url']."?month=$this_month&year=$year&section_id=$section_id\">"
        . $MOD_BOOKINGS['MONTHNAMES'][$month]
        . "</a> <a href=\"".$settings['base_url']."?year=$year\">$year</a></th>\n</tr>\n"
        . "<tr>\n"
        . "    <th class=\"bookings_week_header\">"
        . Bookings_create_link ( $lastyear, NULL, NULL, $lastweek, '&laquo;', true )
        . "</th>\n"
        . $dateline
        . "    <th class=\"bookings_week_header\">"
        . Bookings_create_link ( $nextyear, NULL, NULL, $nextweek, '&raquo;', false )
        . "</th>\n</tr>\n<tr>\n"
        . "    <th class=\"bookings_week_header\">&nbsp;</th>\n";

    // day names
    for ( $i=0; $i<7; $i++ )
    {
        $r .= "    <th class=\"bookings_week_header\">" . $MOD_BOOKINGS['SHORTDAYNAMES'][$i] . "</th>\n";
    }
    $r .= "    <th class=\"bookings_week_header\">&nbsp;</th>\n</tr>\n<tr>\n";

    while ( list ( $time, $line ) = each ( $lines ) )
    {
        $r .= "<tr>\n    <td>$time</td>\n"
           .  implode( "\n", $line )
           .  "\n    <td>&nbsp;</td>\n</tr>\n";
    }

    $r .= "</table>\n";

    return $r;
}

/**
 * creates a "calendar sheet" for a chosen month
 **/
function Bookings_Month_Sheet( $year, $month, $section_id, $single, $no_prev_next = false )
{
    global $MOD_BOOKINGS;

    // strip leading zeros from $month
    $month = (int)($month);

    $last_day_of_month = strftime( "%d", mktime(0,0,0,$month+1,0,$year));
    $colspan           = 8;
    $today             = getdate();
    $settings          = _Bookings_Settings($section_id);

    list ( $bookings, $part, $states )
        = _GetBookings ( $year, $month, $section_id, $single );

    $r  = "<table class=\"bookings_sheet\">\n";

    if ( $single ) {

        $prev_month = strftime( "%m", mktime(0,0,0,($month),0,$year));
        $prev_year  = strftime( "%Y", mktime(0,0,0,($month),0,$year));
        $next_month = strftime( "%m", mktime(0,0,0,($month+2),0,$year));
        $next_year  = strftime( "%Y", mktime(0,0,0,($month+2),0,$year));

        $r .= "<tr><th class=\"bookings_month\">";

        if ( ! $no_prev_next ) {
            $r .= Bookings_create_link ( $prev_year, $prev_month, NULL, NULL, '&laquo;', true );
        }

        $r .= "</th>\n<th colspan=\"" . ( $colspan - 2 ) . "\" class=\"bookings_month\">"
           .  " <a href=\"".$settings['base_url']."?year=$year\" title=\""
           .  $MOD_BOOKINGS['BACK_TO_YEARVIEW']
           .  "\">"
           .  $MOD_BOOKINGS['MONTHNAMES'][$month] . " "
           .  $year
           .  "</a></th>\n<th class=\"bookings_month\">";

        if ( ! $no_prev_next ) {
            $r .= Bookings_create_link ( $next_year, $next_month, NULL, NULL, '&raquo;', false );
        }

        $r .= "</th>\n</tr>\n";

    }
    else {

        if ( $month == 1 )
        {
            $prev_year  = strftime( "%Y", mktime(0,0,0,($month),0,$year));

            $r .= "<tr><th colspan=\"$colspan\" class=\"bookings_month\">"
               .  Bookings_create_link ( $prev_year, NULL, NULL, NULL, '&laquo;', true )
               .  " <a href=\"".$settings['base_url']."?year=$year&amp;month=$month\" title=\""
               .  $MOD_BOOKINGS['CURRENTMONTH']
               .  "\">"
               .  $MOD_BOOKINGS['MONTHNAMES'][$month]
               .  " "
               .  $year
               .  "</a></th></tr>\n";

        }
        elseif ( $month == 12 )
        {
            $next_year  = strftime( "%Y", mktime(0,0,0,($month+2),0,$year));

            $r .= "<tr><th colspan=\"$colspan\" class=\"bookings_month\">"
               .  "<a href=\"".$settings['base_url']."?year=$year&amp;month=$month\" title=\""
               .  $MOD_BOOKINGS['CURRENTMONTH']
               .  "\">"
               .  $MOD_BOOKINGS['MONTHNAMES'][$month]
               .  " "
               .  $year
               .  "</a>"
               .  " " . Bookings_create_link ( $next_year, NULL, NULL, NULL, '&raquo;', false )
               .  "</th></tr>\n";
        }
        else
        {
            $r .= "<tr><th colspan=\"$colspan\" class=\"bookings_month\">"
               .  "<a href=\"".$settings['base_url']."?year=$year&amp;month=$month\" title=\""
               .  $MOD_BOOKINGS['CURRENTMONTH']
               .  "\">"
               .  $MOD_BOOKINGS['MONTHNAMES'][$month]
               .  " "
               .  $year
               .  "</a></th></tr>\n";
        }
    }

    // weekday names
    $r .= "<tr>\n";
    for ( $i=0; $i<7; $i++ )
    {
        $r .= "    <th>" . $MOD_BOOKINGS['SHORTDAYNAMES'][$i] . "</th>\n";
    }

    $r .= "</tr>\n<tr>";

    // pre-padding
    $count   = 0;
    $colspan = strftime( "%w", mktime(0,0,0,$month,0,$year ) );
    if ( $colspan >= 1 ) {
        $r .= "<td class=\"bookings_blank\" colspan=\"$colspan\">&nbsp;</td>";
        $count = $count + $colspan;
    }

    $weeknumber = date( 'W', mktime(0,0,1,$month,1,$year));

    for ( $i=1; $i<=$last_day_of_month; $i++ )
    {

        if ( $count%7 == 0 && $count > 0 )
        {
            $r .= "<td class=\"bookings_weeknumber\">"
               .  "<a href=\"".$settings['base_url']."?year=$year&week=$weeknumber\">$weeknumber</a>"
			   .  "</td></tr>\n<tr>";
            $weeknumber = date( 'W', mktime(0,0,1,$month,$i+1,$year));
        }

        $day = sprintf( "%02d", $i );

        $css = "bookings_weekday";
        if ( ( $count + 1 ) % 7 == 0 ) {
            $css = "bookings_sunday";
        }

        if (
            $year  == $today['year']
            &&
            $month == $today['mon']
            &&
            $i     == $today['mday']
        ) {
            $css .= " bookings_today";
        }

        if ( is_in_past( $year, $month, $i ) ) {
            $css .= " bookings_past";
        }

        if ( ! is_in_past( $year, $month, $i ) || $settings['showpast'] == 'y' ) {
            if ( ! empty ( $bookings[$i] ) ) {
                $css .= " bookings_".$states[$i];
                if ( ! preg_match( "/^\d+$/", $bookings[$i] ) ) {
                    $day  = $bookings[$i];
                }
                if ( ! empty ( $part[$i] ) ) {
                    $css .= " bookings_partially";
                }
            }
        }

        $r .= "<td class=\"$css\">$day</td>";

        $count++;

    }

    // post-padding
    if ( $count%7 != 0 && $count > 0 )
    {
        $colspan = 7 - strftime( "%w", mktime(0,0,0,$month,$last_day_of_month,$year ) );
        if ( $colspan >= 1 ) {
            $r .= "<td class=\"bookings_blank\" colspan=\"$colspan\">&nbsp;</td>";
        }
    }
    if ( $weeknumber == "01" ) {
        $year++;
    }
    $r .= "<td class=\"bookings_weeknumber\">"
       .  "<a href=\"".$settings['base_url']."?year=$year&week=$weeknumber\">$weeknumber</a>"
       .  "</td>";

    return $r . "</tr></table>\n";
}

/**
 *
 **/
function Bookings_Show_Quarter ( $year, $quart, $section_id, $set )
{

    global $MOD_BOOKINGS, $settings;

    $begin = 3 * $quart - 2;
    $end   = $begin + 3;

    echo Bookings_create_Breadcrumb('quart');

    // link to last quarter
    if ( ( $quart - 1 ) > 0 ) {
        $prevquarter = $quart - 1;
        $prevyear    = $year;
        $prevmonth   = $begin - 1;
    }
    else {
        $prevquarter = 4;
        $prevyear    = ( $year - 1 );
        $prevmonth   = 12;
    }

    // next quarter
    if ( ( $quart + 1 ) > 4 ) {
        $nextquarter = 1;
        $nextyear    = ( $year + 1 );
        $nextmonth   = 1;
    }
    else {
        $nextquarter = $quart + 1;
        $nextyear    = $year;
        $nextmonth   = $begin + 1;
    }

    $lastquarter = "<a href=\"".$settings['base_url']."?year=$prevyear&amp;quart=$prevquarter\">&laquo; Q".$prevquarter."</a>";
    $nextquarter = "<a href=\"".$settings['base_url']."?year=$nextyear&amp;quart=$nextquarter\">Q".$nextquarter." &raquo;</a>";

    echo "<!-- begin div bookings_yearnav --><div class=\"bookings_yearnav\">\n",
             "<span class=\"bookings_left\">$lastquarter</span>\n",
             "<span class=\"bookings_right\">$nextquarter</span>\n",
             "<br class=\"bookings_yearnav_clear\" /><br />\n";

    for ( $m = $begin; $m < $end; $m++ ) {
        if ( $m % $set['breakafter'] == 1 ) {
            echo "<br class=\"bookings_clear\" />\n";
        }
        echo Bookings_Month_Sheet( $year, $m, $section_id, false );
    }

    echo "</div>";

    $prev_ts   = mktime( 0, 0, 0, ($begin-1), 1, $year );
    $next_ts   = mktime( 0, 0, 0, ($begin+1), 1, $year );

    $prevmonth = date( 'm', $prev_ts );
    $prevyear  = date( 'Y', $prev_ts );
    $nextmonth = date( 'm', $next_ts );
    $nextyear  = date( 'Y', $next_ts );


    // one month back
    echo "<span class=\"bookings_left\">",
         "<a href=\"".$settings['base_url']."?year=$prevyear&amp;month=$prevmonth&amp;monthplus=1\">",
         $MOD_BOOKINGS['ONE_MONTH_BACK'],
         "</a></span>\n";
    // one month ahead
    echo "<span class=\"bookings_right\">",
         "<a href=\"".$settings['base_url']."?year=$nextyear&amp;month=$nextmonth&amp;monthplus=1\">",
         $MOD_BOOKINGS['ONE_MORE_MONTH'],
         "</a></span>\n";

}   // end function Bookings_Show_Quarter()

/*

*/
function Bookings_Three_Months( $year, $beginmonth, $section_id )
{

    global $MOD_BOOKINGS, $set, $settings;

    $end       = ( $beginmonth + 3 );

    $prev_ts   = mktime( 0, 0, 0, ($beginmonth-1), 1, $year );
    $next_ts   = mktime( 0, 0, 0, ($beginmonth+1), 1, $year );

    $prevmonth = date( 'm', $prev_ts );
    $prevyear  = date( 'Y', $prev_ts );
    $nextmonth = date( 'm', $next_ts );
    $nextyear  = date( 'Y', $next_ts );

    for ( $m = $beginmonth; $m < $end; $m++ ) {
        if ( $m > 12 ) {
            $year++;
            $m   = 1;
            $end = ( $beginmonth + 3 ) - 12;
        }
        if ( $set['breakafter'] > 0 && $m % $set['breakafter'] == 1 ) {
            echo "<br class=\"bookings_clear\" />\n";
        }
        echo Bookings_Month_Sheet( $year, $m, $section_id, false );
    }

    echo "<br class=\"bookings_clear\" />\n";

    // one month back
    echo "<span class=\"bookings_left\">",
         "<a href=\"".$settings['base_url']."?month=$prevmonth",
         "&amp;year=$prevyear",
         "&amp;monthplus=1\">",
         $MOD_BOOKINGS['ONE_MONTH_BACK'],
         "</a></span>\n";

    echo "<span class=\"bookings_right\">",
         "<a href=\"".$settings['base_url']."?month=$nextmonth",
         "&amp;year=$nextyear",
         "&amp;monthplus=1\">",
         $MOD_BOOKINGS['ONE_MORE_MONTH'],
         "</a></span>\n";

}

/**
 * group view
 **/
function Bookings_View_ByGroup( $year, $month, $day, $section_id, $set )
{
    global $MOD_BOOKINGS;

    $seen = array();

    $bookings = _GetBookingsForDay( $year, $month, $day, $section_id, 'sheet' );
    $groups   = _GetBookingsGroups( $section_id );

    $r = "<table class=\"bookings_sheet bookings_bygroup\">\n";

    foreach ( $groups as $group ) {

        array_splice( $seen, 0 );

        $css  = "bookings_daysheet_free";
        $txt  = '';
        $r   .= "<tr><td class=\"mod_bookings_left\">".$group['name']."</td>\n";

        reset($bookings);

        foreach ( $bookings as $item ) {

            if ( $item['group_id'] === $group['group_id'] ) {
                if ( empty ( $seen[ $item['what'] ] ) ) {
                    $css = "bookings_partially";
                    if ( $item['fullday'] == 1 ) {
                        $css = "bookings_booked";
                    }
                    $txt .= $item['time'] . " " . $item['what'] . "<br />\n";
                }
                $seen[ $item['what'] ] = 1;
            }
        }
        $r .=  "<td class=\"$css\">&nbsp;</td>\n"
           .   "<td>$txt</td></tr>\n";
    }

    return $r . "</table>\n";

}

/**
 * check date overlap
 **/
function Bookings_DateOverlap ( $begin, $end, $section_id, $bookings_id, $return_booked = 0 )
{

    global $database, $MOD_BOOKINGS;
    $return = '';

    if ( $begin === $end )
    {
        $temp = date_parse( $end );
        $end  = sprintf( "%04d-%02d-%02d 23:59:59",
                         $temp['year'], $temp['month'], $temp['day']
                );
    }

    // very new statement
    // IF( COUNT(1),'no','yes' ) as available
    $sql = "SELECT *
FROM ".TABLE_PREFIX."mod_bookings_dates
WHERE section_id = '$section_id' AND bookings_id <> '$bookings_id'
  AND begindate < '$end'
  AND enddate   > '$begin'
";

#echo "SQL: $sql<br />\n";
    $result = $database->query(
        $sql
    );

    while ( $row = $result->fetchRow() )
    {

        if ( $return_booked != 0 ) {

            $begindate = date_parse( $row['begindate'] );
            $enddate   = date_parse( $row['enddate'] );
            $dateformat = $MOD_BOOKINGS['DEFAULT_DATEFORMAT'];

            $return .= "  <tr>\n"
                    .  "    <td>"
                    .  strftime( $dateformat.' %H:%M', mktime( $begindate['hour'], $begindate['minute'], 0, $begindate['month'], $begindate['day'], $begindate['year']) )
                    .  "    </td>\n"
                    .  "    <td>"
                    .  strftime( $dateformat.' %H:%M', mktime( $enddate['hour'], $enddate['minute'], 0, $enddate['month'], $enddate['day'], $enddate['year']) )
                    .  "    </td>\n    <td>".$row['name']."</td>"
                    .  "  </tr>";
        }
        else {
            return;
        }

    }

    if ( $return && $return_booked != 0 )
    {
        return "<table>".$return."</table>\n";
    }
}

/*
    show available booking dates
*/
function Bookings_Show_List ( $section = '' ) {

    global $MOD_BOOKINGS, $TEXT, $page_id, $section_id, $database, $admin;

    if ( isset( $section_id ) ) {
        $section = $section_id;
    }

    // check for valid section id (must be a number)
    if ( ! is_numeric( $section ) ) {
        return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'], 'section_id ['.$section.']' );
    }

    // check for valid bookings id (must be a number)
    $id = NULL;
    if ( isset ( $_REQUEST['bookings_id'] ) ) {
        if ( is_numeric ( $_REQUEST['bookings_id'] ) ) {
            $id  = $_REQUEST['bookings_id'];
        }
        else {
            return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'], 'bookings_id' );
        }
    }

    if ( ! isset( $admin ) OR get_class($admin) != 'admin' ) {
        echo "<script language=\"javascript\" type=\"text/javascript\">\n",
             "  function confirm_link(message, url) {\n",
	           "    if(confirm(message)) location.href = url;\n",
             "  }\n</script>\n";
    }

    if (
        isset ( $_REQUEST['togglevis'] )
        &&
        Bookings_user_can( 'mod', $id, $section )
    ) {

        $r   = $database->query("SELECT hidename FROM `".TABLE_PREFIX."mod_bookings_dates`
                                 WHERE page_id = '$page_id' AND section_id = '$section' AND bookings_id = '$id'" );
        $vis = $r->fetchRow();

        $new_vis = ( $vis[0] === 'y' ) ? 'n' : 'y';

        $database->query( "UPDATE `".TABLE_PREFIX."mod_bookings_dates` SET hidename = '$new_vis'
                           WHERE page_id = '$page_id' AND section_id = '$section' AND bookings_id = '$id'"
                   );
    }

    $hidden_img  = '<img src="' . THEME_URL . '/images/hidden_16.png" alt="'  . $TEXT['HIDDEN'] . '" title="' . $TEXT['HIDDEN'] . '" />';
    $visible_img = '<img src="' . THEME_URL . '/images/visible_16.png" alt="' . $TEXT['PUBLIC'] . '" title="' . $TEXT['PUBLIC'] . '" />';

    $sql = "SELECT bookings_id, begindate, enddate, dates.name as name, hidename, state, groups.name as groupname
        FROM ".TABLE_PREFIX."mod_bookings_dates as dates
        LEFT OUTER JOIN ".TABLE_PREFIX."mod_bookings_groups as groups
            ON dates.group_id = groups.group_id
        WHERE dates.section_id = '$section'
        ORDER BY begindate, enddate";
    $query_dates = $database->query( $sql );

    $caller = debug_backtrace();

    // [Options] button
    if ( Bookings_user_can( 'admin', '', $section  ) ) {

#        if ( basename( $caller[0]['file'] ) === 'modify.php' ) {
#            // called from backend
#            $action = WB_URL."/modules/bookings_v2/modify_settings.php";
#        }
#        else {
            $action = $_SERVER['SCRIPT_NAME'];
#        }
        $ftan = ( version_compare( WB_VERSION, "2.8.2", '>=' ) ? $admin->getFTAN() : NULL );

        echo "<table style=\"width: 100%;\">\n<tr>\n<td>\n",
             "<form method=\"post\" action=\"$action?page_id=".$page_id."\">\n",
             $ftan,
             "<input type=\"hidden\" name=\"page_id\" value=\"".$page_id."\" />\n",
             "<input type=\"hidden\" name=\"settings\" value=\"1\" />\n",
             "<input type=\"hidden\" name=\"section_id\" value=\"$section\" />\n",
             "<input type=\"submit\" value=\"",
             $TEXT['SETTINGS'],
             "\" style=\"width: 100%;\" />\n</form>\n</td>\n";

        echo "<td style=\"text-align: right;\">\n",
             "<form method=\"post\" action=\"".$_SERVER['SCRIPT_NAME']."?page_id=".$page_id."\">\n",
             $ftan,
             "<input type=\"hidden\" name=\"editgroups\" value=\"1\" />\n",
             "<input type=\"hidden\" name=\"page_id\" value=\"$page_id\" />\n",
             "<input type=\"hidden\" name=\"section_id\" value=\"$section\" />\n",
             "<input type=\"submit\" value=\"",
             $MOD_BOOKINGS['GROUPS'],
             "\" style=\"width: 100%;\" />\n</form>\n",
             "</td>\n</tr>\n</table>\n<br />\n";

    }

    echo "<table class=\"mod_bookings_table\">\n";

    if ($query_dates->numRows() > 0) {

        $dateformat = $MOD_BOOKINGS['DEFAULT_DATEFORMAT'];

        while( $result = $query_dates->fetchRow() ) {

            $begindate = date_parse( $result['begindate'] );
            $enddate   = date_parse( $result['enddate'] );

            $img = $visible_img;
            if ( $result['hidename'] == 'y' ) {
                $img = $hidden_img;
            }

            if (
                Bookings_user_can( 'mod', $result['bookings_id'], $section )
            ) {
                $img = "<a href=\""
                     . $_SERVER['SCRIPT_NAME']
                     . "?page_id=$page_id&section_id=$section&bookings_id="
                     . $result['bookings_id']
                     . "&togglevis=1&modify=1\">$img</a>";
            }

            if ( empty( $lastyear ) || $begindate['year'] <> $lastyear ) {
                echo "<tr><th class=\"row_a\" colspan=\"7\">".$begindate['year']."</th></tr>\n";
?>
        <tr>
          <th><?php echo $MOD_BOOKINGS['STATE']; ?></th>
          <th><?php echo $MOD_BOOKINGS['BEGINDATE']; ?></th>
          <th><?php echo $MOD_BOOKINGS['ENDDATE']; ?></th>
          <th>&nbsp;</th>
          <th><?php echo $MOD_BOOKINGS['NAME']; ?></th>
          <th><?php echo $MOD_BOOKINGS['GROUP']; ?></th>
          <th><?php echo $MOD_BOOKINGS['ACTIONS']; ?></th>
        </tr>
<?php
            }
            $lastyear = $begindate['year'];

            echo "  <tr>\n",
                 "    <td>",
                 "      <span class=\"bookings_".$result['state']."\">&nbsp;&nbsp;</span>",
                 "    </td>\n",
                 "    <td>",
                 strftime( $dateformat.' %H:%M', mktime( $begindate['hour'], $begindate['minute'], 0, $begindate['month'], $begindate['day'], $begindate['year']) ),
                 "    </td>\n",
                 "    <td>",
                 strftime( $dateformat.' %H:%M', mktime( $enddate['hour'], $enddate['minute'], $enddate['second'], $enddate['month'], $enddate['day'], $enddate['year']) ),
                 "    </td>\n    <td>$img</td>\n",
                 "    <td>".$result['name']."</td>",
                 "    <td>".$result['groupname']."</td>",
                 "    <td>";

            if (
                Bookings_user_can( 'mod', $result['bookings_id'], $section )
            ) {

                $file = NULL;

                if ( isset( $caller[1] ) ) {
                    $file = basename( $caller[1]['file'] );
                }
                elseif ( isset( $caller[0] ) ) {
                    $file = basename( $caller[0]['file'] );
                }

                if ( $file && $file === 'modify.php' ) {
                    // called from backend
                    $action = WB_URL."/modules/bookings_v2/modify_bookings.php";
                }
                else {
                    $action = $_SERVER['SCRIPT_NAME'];
                }

                echo
                 "        <a href=\"$action?edit=1&amp;page_id=$page_id&amp;section_id=$section&amp;bookings_id=", $result['bookings_id'], "\"",
                 "           title=\"", $TEXT['MODIFY'], "\">",
                 "        <img src=\"", THEME_URL, "/images/modify_16.png\" border=\"0\"",
                 "           alt=\"", $TEXT['MODIFY'], "\" /></a>";

            }

            if (
                Bookings_user_can( 'del', $result['bookings_id'], $section )
            ) {

                if ( basename( $caller[0]['file'] ) === 'modify.php' ) {
                    // called from backend
                    $action = ADMIN_URL."/pages/modify.php";
                }
                else {
                    $action = $_SERVER['SCRIPT_NAME'];
                }

                echo
                 "        <a href=\"javascript: confirm_link('",
                 $TEXT['ARE_YOU_SURE'],
                 "', '",
                 "$action?delete=1&page_id=$page_id&section_id=$section&bookings_id=",
                 $result['bookings_id'],
                 "');\"\n",
                 "           title=\"", $TEXT['DELETE'], "\">\n",
                 "        <img src=\"", THEME_URL, "/images/delete_16.png\" border=\"0\" alt=\"",
                 $TEXT['DELETE'],
                 "\" /></a>\n";
            }

            echo
                 "    </td>\n",
                 "  </tr>\n";
        }

    }

    if ( Bookings_user_can( 'add', '', $section  ) )
    {

        if ( basename( $caller[0]['file'] ) === 'modify.php' ) {
            // called from backend
            $action = WB_URL."/modules/bookings_v2/add_bookings.php";
        }
        else {
            $action = $_SERVER['SCRIPT_NAME'];
        }

        echo
             "  <tr><td colspan=\"7\" style=\"text-align: right;\">\n",
             "        <a href=\"$action?page_id=$page_id&section_id=$section&add=1\"\n",
             "           title=\"", $TEXT['ADD'], "\">", $TEXT['ADD'], "\n",
             "        <img src=\"", THEME_URL, "/images/plus_16.png\" border=\"0\"\n",
             "           alt=\"", $TEXT['ADD'], "\" /></a>\n",
             "  </td></tr>\n";

    }

    echo "  </table>\n<br /><br />\n";

    if ( ! isset($file) || ! $file || $file != 'modify.php' ) {
        // back button
        echo "<form method=\"post\" action=\"".$_SERVER['SCRIPT_NAME']."\">\n",
             ( version_compare( WB_VERSION, "2.8.2", '>=' ) ? $admin->getFTAN() : '' ),
             "<input type=\"submit\" value=\"&laquo; ".$TEXT['BACK']."\" />\n</form>";
    }

}   // end function Bookings_Show_List ()

/*

*/
function Bookings_Form( $what, $date, $year, $show_hide_selected, $section )
{
    global $MOD_BOOKINGS;

    $CurrYear                          = date("Y");
    $month_selected[$date['month']]    = 'selected="selected"';
    $year_selected[$date['year']]      = 'selected="selected"';
    $day_selected[$date['day']]        = 'selected="selected"';

    $output = "
    <tr>
        <td class=\"mod_bookings_left\">" . $MOD_BOOKINGS[ strtoupper ($what) . 'DATE'] . "</td>
        <td class=\"mod_bookings_right\">
            <select onchange=\"checkDate();return false;\" class=\"small\" name=\"" . $what . "day\" id=\"" . $what . "day\">
";

    // day select
    for ( $d = 1; $d <= 31; $d++ ) {
        $output .= "<option value=\"$d\" "
                .  ( isset($day_selected[$d]) ? $day_selected[$d] : '' )
                .  ">$d</option>\n";
    }

    $output .= "
            </select>&nbsp;
            <select onchange=\"checkDate();return false;\" name=\"" . $what . "month\" id=\"" . $what . "month\">
";

    // month select
    for ( $m = 1; $m <= 12; $m++ ) {
        $output .= "<option value=\"$m\" "
                .  ( isset($month_selected[$m]) ? $month_selected[$m] : '' )
                .  "> "
                .  $MOD_BOOKINGS['MONTHNAMES'][$m]
                .  "</option>\n";
    }

    $output .= "
            </select>&nbsp;
            <select onchange=\"checkDate();return false;\" class=\"small\" name=\"" . $what . "year\" id=\"" . $what . "year\">
";

    // year select
    for ( $y = $CurrYear; $y <= ( $CurrYear + 5 ); $y++ ) {
        $output .= "<option value=\"$y\" "
                .  ( isset($year_selected[$y]) ? $year_selected[$y] : '' )
                .  "> $y</option>\n";
    }

    $output .= "
            </select>&nbsp;
            <a href=\"#\" onClick=\"".$what."date.showCalendar('".$what."date'); return false;\" title=\"Calendar\" name=\"" . $what . "date\" id=\"" . $what . "date\">
              <img src=\""
            .  WB_URL
            .  "/modules/bookings_v2/calendar.png\" alt=\"Calendar\" style=\"border: 0\" />
            </a>
        </td>
        <td>"
            . Bookings_TimesForm ( $what, $date, $section )
            . "
        </td>
    </tr>\n";

    return $output;
}

function Bookings_TimesForm ( $what, $date, $section ) {

    global $MOD_BOOKINGS;

    $year_selected[$date['year']]     = 'selected="selected"';
    if ( $date['hour'] == 23 && $date['minute'] == 59 ) {
        $hour_selected[0]             = 'selected="selected"';
    }
    else {
        $hour_selected[$date['hour']] = 'selected="selected"';
    }

    $output = "
        <span class=\"bookings_toggle_time\">
            <select onchange=\"checkDate();return false;\" class=\"small\" name=\"" . $what . "hour\">
";

    for ( $d = 0; $d <= 23; $d++ ) {
        $output .= "<option value=\"$d\" "
                .  ( isset($hour_selected[$d]) ? $hour_selected[$d] : '' )
                .  ">$d</option>\n";
    }

    $output .= "
            </select>&nbsp;:
            <select onchange=\"checkDate();return false;\" class=\"small\" name=\"" . $what . "minute\">"
            . _getMinutes( $date['minute'], $section )
            . "
            </select>&nbsp;
        </span>
";

    return $output;
}

/*
    create a breadcrumb
*/
function Bookings_create_Breadcrumb ( $stop = 'month' ) {

    global $day, $month, $week, $year, $quart, $monthplus, $delim, $MOD_BOOKINGS, $settings;

    $bread = array();
    $get   = array();

    $quart = ( empty($quart) ) ? intval(( date("m") - .1)/3 + 1) : $quart;

    foreach ( array( 'year', 'mplus', 'quart', 'month', 'week', 'day' ) as $item ) {

        // just to have a valid breadcrumb on "monthplus"
        if ( $item == 'mplus' ) { break; }

        $value = NULL;

        $ret = eval( '$value = $'.$item.';' );

        if ( $item == $stop ) {
            $bread[] = "<strong>" . $MOD_BOOKINGS[strtoupper($item)] . ": $value</strong>";
            break;
        }

#echo "STOP $stop ITEM $item RET $ret VALUE $value<br />";

        if ( empty( $value ) ) {
            continue;
        }

        $get[]   = "$item=$value";
        $bread[] = "<a href=\"".$settings['base_url']."?".implode('&amp;',$get)."\">".$MOD_BOOKINGS[strtoupper($item)].": $value</a>";
    }

    return $delim . ' ' . implode( $delim, $bread ) . "<br /><br />\n";

}   // end function Bookings_create_Breadcrumb ()

/*
    add new entry
*/
function Bookings_add_Entry ( $section = '' ) {

    global $page_id, $database, $wb, $admin, $TEXT, $MOD_BOOKINGS;

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    if ( ! Bookings_user_can( 'add', '', $section ) ) {
        $class->print_error(
            $MOD_BOOKINGS['ERR_PERMISSION'],
            '?page_id='.$page_id
        );
    }

    unset($_REQUEST['add']);

    // The booking is no longer added at this point. It will be added
    // after clicking [Save] in the bookings form.
    Bookings_edit_Entry ( 0, $section );

}   // end function Bookings_add_Entry()

/*
    Edit entry
*/
function Bookings_edit_Entry ( $bookings_id = '', $section = '' ) {

    global $database, $page_id, $wb, $admin, $MOD_BOOKINGS, $TEXT;

    $users = array();
    $url   = WB_URL.'/modules/bookings_v2/ajax_check_date.php';

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    // add new?
    if ( $bookings_id == 0 )
    {
        if ( ! Bookings_user_can( 'add', '', $section ) ) {
            $class->print_error(
                $MOD_BOOKINGS['ERR_PERMISSION'],
                '?page_id='.$page_id
            );
        }

        $dates['begindate'] = date('Y-m-d 00:00:00', time() );
        $dates['enddate']   = date('Y-m-d 00:00:00', time() );
        $dates['owner_id']  = $_SESSION['USER_ID'];
        $dates['name']      = '';
        $dates['hidename']  = 'y';
        $dates['state']     = 'booked';

    }
    else {

        if ( Bookings_user_can( 'mod', $bookings_id, $section ) != 1 ) {
            $class->print_error(
                $MOD_BOOKINGS['ERR_PERMISSION'],
                ADMIN_URL.'?page_id='.$page_id
            );
        }

        // Get Data from database
        $result      = $database->query(
            "SELECT * FROM ".TABLE_PREFIX."mod_bookings_dates
            WHERE bookings_id = '$bookings_id'"
        );
        $dates       = $result->fetchRow();

    }

    // for old bookings (before v2.23)
    if ( empty ( $dates['state'] ) ) {
        $dates['state'] = 'booked';
    }

    if ( Bookings_user_can( 'admin', '', $section ) ) {
        $result = $database->query("SELECT * FROM ".TABLE_PREFIX.'users');
        while( $row = $result->fetchRow() ) {
            $users[] = $row;
        }
    }

    // Get Settings from database
    $settings    = _Bookings_Settings($section);

    $caller = debug_backtrace();
    if ( isset( $caller[1] ) && basename( $caller[1]['file'] ) === 'add_bookings.php' ) {
        // called from backend
        $action = ADMIN_URL."/pages/modify.php?page_id=$page_id";
    }
    elseif ( isset ( $caller[0] ) && basename( $caller[0]['file'] ) === 'modify_bookings.php' ) {
        // called from backend
        $action = ADMIN_URL."/pages/modify.php?page_id=$page_id";
    }
    else {
        $action = $_SERVER['SCRIPT_NAME']."?section_id=$section";
    }

?>

<form name="modify" action="<?php echo $action; ?>" method="post" style="margin: 0;">
<?php ( version_compare( WB_VERSION, "2.8.2", '>=' ) ? $admin->getFTAN() : '' ); ?>
<input type="hidden" id="section_id" name="section_id" value="<?php echo $section; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="bookings_id" value="<?php echo $bookings_id; ?>" />

<?php

    //Array ( [year] => 2008 [month] => 7 [day] => 31 [hour] => 9 [minute] => 0 [second] => 0 [fraction] => 0 [warning_count] => 0 [warnings] => Array ( ) [error_count] => 0 [errors] => Array ( ) [is_localtime] => )
    $begindate = date_parse( $dates['begindate'] );
    $enddate   = date_parse( $dates['enddate'] );

    $begindate['day']   = sprintf( '%d', $begindate['day'] );
    $begindate['month'] = sprintf( '%d', $begindate['month'] );

    $name      = $dates['name'];

    $show_hide_selected[$dates['hidename']] = 'checked="checked"';

    for ( $i=1; $i<=12; $i++ ) {
        $monthnames[] = htmlentities( strftime( '%B', mktime(0,0,0,($i+1),0,$begindate['year']) ) );
    }

    $daylong = '';
    if ( $begindate['hour'] == 0  && $begindate['minute'] == 0
      &&   $enddate['hour'] == 23 &&   $enddate['minute'] == 59 )
    {
        $daylong = 'checked="true"';
    }

?>

<script src="<?php echo WB_URL; ?>/modules/bookings_v2/CalendarPopup.js" type="text/javascript"></script>
<script type="text/javascript">
    var begindate = new CalendarPopup();
    var enddate   = new CalendarPopup();
    var url = "<?php echo $url?>";

    begindate.setTodayText(" <?php echo $MOD_BOOKINGS['TODAY']; ?>");
    // translated month names and day abbr.
    begindate.setMonthNames( "<?php echo join('", "', $monthnames ); ?>" );
    begindate.setDayHeaders( "<?php echo $MOD_BOOKINGS['SHORTDAYNAMES'][6]; ?>", "<?php echo join('", "', array_slice( $MOD_BOOKINGS['SHORTDAYNAMES'], 0, 6 ) ); ?>" );

    // same for end select
    enddate.setMonthNames( "<?php echo join('", "', $monthnames ); ?>" );
    enddate.setDayHeaders( "<?php echo $MOD_BOOKINGS['SHORTDAYNAMES'][6]; ?>", "<?php echo join('", "', array_slice( $MOD_BOOKINGS['SHORTDAYNAMES'], 0, 6 ) ); ?>" );

<?php
    // disable dates?
    if ( $settings['showpast'] != 'y' ) {
        $today = getdate();
        $js_begin = $today['year'].'-'.$today['mon'].'-'.($today['mday']-1);
?>
    // Pass null as the first parameter to mean "anything up to and including" the
    // passed date:
    begindate.addDisabledDates(null, "<?php echo $js_begin; ?>");
    enddate.addDisabledDates(null, "<?php echo $js_begin; ?>");
<?php
    }
?>

</script>
<script src="<?php echo WB_URL; ?>/modules/bookings_v2/CalendarAJAX.js" type="text/javascript"></script>

  <?php echo $MOD_BOOKINGS['INFO'] ?><br /><br />

  <div id="ajax_response" class="mod_bookings_ok" style="display: none;">ok</div>

  <table class="mod_bookings_table" cellpadding="2" cellspacing="0" border="0" align="center" width="100%" style="margin-top: 5px;">
  <tr>
  	<td colspan="3" class="row_a"><strong><?php echo $MOD_BOOKINGS['MODIFY_HEADER']; ?> (Page: <?php echo $page_id; ?> / Section: <?php echo $section; ?> / <?php echo $MOD_BOOKINGS['ID']; ?>:  <?php echo $bookings_id; ?>)</strong></td>
  </tr>

<?php
    $year = date("Y");
    echo Bookings_Form( 'begin', $begindate, $year, $show_hide_selected, $section );
    echo Bookings_Form( 'end',   $enddate,   $year, $show_hide_selected, $section );
?>

    <tr>
        <td class="mod_bookings_left"><?php echo $MOD_BOOKINGS['DAYLONG'] ?></td>
        <td class="mod_bookings_right" colspan="2">
            <input type="checkbox" id="daylong" name="daylong" <?php echo $daylong; ?> onchange="toggleDaylong('<?php echo $url ?>'); return false;" />
        </td>
    </tr>
    <tr>
        <td class="mod_bookings_left">
            <?php echo $MOD_BOOKINGS['STATE']; ?>
        </td>
        <td class="mod_bookings_right" colspan="2">
            <input type="radio" name="state" value="reserved" <?php echo ( ($dates['state']=='reserved') ? 'checked="checked"' : '' ) ?> /> <?php echo $MOD_BOOKINGS['STATE_RESERVED']; ?>
            <input type="radio" name="state" value="booked" <?php echo ( ($dates['state']=='booked') ? 'checked="checked"' : '' ) ?> /> <?php echo $MOD_BOOKINGS['STATE_BOOKED']; ?>
        </td>
    </tr>
    <tr>
        <td class="mod_bookings_left">
            <?php echo $MOD_BOOKINGS['NAME']; ?>
        </td>
        <td class="mod_bookings_right" colspan="2">
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" style="width: 200px;" /><br />
            <input type="radio" name="hidename" value="y" <?php echo ( isset($show_hide_selected['y']) ? $show_hide_selected['y'] : '' ) ?> />
              <?php echo $MOD_BOOKINGS['HIDENAME']; ?>
            <input type="radio" name="hidename" value="n" <?php echo ( isset($show_hide_selected['n']) ? $show_hide_selected['n'] : '' ) ?> />
              <?php echo $MOD_BOOKINGS['SHOWNAME']; ?>
        </td>
    </tr>
    <tr>
        <td class="mod_bookings_left">
            <?php echo $MOD_BOOKINGS['GROUP']; ?>
        </td>
        <td class="mod_bookings_right" colspan="2">
            <select id="group" name="group">
            <option value="0"><?php echo $TEXT['NONE']; ?></option>;
<?php
    $groups = _GetBookingsGroups( $section );
    foreach ( $groups as $group ) {
        $flag_checked = '';
        if ( isset($group['group_id']) && isset($dates['group_id']) && $group['group_id'] == $dates['group_id'] ) {
    		    $flag_checked  = ' selected="selected"';
    		}
        echo "<option value=\"", $group['group_id'], "\" $flag_checked>", $group['name'], "</option>\n";
    }
?>
            </select>
        </td>
    </tr>

<?php

    if ( Bookings_user_can( 'admin', '', $section  ) ) {

        echo "    <tr>\n      <td class=\"mod_bookings_left\">\n",
             $MOD_BOOKINGS['OWNER'],
             "      </td>\n",
             "      <td>\n",
             "        <select name=\"owner_id\">\n";

        foreach ( $users as $user ) {

            $sel = '';

            if ( $user['user_id'] == $dates['owner_id'] ) {
                $sel = ' selected="selected"';
            }

            echo "          <option value=\"",
                 $user['user_id'],
                 "\" $sel>",
                 $user['username'],
                 "</option>\n";

        }

        echo "        </select>\n      </td>\n    </tr>\n";

    }

?>
    <tr>
        <td class="mod_bookings_left row_b">
            <input name="save" id="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" />
        </td>
        <td class="mod_bookings_left row_b" style="text-align: right;" colspan="2">
    			  <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo $action; ?>&amp;modify=1';" />
    		</td>
    </tr>
</table>

</form>

<script type="text/javascript">CalCheckDate();</script>

<?php
}

/*
    save entry to DB
*/
function Bookings_save_Entry ( $bookings_id = '', $section = '' ) {

    global $wb, $admin, $page_id, $database,
           $settings, $debug, $TEXT, $MOD_BOOKINGS;

    // check for numeric $bookings_id
    if ( ! preg_match( "/^(\d+)$/", $bookings_id ) ) {
        return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'], 'bookings_id' );
    }

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
        require_once(WB_PATH.'/framework/class.wb.php');
    }

    // Validate all fields
    $beginyear   = $class->get_post_escaped('beginyear');
    $beginmonth  = $class->get_post_escaped('beginmonth');
    $beginday    = $class->get_post_escaped('beginday');
    $beginhour   = $class->get_post_escaped('beginhour');
    $beginminute = $class->get_post_escaped('beginminute');
    $endyear     = $class->get_post_escaped('endyear');
    $endmonth    = $class->get_post_escaped('endmonth');
    $endday      = $class->get_post_escaped('endday');
    $endhour     = $class->get_post_escaped('endhour');
    $endminute   = $class->get_post_escaped('endminute');
    $hidename    = $class->get_post_escaped('hidename');
    $owner       = $class->get_post_escaped('owner_id');
    $group       = $class->get_post_escaped('group');
    $state       = $class->get_post_escaped('state');

    // do not allow HTML or <script> in name!
    $name        = __strip( strip_tags( $class->get_post_escaped('name') ) );

    if ( empty ( $owner ) ) {
        $owner    = $_SESSION['USER_ID'];
    }

    // check for valid items
    if (
           ! is_numeric( $owner )
        || ! is_numeric( $beginyear )
        || ! is_numeric( $beginmonth )
        || ! is_numeric( $beginday )
        || ! is_numeric( $beginminute )
        || ! is_numeric( $beginhour )
        || ! is_numeric( $endyear )
        || ! is_numeric( $endmonth )
        || ! is_numeric( $endday )
        || ! is_numeric( $endhour )
        || ! is_numeric( $endminute )
    ) {
        return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'] );
    }

    // group is optional
    if ( ! empty ( $group ) && ! is_numeric( $group ) ) {
        return Bookings_Error( $MOD_BOOKINGS['ERR_INVALID_PARAM'], 'group' );
    }

    // validate hidename
    $hidename = ( $hidename === 'y' ) ? 'y' : 'n';

    $begin = mktime($beginhour, $beginminute, 0, $beginmonth, $beginday, $beginyear);
    $end   = mktime($endhour  , $endminute  , 0, $endmonth  , $endday  , $endyear  );

    if ( $begin == $end ) // whole day
    {
        $end   = mktime( 23, 59, 59, $endmonth, $endday, $endyear );
    }

    if ( $endhour == 0 && $endminute == 0 )
    {
        $end   = mktime( 23, 59, 59, $endmonth, $endday, $endyear );
    }

    if ( $end < $begin ) {
        $class->print_error(
            $MOD_BOOKINGS['ERR_DATES'],
            '?page_id='.$page_id.'&section_id='.$section.'&bookings_id='.$bookings_id
        );
    }

    // send the posted date through date() to get a correct date
    $begindate = date( 'Y-m-d H:i:s', $begin );
    $enddate   = date( 'Y-m-d H:i:s', $end   );

    // check for date overlap
    if ( Bookings_DateOverlap( $begindate, $enddate, $section, $bookings_id ) ) {
        $url = '?page_id='
             . $page_id
             . '&amp;section_id='
             . $section
             . '&amp;bookings_id='
             . $bookings_id;
        $class->print_error( $MOD_BOOKINGS['ERR_DATES_OVERLAP'], $url );
    }

    // add new?
    $mail_to_admin = '';
    if ( $bookings_id == 0 ) {

        $sql = "INSERT INTO ".TABLE_PREFIX."mod_bookings_dates
    VALUES ( '$page_id',
             '$section',
             NULL,
             '$begindate',
             '$enddate',
             '$name',
             '$hidename',
             '$owner',
             '$group',
             '$state' );";

        if ( ! empty ( $settings['admin_email'] ) ) {
            $mail_to_admin = $settings['admin_email'];
        }
    }
    else {
        // Update row
        $sql = "UPDATE ".TABLE_PREFIX."mod_bookings_dates
            SET page_id = '$page_id',
                section_id = '$section',
                begindate = '$begindate',
                enddate = '$enddate',
                name = '$name',
                hidename = '$hidename',
                owner_id = '$owner',
                group_id = '$group',
                state = '$state'
            WHERE bookings_id = '$bookings_id'";
    }

    if ( true === $debug ) {
        echo "SQL: $sql<br />\n";
    }

    $database->query(
        $sql
    );

    // Check if there is a db error, otherwise say successful
    if($database->is_error()) {
        return $database->get_error();
    }

    // mail to admin?
    if( $mail_to_admin != "" ) {
		    $mail_subject = $MOD_BOOKINGS['MAILSUBJECT'];
		    $mail_message = $MOD_BOOKINGS['MAILMESSAGE'].'<a href="'.page_link(  '/'. page_filename( get_menu_title($page_id)) ).'" target="_blank">'.page_link(  '/'. page_filename( get_menu_title($page_id)) ).'</a>';
			  $wb->mail(
            SERVER_EMAIL,
            $mail_to_admin,
            $mail_subject,
            $mail_message
        );
		}

    return;
}   // function Bookings_save_Entry

/*
    delete booking
*/
function Bookings_delete_Entry ( ) {

    global $wb, $admin, $database, $TEXT, $page_id;

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    // Get id
    if( ! isset ( $_GET['bookings_id'] ) OR ! is_numeric ( $_GET['bookings_id'] ) ) {
    	  header("Location: ".$_SERVER['SCRIPT_NAME']);
    } else {
    	  $bookings_id = $_GET['bookings_id'];
    }

    // Get post details
    $query_details = $database->query(
        "SELECT * FROM ".TABLE_PREFIX."mod_bookings_dates
        WHERE bookings_id = '$bookings_id'"
    );

    if( $query_details->numRows() > 0 ) {
    	  $get_details = $query_details->fetchRow();
    } else {
    	  $class->print_error(
            $TEXT['NOT_FOUND'],
            '?modify=1&page_id='.$page_id
       );
    }

    // Delete post
    $database->query(
        "DELETE FROM ".TABLE_PREFIX."mod_bookings_dates
        WHERE bookings_id = '$bookings_id' LIMIT 1"
    );

    // Check if there is a db error, otherwise say successful
    if($database->is_error()) {
    	  return $database->get_error();
    }

    unset($_REQUEST['delete']);
    unset($_REQUEST['bookings_id']);

    return;
}   // Bookings_delete_Entry

/*
    edit groups form
*/
function Bookings_edit_Groups( $section = '' ) {

    global $wb, $admin, $page_id, $database, $js_back, $TEXT, $MOD_BOOKINGS;

    $exist_item = '';
    $class      = $wb;

    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    if ( isset( $_REQUEST['cancel'] ) ) {
        return Bookings_Show_List( $_REQUEST['section_id'] );
    }

    if ( Bookings_user_can( 'admin', '', $section ) != 1 ) {
        $class->print_error(
            $MOD_BOOKINGS['ERR_PERMISSION'],
            '?page_id='.$page_id.'&section_id='.$section
        );
    }

    $groups = _GetBookingsGroups( $section );

    // group to delete?
    if ( isset( $_REQUEST['delgroup'] ) && is_numeric( $_REQUEST['delgroup'] ) ) {

        $group_id = $_REQUEST['delgroup'];
        $sql      = "DELETE FROM ".TABLE_PREFIX."mod_bookings_groups WHERE "
                  . "page_id = '$page_id' AND section_id = '$section' "
                  . "AND group_id = '$group_id'";

        $database->query( $sql );
        if($database->is_error()) {
        	  $class->print_error($database->get_error(), $js_back);
        }
        $groups = _GetBookingsGroups( $section );

    }

    $modified_id = $groupToEdit = $isModified = NULL;
    if ( isset( $_REQUEST['modified'] ) && is_numeric( $_REQUEST['modified'] ) ) {
        $modified_id = $_REQUEST['modified'];
    }

    if ( isset( $_REQUEST['editgroup'] ) && is_numeric( $_REQUEST['editgroup'] ) ) {
    	$groupToEdit = $_REQUEST['editgroup'];
    	$isModified = '&amp;modified='.$_REQUEST['editgroup'];
    }

    // new group to add?
    if ( ! empty ( $_POST['addgroup'] ) ) {

        $group_name = NULL;

        // check for valid group name
        if ( ! preg_match( "/([\w\s\.\-]+)/", $_POST['addgroup'], $matches ) ) {
            return Bookings_Error(
                $MOD_BOOKINGS['ERR_INVALID_PARAM'] . ' - invalid group name',
                'addgroup -'.$_POST['addgroup'].'-'
            );
        }
        else {
            $group_name = strip_tags( $_POST['addgroup'] );
        }

        // check for valid color code
        $color = NULL;
        if ( isset( $_POST['color'] ) && $_POST['color'] != '' ) {
            $color = $_POST['color'];
            // check for leading #
            if ( ! preg_match( '/^#/', $color ) ) {
                $color = '#'.$color;
            }
            // length should now be 4 or 7
            if ( strlen($color) != 4 && strlen($color) != 7 ) {
                return Bookings_Error(
                    $MOD_BOOKINGS['ERR_INVALID_PARAM'] . ' - color must be 4 or 7 characters long',
                    'color -'.$_POST['color'].'-'
                );
            }
            // check hex
            if ( ! preg_match( '/^#[0-9a-f]+$/i', $color ) ) {
                return Bookings_Error(
                    $MOD_BOOKINGS['ERR_INVALID_PARAM'] . ' - color ['.$color.'] must be a hex value',
                    'color -'.$_POST['color'].'-'
                );
            }
        }

        // check if group already exists
        foreach ( $groups as $group ) {
            if ( $group['name'] === $_POST['addgroup'] && $modified_id == NULL ) {
                $exist_item = $group['group_id'];
                echo "<div class=\"mod_bookings_fail\">",
                     $MOD_BOOKINGS['ERR_EXISTS'],
                     "</div>\n";
            }
        }

        if ( empty ( $exist_item ) ) {
            if ( $modified_id !== NULL ) {
     			$sql = "UPDATE ".TABLE_PREFIX."mod_bookings_groups SET "
	                 . "name = '$group_name', color = '$color' "
	                 . "WHERE group_id = ".$modified_id;
      		} else {
                $sql = "INSERT INTO ".TABLE_PREFIX."mod_bookings_groups VALUES( "
                     . "NULL, '$page_id', '$section', '$group_name', '$color'"
                     . ")";
            }

            $database->query( $sql );
            if($database->is_error()) {
            	  $class->print_error($database->get_error(), $js_back);
            }
            $groups = _GetBookingsGroups( $section );
        }

    }

    $caller = debug_backtrace();

    if ( basename( $caller[0]['file'] ) === 'modify.php' ) {
        // called from backend
        $action = ADMIN_URL."/pages/modify.php";
    }
    else {
        $action = $_SERVER['SCRIPT_NAME'];
    }

?>

<div id="mod_bookings">
  <script charset=windows-1250 src="<?php echo WB_URL; ?>/modules/bookings_v2/js/301a.js" type="text/javascript"></script>

  <form name="edit" action="<?php echo $action; ?>?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section; ?>&amp;editgroups=1<?php echo $isModified; ?>" method="post" style="margin: 0;">
<?php ( version_compare( WB_VERSION, "2.8.2", '>=' ) ? $admin->getFTAN() : '' ); ?>
  <input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
  <input type="hidden" name="section_id" value="<?php echo $section; ?>" />
  <input type="hidden" name="editgroups" value="1" />

  <h3><?php echo $MOD_BOOKINGS['GROUPS']; ?></h3>

  <table class="mod_bookings_table">
  <tr>
    <th><?php echo $MOD_BOOKINGS['GROUPNAME']; ?></th>
    <th><?php echo $MOD_BOOKINGS['GROUPCOLOR']; ?></th>
    <th><?php echo $MOD_BOOKINGS['GROUPMEMBERS']; ?></th>
    <th>&nbsp;</th>
  </tr>
<?php

    $oldName = '';
	$oldColor = '';
	$btnSaveText = $TEXT['SAVE'];

    foreach ( $groups as $group ) {

        $sql = "SELECT count(*) FROM ".TABLE_PREFIX."mod_bookings_dates
    WHERE page_id = '$page_id'
        AND section_id = '$section'
        AND group_id = '".$group['group_id']."'";

        $result = $database->query(
            $sql
        );

        $count = $result->fetchRow();

        $delete = '';
        if ( $count[0] == 0 ) {
            $delete = '<a href="'.$_SERVER['SCRIPT_NAME'].'?page_id='.$page_id.'&section_id='.$section.'&editgroups=1&delgroup='.$group['group_id'].'"'
                    . "title=\"".$TEXT['DELETE']."\">\n"
                    . "  <img src=\"".THEME_URL."/images/delete_16.png\" border=\"0\" alt=\""
                    . $TEXT['DELETE']
                    . "\" /></a>\n";
        }
        $modify  = '<a href="'.$_SERVER['SCRIPT_NAME'].'?page_id='.$page_id.'&section_id='.$section.'&editgroups=1&editgroup='.$group['group_id'].'"'
                 . "title=\"".$TEXT['MODIFY']."\">\n"
                 . "  <img src=\"".THEME_URL."/images/modify_16.png\" border=\"0\" alt=\""
                 . $TEXT['MODIFY']
                 . "\" /></a>\n";
        $bgcolor = ( isset($group['color']) && $group['color'] != '' )
                 ? $group['color']
                 : '#ffffff';
        if ($group['group_id'] === $groupToEdit) {
        	$oldName = $group['name'];
            $oldColor = $bgcolor;
			$btnSaveText = $TEXT['MODIFY'];
        }

?>
  <tr <?php if ( $exist_item == $group['group_id'] ) { echo "class=\"mod_bookings_fail\""; } ?>>
    <td><?php echo $group['name']; ?></td>
    <td><span style="width: 15px; display: inline-block; background-color: <?php echo $bgcolor; ?>;">&nbsp;</span> <?php echo $bgcolor; ?></td>
    <td><?php echo $count[0]; ?></td>
    <td><?php echo $modify.$delete; ?></td>
  </tr>
<?php
    }

?>
  <tr>
    <th colspan="3"><?php echo $TEXT['ADD']; ?></th>
  </tr>
  <tr>
    <td>Name: <input type="text" name="addgroup" value="<?php echo $oldName; ?>" /></td>
    <td>Farbe:
       <input type="text" class="colorsample"
              style="background-color: #ffffff;"
              id="colorsample"
              onclick="showColorGrid3('color','colorsample');"
       />
       <input type="text" size="9" class="small" id="color"
              name="color"
              value="<?php echo $oldColor; ?>"
              maxlength="7"
              onclick="showColorGrid3('color','colorsample');"
       />
    <td><input type="submit" value="<?php echo $btnSaveText; ?>" />
        <input type="submit" name="cancel" id="cancel" value="<?php echo $TEXT['BACK']; ?>" /></td>
  </tr>
  </table>

  </form>
  <DIV id='colorpicker301' class='colorpicker301'></div>
</div><!-- mod_bookings -->

<?php

}   // Bookings_edit_Groups

/*
    settings form
*/
function Bookings_edit_Settings ( $section = '' ) {

    global $wb, $admin, $page_id, $database, $TEXT, $MOD_BOOKINGS;
    global $admin_groups, $add_groups, $mod_groups, $del_groups;

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    if ( Bookings_user_can( 'admin', '', $section ) != 1 ) {
        $class->print_error(
            $MOD_BOOKINGS['ERR_PERMISSION'],
            '?page_id='.$page_id.'&section_id='.$section
        );
    }

    if ( isset( $_REQUEST['save'] ) ) {
        Bookings_save_Settings( $section );
    }

    $fetch          = _Bookings_Settings($section);

    $breakafter     = stripslashes($fetch['breakafter']);
    $stylesheet     = htmlentities( $fetch['stylesheet'] );
    $bookingsheader = htmlentities( $fetch['bookingsheader'] );
    $bookingsfooter = htmlentities( $fetch['bookingsfooter'] );
    $dateformat     = stripslashes($fetch['dateformat']);
    $dayview        = stripslashes($fetch['dayview']);
    $daysheetheader = stripslashes($fetch['daysheetheader']);
    $daystarthour   = stripslashes($fetch['daystarthour']);
    $dayendhour     = stripslashes($fetch['dayendhour']);
    $timeoffset     = stripslashes($fetch['timeoffset']);
    $admin_groups   = explode( ",", stripslashes($fetch['admin_groups']) );
    $add_groups     = explode( ",", stripslashes($fetch['add_groups'])   );
    $mod_groups     = explode( ",", stripslashes($fetch['mod_groups'])   );
    $del_groups     = explode( ",", stripslashes($fetch['del_groups'])   );
    $owner_edit     = stripslashes($fetch['owner_edit']);
    $default_view   = stripslashes($fetch['default_view']);
    $admin_email    = htmlentities( $fetch['admin_email'] );
    $showpast       = htmlentities( $fetch['showpast'] );
    $past_years     = stripslashes($fetch['past_years']);
    $next_years     = stripslashes($fetch['next_years']);
    $always_link    = stripslashes($fetch['always_link']);

    if ( empty ( $dayview ) ) { $dayview = 'list'; }

    // include edit css button
    if(function_exists('edit_module_css')) {
        global $section_id;
        $section_id = $section;
    	  edit_module_css('bookings_v2');
    }

    // cancel location
    $caller = debug_backtrace();
    if ( basename( $caller[0]['file'] ) === 'modify_settings.php' ) {
        // called from backend
        $cancel = ADMIN_URL."/pages/modify.php";
    }
    else {
        $cancel = $_SERVER['SCRIPT_NAME'];
    }
?>

<div id="mod_bookings">
  <form name="edit" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section; ?>" method="post" style="margin: 0;">
  <input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
  <input type="hidden" name="section_id" value="<?php echo $section; ?>" />

<?php
    ( version_compare( WB_VERSION, "2.8.2", '>=' ) ? $admin->getFTAN() : '' );
    include_once( 'info.php' );
    echo "<h2>Bookings V$module_version</h2>\n";

?>

  <h3><?php echo $MOD_BOOKINGS['LAYOUT_SETTINGS']; ?></h3>

  <table class="mod_bookings_table">
    <tr>
  		<td colspan="2" class="row_a"><strong><?php echo $MOD_BOOKINGS['DEFAULTS']; ?></strong></td>
  	</tr>
   	<tr>
  		<td class="mod_bookings_left">
        <label for="default_view"><?php echo $MOD_BOOKINGS['DEFAULT_VIEW']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  		  <select name="default_view" id="default_view">
<?php
    foreach ( array( 'year', 'quart', 'month', 'day', 'week' ) as $view ) {
        echo "<option value=\"$view\"";
        if ( $view === $default_view ) {
            echo " selected=\"selected\"";
        }
        echo ">",
             $MOD_BOOKINGS['DEFAULT_VIEW_'.strtoupper($view)],
             "</option>\n";
    }
?>
        </select>
  		</td>
  	</tr>
   	<tr>
  		<td class="mod_bookings_left">
        <label for="past_years"><?php echo $MOD_BOOKINGS['PREVYEARS']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="past_years" id="past_years" value="<?php echo $past_years; ?>" />
  		</td>
  	</tr>
   	<tr>
  		<td class="mod_bookings_left">
        <label for="next_years"><?php echo $MOD_BOOKINGS['NEXTYEARS']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="next_years" id="next_years" value="<?php echo $next_years; ?>" />
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="showpast"><?php echo $MOD_BOOKINGS['SHOWPAST']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
     		<input type="radio" name="showpast" value="y" <?php if ( $showpast == 'y' ) { echo 'checked="checked"'; } ?> /> <?php echo $TEXT['YES'] ?>
        <input type="radio" name="showpast" value="n" <?php if ( $showpast == 'n' ) { echo 'checked="checked"'; } ?> /> <?php echo $TEXT['NO'] ?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="always_link"><?php echo $MOD_BOOKINGS['ALWAYSLINK']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
     		<input type="radio" name="always_link" value="y" <?php if ( $always_link == 'y' ) { echo 'checked="checked"'; } ?> /> <?php echo $TEXT['YES'] ?>
        	<input type="radio" name="always_link" value="n" <?php if ( $always_link == 'n' ) { echo 'checked="checked"'; } ?> /> <?php echo $TEXT['NO'] ?>
  		</td>
  	</tr>
  	<tr>
  		<td colspan="2" class="row_a"><strong><?php echo $MOD_BOOKINGS['LAYOUT_YEARVIEW']; ?></strong></td>
  	</tr>
   	<tr>
  		<td class="mod_bookings_left">
        <label for="breakafter"><?php echo $MOD_BOOKINGS['BREAK']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="breakafter" id="breakafter" value="<?php echo $breakafter; ?>" />
  			<?php echo $MOD_BOOKINGS['MONTHS']; ?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="stylesheet"><?php echo $MOD_BOOKINGS['STYLESHEET']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<textarea name="stylesheet" id="stylesheet" style="width: 98%; height: 80px;" rows="5" cols="80"><?php echo $stylesheet; ?></textarea>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="bookingsheader"><?php echo $MOD_BOOKINGS['BOOKINGSHEADER'] ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<textarea name="bookingsheader" id="bookingsheader" style="width: 98%; height: 80px;" rows="5" cols="80"><?php echo $bookingsheader; ?></textarea>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="bookingsfooter"><?php echo $MOD_BOOKINGS['BOOKINGSFOOTER'] ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<textarea name="bookingsfooter" id="bookingsfooter" style="width: 98%; height: 80px;" rows="5" cols="80"><?php echo $bookingsfooter; ?></textarea>
  		</td>
  	</tr>

  	<tr>
  		<td colspan="2" class="row_a"><strong><?php echo $MOD_BOOKINGS['LAYOUT_DAYVIEW']; ?></strong></td>
  	</tr>
    <tr>
  		<td class="mod_bookings_left">
        <label for="dateformat"><?php echo $MOD_BOOKINGS['DATEFORMAT'] ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="dateformat" id="dateformat" value="<?php echo $dateformat; ?>" />
  			<?php echo $MOD_BOOKINGS['STRFTIMEHINT'] ?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="dayview"><?php echo $MOD_BOOKINGS['DAYVIEW'] ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="radio" name="dayview" id="dayview" value="list" <?php if ( $dayview == 'list' ) { echo 'checked="checked"'; } ?>/> <?php echo $MOD_BOOKINGS['DAYVIEWLIST'] ?>
        <input type="radio" name="dayview" id="dayview" value="sheet" <?php if ( $dayview == 'sheet' ) { echo 'checked="checked"'; } ?> /> <?php echo $MOD_BOOKINGS['DAYVIEWSHEET'] ?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="daysheetheader"><?php echo $MOD_BOOKINGS['DAYSHEETHEADER'] ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<textarea name="daysheetheader" id="daysheetheader" style="width: 98%; height: 80px;" rows="5" cols="80"><?php echo $daysheetheader; ?></textarea>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="daystarthour"><?php echo $MOD_BOOKINGS['DAYSTARTHOUR']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="daystarthour" id="daystarthour" value="<?php echo $daystarthour; ?>" />
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="dayendhour"><?php echo $MOD_BOOKINGS['DAYENDHOUR']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="dayendhour" id="dayendhour" value="<?php echo $dayendhour; ?>" />
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left">
        <label for="timeoffset"><?php echo $MOD_BOOKINGS['TIMEOFFSET']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
  			<input type="text" name="timeoffset" id="timeoffset" value="<?php echo $timeoffset; ?>" />
  		</td>
  	</tr>
  </table>

  <table class="mod_bookings_table">
  	<tr>
  		<td class="row_b">
  			<input name="save_settings" type="submit" value="<?php echo $TEXT['SAVE']; ?>" />
  		</td>
  		<td  class="row_b" style="text-align: right;">
  			<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo $cancel; ?>?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section; ?>';" />
  		</td>
  	</tr>
  </table>

  <h3><?php echo $MOD_BOOKINGS['PERMISSION_SETTINGS']; ?></h3>

  <table class="mod_bookings_table">
  	<tr>
  		<td colspan="2" class="row_a"><strong><?php echo $MOD_BOOKINGS['PERMISSION_OWNER']; ?></strong></td>
  	</tr>
	 	<tr>
  		<td class="mod_bookings_left">
        <label for="owner_edit"><?php echo $MOD_BOOKINGS['OWNER_CAN_ALL']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
     		<input type="radio" name="owner_edit" id="owner_edit" value="y" <?php if ( $owner_edit == 'y' ) { echo 'checked="checked"'; } ?>/> <?php echo $TEXT['YES'] ?>
        <input type="radio" name="owner_edit" id="owner_edit" value="n" <?php if ( $owner_edit == 'n' ) { echo 'checked="checked"'; } ?> /> <?php echo $TEXT['NO'] ?>
      </td>
  	</tr>
  	<tr>
  		<td colspan="2" class="row_a"><strong><?php echo $MOD_BOOKINGS['PERMISSION_GROUP']; ?></strong></td>
  	</tr>
   	<tr>
  		<td class="mod_bookings_left">
        <label for="admin_groups[]"><?php echo $MOD_BOOKINGS['ADMIN_GROUPS']; ?>:</label>
      </td>
  		<td class="mod_bookings_right">
<?php
    _get_group_select( 'admin' );
?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left"><?php echo $MOD_BOOKINGS['ADD_GROUPS']; ?>:</td>
  		<td class="mod_bookings_right">
<?php
  _get_group_select( 'add' );
?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left"><?php echo $MOD_BOOKINGS['MOD_GROUPS']; ?>:</td>
  		<td class="mod_bookings_right">
<?php
  _get_group_select( 'mod' );
?>
  		</td>
  	</tr>
  	<tr>
  		<td class="mod_bookings_left"><?php echo $MOD_BOOKINGS['DEL_GROUPS']; ?>:</td>
  		<td class="mod_bookings_right">
<?php
  _get_group_select( 'del' );
?>
  		</td>
  	</tr>
  </table><br />

  <table class="mod_bookings_table">
  	<tr>
  		<td class="row_b">
  			<input name="save_settings" type="submit" value="<?php echo $TEXT['SAVE']; ?>" />
  		</td>
  		<td  class="row_b" style="text-align: right;">
  			<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo $cancel; ?>?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section; ?>';" />
  		</td>
  	</tr>
  </table>
  </form>
</div><!-- mod_bookings -->

<?php

}   // Bookings_edit_Settings

/*
    save settings
*/
function Bookings_save_Settings ( $section = '' ) {

    global $page_id, $database, $admin, $wb, $MESSAGE, $MOD_BOOKINGS, $js_back;

    $class = $wb;
    if ( isset( $admin ) AND get_class($admin) == 'admin' ) {
        $class = $admin;
    }

    if ( Bookings_user_can( 'admin', '', $section ) != 1 ) {
        $class->print_error(
            $MOD_BOOKINGS['ERR_PERMISSION'],
            ADMIN_URL.'/pages/modify.php?page_id='.$page_id
        );
    }

    // Include WB admin wrapper script
    $update_when_modified = true; // Tells script to update when this page was last updated

    // remove <script>
    $stylesheet     = __strip( $_POST['stylesheet']     );
    $bookingsheader = __strip( $_POST['bookingsheader'] );
    $bookingsfooter = __strip( $_POST['bookingsfooter'] );

    $dateformat     = addslashes($_POST['dateformat']);
    $dayview        = addslashes($_POST['dayview']);
    $daysheetheader = addslashes($_POST['daysheetheader']);
    $daystarthour   = addslashes($_POST['daystarthour']);
    $dayendhour     = addslashes($_POST['dayendhour']);
    $timeoffset     = addslashes($_POST['timeoffset']);

    $class_groups   = $class->get_post_escaped('admin_groups');
    $add_groups     = $class->get_post_escaped('add_groups');
    $mod_groups     = $class->get_post_escaped('mod_groups');
    $del_groups     = $class->get_post_escaped('del_groups');
    $owner_edit     = $class->get_post_escaped('owner_edit');
    $default_view   = $class->get_post_escaped('default_view');
    $admin_email    = $class->get_post_escaped('admin_email');
    $showpast       = $class->get_post_escaped('showpast');

    $past_years     = $class->get_post_escaped('past_years');
    $next_years     = $class->get_post_escaped('next_years');

    $breakafter     = is_numeric( $_POST['breakafter'] )
                    ? $_POST['breakafter']
                    : 3;

    if ( ! is_numeric( $past_years ) || strlen( $past_years ) > 2 ) {
        $past_years = 1;
    }
    if ( ! is_numeric( $next_years ) || strlen( $next_years ) > 2 ) {
        $next_years = 2;
    }

    // Add "Administrators" group to all groups and convert to string
    $class_groups[] = 1;
    if(!in_array(1, $class->get_groups_id())) {
    	  $class_groups[] = implode(",",$class->get_groups_id());
    }
    $class_groups = implode(',', $class_groups);

    $add_groups[] = 1;
    if(!in_array(1, $class->get_groups_id())) {
    	  $add_groups[] = implode(",",$class->get_groups_id());
    }
    $add_groups = implode(',', $add_groups);

    $mod_groups[] = 1;
    if(!in_array(1, $class->get_groups_id())) {
    	  $mod_groups[] = implode(",",$class->get_groups_id());
    }
    $mod_groups = implode(',', $mod_groups);

    $del_groups[] = 1;
    if(!in_array(1, $class->get_groups_id())) {
    	  $del_groups[] = implode(",",$class->get_groups_id());
    }
    $del_groups = implode(',', $del_groups);

    if ( empty( $breakafter ) ) {
        $breakafter = 3;
    }

    if ( $default_view === 'year' ) {
        $default_view = '';
    }

    //Write Settings to Database
    $database->query("UPDATE ".TABLE_PREFIX."mod_bookings_settings
    			SET	page_id = '$page_id',
      				stylesheet = '$stylesheet',
      				breakafter = '$breakafter',
      				bookingsheader = '$bookingsheader',
      				bookingsfooter = '$bookingsfooter',
      				dateformat = '$dateformat',
      				dayview = '$dayview',
      				daysheetheader = '$daysheetheader',
      				daystarthour = '$daystarthour',
      				dayendhour = '$dayendhour',
      				timeoffset = '$timeoffset',
      				admin_groups = '$class_groups',
      				add_groups = '$add_groups',
      				mod_groups = '$mod_groups',
      				del_groups = '$del_groups',
      				owner_edit = '$owner_edit',
      				default_view = '$default_view',
      				admin_email = '$admin_email',
      				showpast = '$showpast',
      				past_years = '$past_years',
      				next_years = '$next_years'
    			WHERE section_id = '$section'"
    			);

    // Check if there is a database error, otherwise say successful
    if($database->is_error()) {
    	  $class->print_error($database->get_error(), $js_back);
    } else {
    	  $class->print_success(
            $MESSAGE['PAGES']['SAVED'],
            $_SERVER['SCRIPT_NAME'].'?page_id='.$page_id.'&section_id='.$section.'&settings=1'
        );
    }

}   // Bookings_save_Settings

function Bookings_get_permissions ( $section = '' ) {

    global $debug, $database, $can, $_init, $page_id, $admin;

    $sql = "SELECT admin_groups, add_groups, mod_groups, del_groups
    FROM ".TABLE_PREFIX."mod_bookings_settings
    WHERE page_id = '$page_id'
      AND section_id = '$section'";

	  $results      = $database->query($sql);
	  if($database->is_error()) {
    	  $admin->print_error($database->get_error());
    }
		$result       = $results->fetchRow();

		foreach ( array( 'admin', 'add', 'mod', 'del' ) as $action ) {

        $groups   = explode(',', str_replace('_', '', $result[$action.'_groups']));
        $in_group = FALSE;

        foreach( $admin->get_groups_id() as $cur_gid ) {
    		    if ( is_array( $groups )
              && in_array( $cur_gid, $groups )
            ) {
    		        $can[ $action ] = 1;
    		    }
    		}

    }

    if ( true === $debug ) {
        echo "[Bookings_get_permissions]<br />\n<pre>SQL\n$sql\n";
        print_r($can);
        echo "</pre>";
    }

    return $can;

}   // Bookings_get_permissions

function Bookings_user_can( $action = 'add', $id = '', $section = '' ) {

    global $database, $page_id, $wb, $admin, $debug, $settings;

    if ( true === $debug ) {
        echo "[debug] requested right(s) -$action-<br />";
    }

    // users with specific system permissions can do all...
    if (
#         ( isset( $wb    ) AND $wb->is_authenticated() == true )
#         AND
         ( isset( $admin ) AND get_class($admin) == 'admin' AND $admin->get_permission( 'pages_modify' ) )
    ) {
        if ( true === $debug ) {
            echo "[debug] requested right(s) -$action- permitted by WB<br />";
        }
        return 1;
    }

    $can = Bookings_get_permissions( $section );

    if ( isset( $can[$action] ) && $can[$action] == 1 ) {
        if ( true === $debug ) {
            echo "[debug] requested right(s) -$action- permitted by Bookings module settings<br />";
        }
        return 1;
    }

    if ( ! empty( $id ) ) {

        $result = $database->query(
            "SELECT * FROM ".TABLE_PREFIX."mod_bookings_dates
            WHERE bookings_id = '$id'"
        );
        $row = $result->fetchRow();

        if ( $row['owner_id'] == $_SESSION['USER_ID'] && $settings['owner_edit'] !== "n" ) {
            if ( true === $debug ) {
                echo "[debug] requested right(s) -$action- permitted by Bookings module - User is Owner<br />";
            }
            return 1;
        }

    }

    return 0;

}   // Bookings_user_can

/**
 *
 **/
function Bookings_create_link ( $year, $month = NULL, $day = NULL, $week = NULL, $text = NULL, $prev = NULL ) {

    global $settings;

    $link = NULL;

    // create link to previous year [month [day]]
    if ( $prev ) {

        if (
            ! isset ( $settings['past_years'] )
            ||
            $year >= ( date("Y") - $settings['past_years'] )
        ) {
            $link = "<a href=\"".$settings['base_url']."?year=$year";
            if ( ! $text ) {
                $text = "&laquo; $year";
            }
        }
    }
    else {

        if (
            ! isset ( $settings['next_years'] )
            ||
            $year <= ( date("Y") + $settings['next_years'] )
        ) {
            $link = "<a href=\"".$settings['base_url']."?year=$year";
            if ( ! $text ) {
                $text = "$year &raquo;";
            }
        }
    }

    if ( $link )  {
        if ( $month ) {
            $link .= "&amp;month=$month";
        }
        if ( $week ) {
            $link .= "&amp;week=$week";
        }
        if ( $day ) {
            $link .= "&amp;day=$day";
        }
        $link .= "\">$text</a>";
    }

    return $link;

}   // end function Bookings_create_link()

function Bookings_Ranges_Form() {

	global $ranges, $MOD_BOOKINGS;

	if ( is_array( $ranges ) ) {
        for ( $i=1; $i<=12; $i++ ) {
	        $monthnames[] = htmlentities( strftime( '%B', mktime(0,0,0,($i+1),0,2011) ) );
	    }
		echo "<script src=\"".WB_URL."/modules/bookings_v2/CalendarPopup.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
    var begindate = new CalendarPopup();
    begindate.setTodayText(  '" . $MOD_BOOKINGS['TODAY']     . "' );
    begindate.setMonthNames( '" . join('", "', $monthnames ) . "' );
    begindate.setDayHeaders( '" . $MOD_BOOKINGS['SHORTDAYNAMES'][6] . "', '" . join("', '", array_slice( $MOD_BOOKINGS['SHORTDAYNAMES'], 0, 6 ) ) . "' );
    begindate.setReturnFunction( 'setRange' );
    function setRange(y,m,d) {
    	document.forms['setrange'].year.value=y;
    	document.forms['setrange'].month.value=m;
    	document.forms['setrange'].day.value=d;
	}
</script>
<form method=\"get\" name=\"setrange\" action=\"\">
<label for=\"range\">",$MOD_BOOKINGS['DAYS'],": </label>
<select name=\"range\" class=\"small\">";

		foreach( $ranges as $item ) {
			echo "<option value=\"$item\">$item</option>";
		}
		echo "</select> ",
             $MOD_BOOKINGS['BEGINDATE'],
			 " <select name=\"day\" class=\"small\">";
    	for ( $d = 1; $d <= 31; $d++ ) {
        	echo "<option value=\"$d\"".($d==date('d')?' selected="selected"':NULL).">$d</option>\n";
    	}
        echo "</select>&nbsp;<select name=\"month\">";
    	for ( $m = 1; $m <= 12; $m++ ) {
        	echo "<option value=\"$m\"".($m==date('m')?' selected="selected"':NULL).">",$MOD_BOOKINGS['MONTHNAMES'][$m],"</option>\n";
    	}
        echo "</select>&nbsp;<select name=\"year\" class=\"small\">";
    	for ( $y = date('Y'); $y <= ( date('Y') + 5 ); $y++ ) {
        	echo "<option value=\"$y\"".($y==date('Y')?' selected="selected"':NULL)."> $y</option>\n";
    	}
		echo "</select>",
			 "<a id=\"begindate\" name=\"begindate\" title=\"Calendar\" onclick=\"begindate.showCalendar('begindate'); return false;\" href=\"#\">",
             "<img style=\"border: 0\" alt=\"Calendar\" src=\"".WB_URL."/modules/bookings_v2/calendar.png\" />",
             "</a><input type=\"submit\" /></form><br /><br />";
	}
}   // end function Bookings_Ranges_Form()

function is_in_past ( $year, $month, $day, $hour = NULL, $min = NULL ) {

    $today = getdate();

    if ( empty( $hour ) ) {
        $hour = $today['hours'];
    }

    if ( empty( $min ) ) {
        $min = $today['minutes'];
    }

    if (
        mktime( $today['hours'], $today['minutes'], 0, $today['mon'], $today['mday'], $today['year'] )
        >
        mktime( $hour, $min, 0, $month, $day, $year )
    ) {
        return true;
    }

    return false;

}

/**
 * get the minute options
 **/
function _getMinutes ( $selected, $section ) {
    $TIMES = _GetTimeOffsets($section);
    $sel[$selected] = 'selected="selected"';
    $minute_options = '';
    foreach ( $TIMES as $min ) {
        $min = sprintf( "%02d", $min );
        $minute_options .= '<option value="' . $min . '" '
                        .  ( isset( $sel[$min] ) ? $sel[$min] : '' )
                        . '>' . $min . '</option>' . "\n";
    }
    return $minute_options;
}   // _getMinutes

/**
 *
 **/
function _WeekToDay ( $week, $year ) {
    global $debug;

    $ts = strtotime($year . '-01-04 +' . ($week - 1) . ' weeks');
    while (date('l', $ts) != 'Monday') {
        $ts = strtotime('-1 day', $ts);
    }

    if ( $debug === true ) {
        echo "[debug] week $week year $year ts $ts<br />\n";
    }

    return $ts;

}   // _WeekToDay

/**
 * get booking details for a given day
 **/
function _GetBookingsForDay( $year, $month, $day, $section_id, $for='list' )
{
    global $page_id, $database, $debug, $MOD_BOOKINGS, $DEFAULTS;

    $booked = array();

    // offset
    $set = _Bookings_Settings( $section_id );
    $offset = $set['timeoffset'] ? $set['timeoffset'] : '30';
    $offset = 60 * $offset;

    if ( empty ( $month ) ) {
        $month  = date("M");
    }

    $sql = "SELECT
		begindate as begin,
    	enddate as end,
    	groups.name AS group_name,
    	groups.color As color,
    	dates.name AS name,
    	dates.hidename AS hidename,
    	dates.group_id AS group_id,
    	dates.state
    FROM
    	".TABLE_PREFIX."mod_bookings_dates AS dates
    LEFT OUTER JOIN ".TABLE_PREFIX."mod_bookings_groups AS groups
    	ON groups.group_id = dates.group_id
	WHERE
        	dates.section_id = '$section_id'
        AND
          (
            (
                UNIX_TIMESTAMP(begindate)
             		BETWEEN
            			UNIX_TIMESTAMP('$year-$month-$day 00:00:00')
            		AND
            			UNIX_TIMESTAMP('$year-$month-$day 23:59:59')
            OR
                UNIX_TIMESTAMP(enddate)
             		BETWEEN
            		    UNIX_TIMESTAMP('$year-$month-$day 00:00:00')
            		AND
            			UNIX_TIMESTAMP('$year-$month-$day 23:59:59')
 			)
 		OR
            (
                UNIX_TIMESTAMP('$year-$month-$day 00:00:00')
                    BETWEEN
                        UNIX_TIMESTAMP(begindate)
                    AND
                        UNIX_TIMESTAMP(enddate)
            OR
                UNIX_TIMESTAMP('$year-$month-$day 23:59:59')
                    BETWEEN
                        UNIX_TIMESTAMP(begindate)
                    AND
                        UNIX_TIMESTAMP(enddate)
            )
        )
        ORDER BY begindate, enddate";

    if ( true === $debug ) {
        echo "[_GetBookingsForDay]<br />SQL: $sql<br />";
    }

    $result = $database->query(
        $sql
    );

    if ( is_object ( $result ) ) {
        while( $row = $result->fetchRow() ) {

            $begin   = strtotime($row['begin']);
            $end     = strtotime($row['end']);
            $state   = $row['state'];
            $color   = $row['color'];
            $fullday = 0;

            // fix leap seconds: make sure the unix timestamp ends
            // with 00; works as we never have seconds in bookings times
            //$begin = substr_replace( $begin, '00', -2 );
            //$end   = substr_replace( $end, '00', -2 );

            if (
                date( 'H:i', $begin ) == '00:00'
                &&
               	date( 'H:i', $end )   == '23:59'
            ) {
                $fullday = 1;
            }

            if (
                $row['hidename'] == 'y'
            ) {
                $name = $MOD_BOOKINGS['STATE_'.strtoupper($state)];
            }
            else {
                $name 		= empty( $row['name'] ) 	  ? $MOD_BOOKINGS['STATE_'.strtoupper($state)] : $row['name'];
            }
			$group_name = empty( $row['group_name'] ) ? $MOD_BOOKINGS['STATE_'.strtoupper($state)] : $row['group_name'];

			if ( ! $fullday ) {
            	if ( isset($DEFAULTS['show_until']) && $DEFAULTS['show_until'] ) {
                	$time = date( 'H:i', $begin ) . ' - ' . date( 'H:i', $end );
            	}
            	else {
                	$time = date( 'H:i', $begin );
            	}
			}
			else {
				$time = $MOD_BOOKINGS['DAYLONG'];
			}

            if ( $for == 'sheet' ) {
                for ( $i = $begin; $i<=$end; $i=$i+$offset ) {
                    $booked[$i] = array(
                        'time'     => $time,
                        'what'     => $name,
                        'group'    => $group_name,
                        'color'    => $color,
                        'state'    => $state,
                        'group_id' => $row['group_id'],
                        'fullday'  => $fullday
                    );
                }
            }
            else {
                $booked[] = array(
                    'time'     => $time,
                    'what'     => $name,
                    'group'    => $group_name,
                    'color'    => $color,
                    'state'    => $state,
                    'fullday'  => $fullday,
                    'group_id' => $row['group_id']
                );
            }

        }
    }

    return $booked;

}   // _GetBookingsForDay

/**
 * get bookings for a given month
 **/
function _GetBookings ( $year, $thismonth, $section_id, $single )
{

    global $database, $wb, $MOD_BOOKINGS, $page_id, $debug, $DEFAULTS, $settings;

    $booked = array();
    $part   = array();
    $states = array();
    $groups = array();

    if ( $single )
    {
        $from = "&amp;single=1";
    }

    $lastday   = strftime( "%d", mktime(0, 0, 0, ($thismonth+1), 0, $year) );
    $thismonth = sprintf( "%02d", $thismonth );

    $sql = "SELECT * FROM ".TABLE_PREFIX."mod_bookings_dates
    WHERE section_id = '$section_id'
        AND
		(
            UNIX_TIMESTAMP(begindate)
         	    BETWEEN
        		     UNIX_TIMESTAMP('$year-$thismonth-01 00:00:00')
        		 AND
        			 UNIX_TIMESTAMP('$year-$thismonth-$lastday 23:59:59')
        OR
            UNIX_TIMESTAMP(enddate)
        	    BETWEEN
        		     UNIX_TIMESTAMP('$year-$thismonth-01 00:00:00')
        		AND
        			 UNIX_TIMESTAMP('$year-$thismonth-$lastday 23:59:59')
        OR
            UNIX_TIMESTAMP(begindate) < UNIX_TIMESTAMP('$year-$thismonth-01 00:00:00')
        		AND
        	UNIX_TIMESTAMP(enddate)	> UNIX_TIMESTAMP('$year-$thismonth-$lastday 23:59:59')
        )
        ORDER BY begindate, enddate";

    if ( true === $debug ) {
        echo "[_GetBookings]<br />SQL: $sql<br />";
    }

    // get the dates for $year
    $result = $database->query(
        $sql
    );

    if ( $result->numRows() > 0 )
    {

        $group_temp
            = _GetBookingsGroups( $section_id );
        $groupnames = array();
        foreach( $group_temp as $gr )
        {
            $groupnames[$gr['group_id']] = $gr['name'];
        }

        while( $row = $result->fetchRow() ) {

            $firstmonth = true;
            $lastmonth  = true;
            $begindate  = date_parse( $row['begindate'] );
            $enddate    = date_parse( $row['enddate'] );
            $beginday   = $begindate['day'];
            $endday     = $enddate['day'];

            if ( $begindate['month'] <> $thismonth ) {
                $begindate['month'] = $thismonth;
                $beginday           = 1;
                $firstmonth         = false;
            }

            if ( $enddate['month'] <> $thismonth ) {
                $enddate['month'] = $thismonth;
                $endday           = strftime( "%d", mktime(0,0,0,$thismonth+1,0,$year));
                $lastmonth        = false;
            }

            if ( ! isset( $row['state'] ) )
            {
                $row['state'] = 'booked';
            }

            for ( $i = $beginday; $i <= $endday; $i++ ) {

                $temp = array();

                $temp['name'] = $MOD_BOOKINGS['STATE_'.strtoupper($row['state'])];

                if ( $i == $begindate['day'] && $firstmonth) {
                    if (  $begindate['hour']   <> 0
                       || $begindate['minute'] <> 0 )
                    {
                        $part[$i] = 1;
                        $temp['begin'] = sprintf( '%02d:%02d', $begindate['hour'], $begindate['minute'] );
                    }
                }

                if ( $i == $enddate['day'] && $lastmonth) {
                    if ( $enddate['hour']   <> 0
                      || $enddate['minute'] <> 0 )
                    {
                        if ( $enddate['hour'] != 23 && $enddate['minute'] != 59 )
                        {
                            $part[$i] = 1;
                            $temp['end'] = sprintf( '%02d:%02d', $enddate['hour'], $enddate['minute'] );
                        }
                    }
                }

                if ( $row['hidename'] == 'n' )
                {
                    $temp['name'] = $row['name'];
                }

                $detail = '';
                if ( ! empty( $temp['begin'] ) && empty( $temp['end'] ) ) {
                    $detail .= $MOD_BOOKINGS['FROM'] . ' ' . $temp['begin'];
                }
                elseif ( ! empty( $temp['end'] ) && empty ( $temp['begin'] ) ) {
                    $detail .= $MOD_BOOKINGS['UNTIL'] . ' ' . $temp['end'];
                }
                elseif ( ! empty( $temp['end'] ) && $temp['begin'] ) {
                    if ( isset($DEFAULTS['show_until']) && $DEFAULTS['show_until'] ) {
                        $detail .= join( ' - ', array( $temp['begin'], $temp['end'] ) );
                    }
                    else {
                        $detail .= $temp['begin'];
                    }
                }

                if ( ! empty( $temp['name'] ) ) {
                    $detail .= ' ' . $temp['name'];
                }

                if ( $row['group_id'] > 0  && $row['hidename'] == 'n' )
                {
					$detail .= ( isset($groupnames[$row['group_id']]) ? '('.$groupnames[$row['group_id']].')' : NULL );
                }

                if ( ! empty ( $detail ) ) {
                    if ( ! empty ( $details[$i] ) ) {
                        $details[$i] .= "<br />$detail\n";
                    }
                    else {
                        $details[$i] = "$detail\n";
                    }
                }

                $booked[$i] = 1;
                $states[$i] = $row['state'];

            }

        }

    }

    // if loaded by droplet
    $base_url = $wb->page_link( $wb->page['link'] );
    if ( PAGE_ID != $page_id ) {
		// get page link for the page Bookings is used on
		$sql  = 'SELECT * FROM `' . TABLE_PREFIX . 'pages` WHERE `page_id`="' . (int)$page_id .'";';
        if ( ($res_pages = $database->query($sql)) != null ) {
            if ( ($page = $res_pages->fetchRow()) ) {
                $base_url = $wb->page_link( $page['link'] );
			}
		}
    }

    while ( list ( $index, $item ) = each ( $booked ) )
    {
        if ( ! empty ( $part[$index] ) || ! empty ( $details[$index] ) ) {

            $link = $base_url;

            if ( ! empty ( $part[$index] ) || $settings['always_link'] == 'y' ) {
                $link .= "?year="
                      .  $year
                      .  "&amp;month="
                      .  $thismonth
                      .  "&amp;day="
                      .  $index
                      ;
            }

            $booked[$index] = "<a href=\"$link\" class=\"bookings_tooltip\"><span>"
                            . $details[$index]
                            . "</span>"
                            . sprintf( '%02d', $index )
                            . "</a>";
        }
    }

    return array( $booked, $part, $states );

}   // _GetBookings

function _GetBookingsGroups ( $section )
{
    global $database, $page_id;

    $groups     = array();

    $query      = "SELECT * FROM ".TABLE_PREFIX."mod_bookings_groups
    WHERE section_id = '$section'
    ORDER BY name";

    $get_groups = $database->query($query);

    while($group = $get_groups->fetchRow()) {
        $groups[] = $group;
    }

    return $groups;

}   // _GetBookingsGroups

/**
 * get time offsets
 **/
function _GetTimeOffsets( $section )
{
    $set    = _Bookings_Settings( $section );

    $offset = $set['timeoffset'] ? $set['timeoffset'] : '30';

    for ( $i=0; $i<60; $i=$i+$offset ) {
        $times[] = $i;
    }

    return $times;
}

/**
 * get settings
 **/
function _Bookings_Settings ( $section_id )
{
    global $database, $wb, $settings, $page_id;

    $sql = "SELECT * FROM ".TABLE_PREFIX."mod_bookings_settings "
         . "WHERE section_id = '$section_id'";

    $result   = $database->query($sql);
    $settings = $result->fetchRow();

    // add current page URL
    $sql = "SELECT link FROM ".TABLE_PREFIX."pages "
         . "WHERE page_id ='$page_id'";
    $res = $database->query($sql);
    $pg  = $res->fetchRow();
    $settings['base_url'] = page_link($pg['link']);

    return $settings;

}   // _Bookings_Settings

/*
    print available groups with checkboxes
*/
function _get_group_select( $action = 'admin' ) {

    global $admin, $database,
           $admin_groups, $add_groups, $del_groups, $mod_groups;

    $groups     = array();
    $query      = "SELECT * FROM ".TABLE_PREFIX."groups";
    $get_groups = $database->query($query);
    while($group = $get_groups->fetchRow()) {
        $groups[] = $group;
    }

    $current = ${ $action . '_groups' };

    foreach ( $groups as $group ) {

        $flag_disabled = '';
    		$flag_checked =  '';

    		// admin cannot kick himself...
    		if ( in_array( $group['group_id'], $admin->get_groups_id() ) ) {
      			$flag_disabled = ' disabled="disabled"';
      			$flag_checked  = ' checked="checked"';
    		}

    		if ( in_array( $group['group_id'], $current ) ) {
    		    $flag_checked  = ' checked="checked"';
    		}

        echo "<input type=\"checkbox\" name=\"",
             $action,
             "_groups[]\" id=\"",
             $group["group_id"],
             "\" value=\"",
             $group["group_id"],
             "\"$flag_disabled$flag_checked />",
             $group['name'],
             "<br />\n";
    }

}   // _get_group_select

/***********************************************************************
 * __sort2d - sorting
 */
function __sort2d ($array, $index, $order='asc', $natsort=FALSE, $case_sensitive=FALSE)
{
    if(is_array($array) && count($array)>0)
    {
         foreach(array_keys($array) as $key)
         {
             $temp[$key]=$array[$key][$index];
         }
         if(!$natsort)
         {
             ($order=='asc')? asort($temp) : arsort($temp);
         }
         else
         {
             ($case_sensitive)? natsort($temp) : natcasesort($temp);
             if($order!='asc')
             {
                 $temp=array_reverse($temp,TRUE);
             }
         }
         foreach(array_keys($temp) as $key)
         {
             (is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
         }
         return $sorted;
    }
    return $array;
}   // function __sort2d

/***********************************************************************
 * http://de3.php.net/manual/de/function.strip-tags.php#89453
 **/
function __strip ( $filter ) {

    // realign javascript href to onclick
    $filter = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "onclick=' $2 '", $filter);

    //remove javascript from tags
    while( preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $filter) ) {
        $filter = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);
    }

    // dump expressions from contibuted content
    if(0) $filter = preg_replace("/:expression\(.*?((?>[^(.*?)]+)|(?R)).*?\)\)/i", "", $filter);

    while( preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $filter) ) {
        $filter = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $filter);
    }

    // remove all on* events
    while( preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $filter) ) {
       $filter = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $filter);
    }

    // remove <script> tags
    $filter = preg_replace( "/<\/?script.*?>/i", '', $filter );

    return $filter;
}

?>

