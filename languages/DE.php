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

$module_description = 'Anzeige von Frei/Gebucht Zeiten auf einem Kalenderblatt. (Etwa f&uuml;r Ferienwohnungen oder Besprechungsr&auml;me.) Daten mit oder ohne Uhrzeit.';

// array for all language dependen text outputs in the front- and backend
// Note: stick to the naming convention: $MOD_MODULE_DIRECTORY
$MOD_BOOKINGS = array(
  	// variables for the backend file: modify_settings.php
  	'ACTIONS'             => 'Aktionen',
  	'ALWAYSLINK'          => 'Tage immer als Link anzeigen',
    'ADD_GROUPS'          => 'Buchungen hinzuf&uuml;gen',
    'ADMIN_EMAIL'         => 'Admin eMail (leer lassen f&uuml;r keine eMail)',
  	'ADMIN_GROUPS'        => 'Optionen bearbeiten (Admin-Rechte)',
    'BACK'                => '&laquo; zur&uuml;ck',
    'BACK_TO_YEARVIEW'    => 'zur&uuml;ck zur Jahresansicht',
  	'BEGINDATE'           => 'Datum von',
  	'BEGINTIME'           => 'Uhrzeit von',
  	'BOOKED'              => 'belegt',
  	'BOOKED_PARTIALLY'    => 'teilweise belegt',
  	'BOOKINGS_TITLE'      => 'Buchungsstatus',
  	'BOOKINGSFOOTER'      => 'Footer',
  	'BOOKINGSHEADER'      => 'Header',
  	'BREAK'               => 'Zeilenumbruch alle',
    'CURRENTMONTH'        => 'nur diesen Monat anzeigen',
    'DATEFORMAT'          => 'Datumsformat',
    'DAY'                 => 'Tag',
    'DAYENDHOUR'          => 'Tageskalenderblatt endend mit Stunde (12 <= x <= 24)',
    'DAYLONG'             => 'ganzt&auml;gig',      
    'DAYS'                => 'Tage',
    'DAYSHEETHEADER'      => '&Uuml;berschrift',
	'DAYSPAN'             => 'Spanne',
    'DAYSTARTHOUR'        => 'Tageskalenderblatt beginnend mit Stunde (0 <= x <= 12)',
    'DAYVIEW'             => 'Darstellung als',
    'DAYVIEWLIST'         => 'Liste',
    'DAYVIEWSHEET'        => 'Kalenderblatt',
    'DEFAULTS'            => 'Grundeinstellungen',
    'DEFAULT_DATEFORMAT'  => '%d.%m.%Y',
    'DEFAULT_VIEW'        => 'Standardansicht',
    'DEFAULT_VIEW_YEAR'   => 'aktuelles Jahr',
    'DEFAULT_VIEW_QUART'  => 'aktuelles Quartal',
    'DEFAULT_VIEW_MONTH'  => 'aktueller Monat',
    'DEFAULT_VIEW_WEEK'   => 'aktuelle Kalenderwoche',
    'DEFAULT_VIEW_DAY'    => 'heute (Tag)',
    'DEL_GROUPS'          => 'Buchungen entfernen',
  	'ENDDATE'             => 'Datum bis',
  	'ENDTIME'             => 'Uhrzeit bis',
    'ERR_DATES'           => 'Das Datum bis liegt vor dem Datum von!',
    'ERR_DATES_OVERLAP'   => 'Das angegebene Datum &uuml;berschneidet sich mit einem vorhandenen Eintrag!',
    'ERR_EXISTS'          => 'Dieses Element existiert bereits',
    'ERR_PERMISSION'      => 'Sie haben keine ausreichenden Rechte f&uuml;r diese Aktion!',
    'ERR_INVALID_PARAM'   => 'Ung&uuml;ltiger Parameter',
  	'FREE'                => 'frei',
    'FROM'                => 'ab',
    'FRONTEND_MOD_LINK'   => 'Buchungen bearbeiten',
    'GROUP'               => 'Gruppe',
    'GROUPCOLOR'          => 'Hintergrundfarbe',
    'GROUPMEMBERS'        => 'Eintr&auml;ge',
    'GROUPNAME'           => 'Name',
    'GROUPS'              => 'Gruppen',
    'HIDENAME'            => 'verstecken',
    'ID'                  => 'ID',
    'IN_PAST'             => 'Buchung liegt in der Vergangenheit',
    'INFO'                => 'Um den kompletten Tag zu belegen, Zeiteinstellungen auf 0:00 belassen!',
    'INFO_PAST_NOT_SHOWN' => 'Buchungen, die in der Vergangenheit liegen, werden nicht angezeigt.',
    'LASTYEAR'            => 'voriges Jahr anzeigen',
  	'LAYOUT_DAYVIEW'      => 'Tagesansicht',
  	'LAYOUT_SETTINGS'			=> 'Layout Einstellungen',
  	'LAYOUT_YEARVIEW'     => 'Jahres&uuml;bersicht',
  	'MAILSUBJECT'         => 'Neue Buchung',
    'MAILMESSAGE'         => 'Sie haben eine neue Buchung ',
  	'MOD_GROUPS'          => 'Buchungen bearbeiten',
  	'MODIFY_HEADER'       => 'Buchungsdaten bearbeiten',
  	'MONTHNAMES'          => array(1 => 'Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'),
  	'MONTH'               => 'Monat',
  	'MONTHS'              => 'Monate',
    'NAME'                => 'Name / Buchungstext',
    'NEXTYEAR'            => 'n&auml;chstes Jahr anzeigen',
    'NEXTYEARS'           => 'Anzahl Jahre in der Zukunft anzeigen',
    'NO_BOOKINGS'         => 'keine Buchungen vorhanden',
    'ONE_MONTH_BACK'      => '&laquo; einen Monat zur&uuml;ck',
    'ONE_MORE_MONTH'      => 'einen Monat weiter &raquo;',
    'OWNER'               => 'Eigent&uuml;mer',
    'PERMISSION_GROUP'    => 'Gruppenrechte',
    'PERMISSION_OWNER'    => 'Eigent&uuml;merrechte',
    'PERMISSION_SETTINGS' => 'Zugriffsrechte',
    'PREVYEARS'           => 'Anzahl Vorjahre zeigen',
  	'SHORTDAYNAMES'       => array('Mo','Di','Mi','Do','Fr','Sa','So'),
    'SHOWNAME'            => 'anzeigen',
    'SHOWPAST'            => 'Zur&uuml;ckliegende Buchungen anzeigen',
    'STATE'               => 'Status',
    'STATE_BOOKED'        => 'belegt',
    'STATE_RESERVED'      => 'reserviert',
    'STRFTIMEHINT'        => '<a href="http://www.php.net/strftime" target="_blank">strftime()</a> Format, z. B. "%m/%d/%y"; Standardformat im Sprachmodul',
  	'STYLESHEET'          => 'abweichender Stylesheet',
    'TIMEOFFSET'          => 'Zeitabstand (Minuten)',
    'TODAY'               => 'heute',
    'UNTIL'               => 'bis',
    'OWNER_CAN_ALL'       => 'Eigent&uuml;mer kann eigene Buchungen &auml;ndern und l&ouml;schen',
    'QUART'               => 'Quartal',
    'WEEK'                => 'Kalenderwoche',
    'YEAR'                => 'Jahr',
);

?>
