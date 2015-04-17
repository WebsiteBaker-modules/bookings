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
  
  Translation by "Ronald" and "Kees"
  See Websitebaker Community Forum for details
  http://forum.websitebaker2.org/index.php/topic,10218.0.html

**/

// array for all language dependen text outputs in the front- and backend
// Note: stick to the naming convention: $MOD_MODULE_DIRECTORY
$MOD_BOOKINGS = array(
	// variables for the backend file: modify_settings.php
	'ACTIONS'             => 'acties',
	'ADD_GROUPS'          => 'Boeking toevoegen',
	'ADMIN_EMAIL'         => 'Admin e-mailadres (Leeglaten voor geen e-mail)',
	'ADMIN_GROUPS'        => 'Admin groepen',
	'ALWAYSLINK'          => 'Always link days',
    'BACK'                => '&laquo; terug', 
    'BACK_TO_YEARVIEW'    => 'terug naar jaaroverzicht',
    'BEGINDATE'           => 'Datum vanaf',
    'BEGINTIME'           => 'Vanaf',
    'BOOKED'              => 'bezet',
    'BOOKED_PARTIALLY'    => 'gedeeltelijk bezet',
    'BOOKINGS_TITLE'      => 'Boekingen',
    'BOOKINGSFOOTER'      => 'Footer',
    'BOOKINGSHEADER'      => 'Opschrift',
	'BREAK'               => 'Breek na',
    'CURRENTMONTH'        => 'alleen deze maand',
    'DATEFORMAT'          => 'Datumnotatie',
    'DAY'                 => 'Dag',
    'DAYS'                => 'Dage',
    'DAYENDHOUR'          => 'Blad tijd tot (uur) (12 <= x <= 24)',
    'DAYLONG'             => 'hele dag',
    'DAYSHEETHEADER'      => 'Opschrift',	
    'DAYSPAN'             => 'Span',
    'DAYSTARTHOUR'        => 'Blad tijd vanaf (uur) (0 <= x <= 12)',
    'DAYVIEW'             => 'Toon als',
    'DAYVIEWLIST'         => 'Lijst',
    'DAYVIEWSHEET'        => 'Blad',
    'DEFAULTS'            => 'Standaardwaarden',
    'DEFAULT_DATEFORMAT'  => '%d.%m.%y',
    'DEFAULT_VIEW'        => 'Standaard weergave',
    'DEFAULT_VIEW_YEAR'   => 'dit jaar',
    'DEFAULT_VIEW_QUART'  => 'dit kwartaal',
    'DEFAULT_VIEW_MONTH'  => 'deze maand',
    'DEFAULT_VIEW_WEEK'   => 'deze kalenderweek',
    'DEFAULT_VIEW_DAY'    => 'vandaag',
    'DEL_GROUPS'          => 'Boeking verwijderen',
    'ENDDATE'             => 'Datum tot',
    'ENDTIME'             => 'Tot',
    'ERR_DATES'           => 'einddatum is voor aanvangsdatum!',
    'ERR_DATES_OVERLAP'   => 'deze periode overlapt een bestaande boeking!',
    'ERR_EXISTS'          => 'Dit record bestond al',
    'ERR_INVALID_PARAM'   => 'Ongeldige waarde',
    'ERR_PERMISSION'      => 'Sorry, u heeft geen bevoegdheden voor deze actie!',
    'FREE'                => 'beschikbaar',
    'FROM'                => 'vanaf',
    'GROUP'               => 'groep',
    'GROUPCOLOR'          => 'color',
    'GROUPMEMBERS'        => 'leden',
    'GROUPNAME'           => 'groepsnaam',
    'GROUPS'              => 'Groepen',
    'HIDENAME'            => 'verbergen',
    'ID'                  => 'ID',
    'IN_PAST'             => 'in het verleden',
    'INFO'                => 'voor hele dag tijd op 0:00 laten staan!',
    'INFO_PAST_NOT_SHOWN' => 'Boekingen uit het verleden niet getoond!',
    'LASTYEAR'            => 'vorig jaar tonen',
    'LAYOUT_DAYVIEW'      => 'Dagoverzicht',
    'LAYOUT_SETTINGS'     => 'Layout-instellingen',
    'LAYOUT_YEARVIEW'     => 'Jaaroverzicht',
    'MAILSUBJECT'         => 'Nieuwe boeking',
    'MAILMESSAGE'         => 'U heeft en nieuwe boeking ',
	  'MOD_GROUPS'          => 'Boekingen veranderen',
	  'MODIFY_HEADER'       => 'Boeking veranderen',
	  'MONTHNAMES'          => array(1 => 'januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'),
	  'MONTH'               => 'Maand',
	  'MONTHS'              => 'maanden',
    'NAME'                => 'Naam / boeking tekst',
    'NEXTYEAR'            => 'volgende jaar tonen',
    'NEXTYEARS'           => 'Toon [x] toekomstige jaren',
    'NO_BOOKINGS'         => 'geen boekingen',
    'ONE_MONTH_BACK'      => '&laquo; &eacute;&eacute;n maand terug',
    'ONE_MORE_MONTH'      => '&eacute;&eacute;n maand vooruit &raquo;',
    'OWNER'               => 'eigenaar',
    'PERMISSION_GROUP'    => 'Bevoegdheden groep',
    'PERMISSION_OWNER'    => 'Bevoegdheden eigenaar',
    'PERMISSION_SETTINGS' => 'Bevoegdheden',
    'PREVYEARS'           => 'Aantal voorgaande jaren tonen',
    'SHORTDAYNAMES'       => array('ma','di','wo','do','vr','za','zo'),
    'SHOWNAME'            => 'tonen',
    'SHOWPAST'            => 'Boekeningen uit het verleden tonen',
    'STATE'               => 'Status',
    'STATE_BOOKED'        => 'bezet',
    'STATE_RESERVED'      => 'gereserveerd',
    'STRFTIMEHINT'        => '<a href="http://www.php.net/strftime" target="_blank">strftime()</a> kies datumnotatie code "%m/%d/%y"; standaard en ./languages/NL.php',
    'STYLESHEET'          => 'Stylesheet',
    'TIMEOFFSET'          => 'tijd interval (in minuten)',
    'TODAY'               => 'vandaag',
    'UNTIL'               => 'tot',
    'OWNER_CAN_ALL'       => 'Eigenaar kan boekingen veranderen / opheffen',
    'QUART'               => 'Kwartaal',
    'WEEK'                => 'Week',
    'YEAR'                => 'Jaar',
);

?>
