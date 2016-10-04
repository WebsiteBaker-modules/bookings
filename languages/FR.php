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
// French translation by quinto
// array for all language dependen text outputs in the front- and backend

// MODUL DESCRIPTION
$module_description = 'Module permettant d&apos;afficher des p&eacute;riodes vacantes ou occup&eacute;es sur un calendrier. (Pour des appartements, ou des salles de conf&eacute;rende par exemple.) Les r&eacute;servations peuvent s&apos;effectuer sur une journ&eacute;e compl&egrave;te ou selon l&apos;heure.';


// Note: stick to the naming convention: $MOD_MODULE_DIRECTORY
$MOD_BOOKINGS = array(
  	// variables for the backend file: modify_settings.php
  	'ACTIONS'             => 'actions',
  	'ADD_GROUPS'          => 'Ajouter de nouvelles r&eacute;servations',
  	'ADMIN_EMAIL'         => 'Email de l&apos;administrateur (laisser vide si pas d&apos;email)',
  	'ADMIN_GROUPS'        => 'Groupes avec droit d&apos;administrer',
    'ALWAYSLINK'          => 'Always link days',
    'BACK'                => '&laquo; retour', 
    'BACK_TO_YEARVIEW'    => 'retour &agrave; la vue par an',
  	'BEGINDATE'           => 'Date d&eacute;but',
  	'BEGINTIME'           => 'Heure d&eacute;but',
    'BOOKED'              => 'occup&eacute;',
    'BOOKED_PARTIALLY'    => 'occup&eacute; partiellement',
    'BOOKINGS_TITLE'      => 'R&eacute;servations',
  	'BOOKINGSFOOTER'      => 'Pied de page',
  	'BOOKINGSHEADER'      => 'Ent&ecirc;te',
  	'BREAK'               => 'Retour &agrave; la ligne apr&egrave;s',
    'CURRENTMONTH'        => 'afficher ce mois seulement',
    'DATEFORMAT'          => 'Format de la date',
    'DAY'                 => 'Jour',
    'DAYS'                => 'Jours',
    'DAYENDHOUR'          => 'Heure de fin sur la feuille (12 <= x <= 24)',
    'DAYLONG'             => 'Journ&eacute;e enti&egrave;re',
    'DAYSHEETHEADER'      => 'Ent&ecirc;te',
    'DAYSPAN'             => 'Span',
    'DAYSTARTHOUR'        => 'Heure de départ sur la feuille (12 <= x <= 24)',
    'DAYVIEW'             => 'Afficher',
    'DAYVIEWLIST'         => 'fa&ccedil;on liste',
    'DAYVIEWSHEET'        => 'fa&ccedil;on feuille',
    'DEL_GROUPS'          => 'Effacer les r&eacute;servations',
    'DEFAULTS'            => 'D&eacute;fauts',
    'DEFAULT_DATEFORMAT'  => '%m/%d/%y',
    'DEFAULT_VIEW'        => 'Vue par d&eacute;faut',
    'DEFAULT_VIEW_YEAR'   => 'ann&eacute;e en cours',
    'DEFAULT_VIEW_QUART'  => 'trimestre en cours',
    'DEFAULT_VIEW_MONTH'  => 'mois en cours',
    'DEFAULT_VIEW_WEEK'   => 'semaine de l&apos;ann&eacute;e en cours',
    'DEFAULT_VIEW_DAY'    => 'aujourd&apos;hui',
  	'ENDDATE'             => 'Date fin',
  	'ENDTIME'             => 'Heure fin',
  	'ERR_EXISTS'          => 'Cette entr&eacute;e existe d&eacute;j&agrave;!',
    'ERR_DATES'           => 'La date de fin se trouve avant la date de d&eacute;but!',
    'ERR_DATES_OVERLAP'   => 'Cette date entre en conflit avec une entr&eacute;e existante!',
    'ERR_INVALID_PARAM'   => 'Param&egrave;tre invalide',
    'ERR_PERMISSION'      => 'Permission refus&eacute;e pour cette action!',
  	'FREE'                => 'libre',
    'FROM'                => 'de',
    'FRONTEND_MOD_LINK'   => 'modifer les r&eacute;servations',
    'GROUP'               => 'groupe',
    'GROUPCOLOR'          => 'color',
    'GROUPMEMBERS'        => 'entrées',
    'GROUPNAME'           => 'nom du groupe',
    'GROUPS'              => 'Groupes',
    'HIDENAME'            => 'cacher',
    'ID'                  => 'ID',
    'IN_PAST'             => 'pass&eacute;',
    'INFO'                => 'Pour une journ&eacute;e enti&egrave;re laissez le r&eacute;glage de l&apos;heure &agrave; 0:00!',
    'INFO_PAST_NOT_SHOWN' => 'Les anciennes r&eacute;servations ne sont pas affich&eacute;es!',
    'LASTYEAR'            => 'afficher l&apos;ann&eacute;e derni&egrave;re',
  	'LAYOUT_DAYVIEW'      => 'Vue par jour',
  	'LAYOUT_SETTINGS'		=> 'R&eacute;glages de mise en page',
  	'LAYOUT_YEARVIEW'     => 'Vue par an',
  	'MAILSUBJECT'         => 'Nouvelle r&eacute;servation',
    'MAILMESSAGE'         => 'Vous avez une nouvelle r&eacute;servation ',
  	'MOD_GROUPS'          => 'Modifier les r&eacute;servations',
  	'MODIFY_HEADER'       => 'Modifier r&eacute;servation',
  	'MONTHNAMES'          => array(1 => 'Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','D&eacute;cembre'),
  	'MONTH'               => 'Mois',
  	'MONTHS'              => 'mois',
    'NAME'                => 'Client / Texte',
    'NEXTYEAR'            => 'afficher l&apos;ann&eacute;e prochaine',
    'NEXTYEARS'           => 'Afficher [x] ann&eacute;es dans le futur',
    'NO_BOOKINGS'         => 'pas de r&eacute;servations',
    'ONE_MONTH_BACK'      => '&laquo; un mois pr&eacute;c&eacute;dent',
    'ONE_MORE_MONTH'      => 'mois suivant &raquo;',
    'OWNER'               => 'propri&eacute;taire',
    'PERMISSION_GROUP'    => 'Permissions des groupes',
    'PERMISSION_OWNER'    => 'Permissions du propri&eacute;taire',
    'PERMISSION_SETTINGS' => 'Permissions',
    'PREVYEARS'           => 'Afficher [x] ann&eacute;es pr&eacute;c&eacute;dentes',
    'SHORTDAYNAMES'       => array('Lun','Mar','Mer','Jeu','Ven','Sam','Dim'),
    'SHOWNAME'            => 'afficher',
    'SHOWPAST'            => 'Afficher toutes les r&eacute;servations ant&eacute;rieures &agrave; aujourd&apos;hui',
    'STATE'               => 'Etat',
    'STATE_BOOKED'        => 'occup&eacute;',
    'STATE_RESERVED'      => 'reserv&eacute;',
    'STRFTIMEHINT'        => 'format <a href="http://www.php.net/strftime" target="_blank">strftime()</a>, ex: "%m/%d/%y"; correspond au r&eacute;glage par d&eacute;faut pour ce module',
  	'STYLESHEET'          => 'Feuille de style',
    'TIMEOFFSET'          => 'Tranche horaire (minutes)',
  	'TODAY'               => 'aujourd&apos;hui',
    'UNTIL'               => 'jusqu&apos;au',
    'OWNER_CAN_ALL'       => 'Le propri&eacute;taire peut modifier/effacer les r&eacute;servations',
    'QUART'               => 'Trimestre',
    'WEEK'                => 'Semaine',
    'YEAR'                => 'Ann&eacute;e',
);

?>
