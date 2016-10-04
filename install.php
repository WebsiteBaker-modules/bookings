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

// delete existing module DB-table (start with a clean database)
$database->query("DROP TABLE IF EXISTS `" .TABLE_PREFIX ."mod_bookings_settings`");

// create a new, clean module DB-table (you need to change the fields added according your needs!!!)
$mod_create_table = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bookings_settings` ('
    . ' `page_id` INT NOT NULL DEFAULT \'0\','
    . ' `section_id` INT NOT NULL DEFAULT \'0\','
    . ' `stylesheet` TEXT NOT NULL ,'
    . ' `breakafter` char(1) NOT NULL DEFAULT \'6\','
    . ' `bookingsheader` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
    . ' `bookingsfooter` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
    . ' `dateformat` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
    . ' `dayview` VARCHAR(50) NOT NULL DEFAULT \'\' ,'
    . ' `daysheetheader` VARCHAR(255) NOT NULL DEFAULT \'\' ,'
    . ' `daystarthour` TINYINT(4) NOT NULL DEFAULT \'0\' ,'
    . ' `dayendhour` TINYINT(4) NOT NULL DEFAULT \'23\' ,'
    . ' `timeoffset` TINYINT(4) NOT NULL DEFAULT \'30\' ,'
    . ' `admin_groups` TEXT NOT NULL ,'
    . ' `add_groups` TEXT NOT NULL ,'
    . ' `mod_groups` TEXT NOT NULL ,'
    . ' `del_groups` TEXT NOT NULL ,'
    . ' `owner_edit` ENUM(\'y\',\'n\') NOT NULL DEFAULT \'y\' ,'
    . ' `default_view` VARCHAR(50) NULL,'
    . ' `admin_email` varchar(150) default NULL,'
    . ' `showpast` ENUM(\'y\',\'n\') NOT NULL DEFAULT \'y\' ,'
    . ' `past_years` tinyint(2) unsigned NOT NULL default \'1\', '
    . ' `next_years` tinyint(2) unsigned NOT NULL default \'2\', '
    . ' `always_link` ENUM(\'y\',\'n\') NOT NULL DEFAULT \'y\', '
    . ' PRIMARY KEY  (`section_id`)'
    . ')';
$database->query($mod_create_table);
if($database->is_error()) {
	  $admin->print_error($database->get_error(), $js_back);
}

// Create table for events
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bookings_dates`");
$mod_create_table = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bookings_dates` ('
			. '`page_id` INT NOT NULL DEFAULT \'0\','
			. '`section_id` INT NOT NULL DEFAULT \'0\','
			. '`bookings_id` INT NOT NULL AUTO_INCREMENT ,'
			. '`begindate` datetime NOT NULL,'
			. '`enddate` datetime NOT NULL,'
			. '`name` tinytext,'
			. '`hidename` char(1) NOT NULL default \'y\','
			. '`owner_id` INT(11) NOT NULL DEFAULT \'1\','
			. '`group_id` INT(11) NOT NULL DEFAULT \'1\','
			. '`state` VARCHAR(255) NOT NULL DEFAULT \'booked\','
			. 'PRIMARY KEY  (`bookings_id`),'
			. 'KEY `begindate` (`begindate`)'
			. ')';
$database->query($mod_create_table);
if($database->is_error()) {
	  $admin->print_error($database->get_error(), $js_back);
}

// Create table for groups
$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bookings_groups`");
$mod_create_table = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bookings_groups` ('
      . '`group_id` int(11) unsigned NOT NULL auto_increment,'
			. '`page_id` INT NOT NULL DEFAULT \'0\','
			. '`section_id` INT NOT NULL DEFAULT \'0\','
			. '`name` varchar(50),'
			. '`color` varchar(7),'
			. 'PRIMARY KEY  (`group_id`,`page_id`,`section_id`),'
      . 'KEY `group_id` (`group_id`)'
			. ')';
$database->query($mod_create_table);
if($database->is_error()) {
	  $admin->print_error($database->get_error(), $js_back);
}

?>
