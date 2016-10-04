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

require_once('../../config.php');
require_once(WB_PATH.'/framework/functions.php');

$database = new database();
global $js_back;

// get previous version
$result = $database->query("SELECT version FROM ".TABLE_PREFIX."addons WHERE name like 'Bookings%'");

if ( ! empty( $result ) )
{

    $row = $result->fetchRow();

    // upgrade from 1.02
    if ( $row['version'] == '1.02' )
    {
        
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD dateformat VARCHAR(255) NOT NULL DEFAULT ''" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `dayview` VARCHAR(50) NOT NULL DEFAULT ''" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `daysheetheader` VARCHAR(255) NOT NULL DEFAULT ''" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `daystarthour` TINYINT(4) NOT NULL DEFAULT ''" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `dayendhour` TINYINT(4) NOT NULL DEFAULT ''" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }

    if ( $row['version'] <= '1.12' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_dates ADD `hidename` CHAR(1) NOT NULL DEFAULT 'y'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }
    
    if ( $row['version'] <= '1.14' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `timeoffset` TINYINT(4) NOT NULL DEFAULT '30'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }
    
    if ( $row['version'] <= '1.17' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `admin_groups` TEXT" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `add_groups` TEXT" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `mod_groups` TEXT" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `del_groups` TEXT" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `owner_edit` ENUM('y','n') NOT NULL DEFAULT 'y'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `default_view` VARCHAR(50) NULL" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `admin_email` varchar(150) default NULL" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("UPDATE ".TABLE_PREFIX."mod_bookings_settings SET admin_groups='1', add_groups='1', mod_groups='1', del_groups='1', owner_edit='y'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_dates ADD `owner_id` INT(11) NOT NULL DEFAULT '1'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
     	  }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_dates ADD `group_id` INT(11) NOT NULL DEFAULT '1'" );
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
        			. 'PRIMARY KEY  (`group_id`,`page_id`,`section_id`),'
              . 'KEY `group_id` (`group_id`)'
        			. ')';
        $database->query($mod_create_table);
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }
    
    if ( $row['version'] <= '2.12' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `showpast` ENUM('y','n') NOT NULL DEFAULT 'y'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }

    if ( $row['version'] <= '2.15' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `past_years` tinyint(2) unsigned NOT NULL default '1'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `next_years` tinyint(2) unsigned NOT NULL default '2'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    
    }
    
    if ( $row['version'] <= '2.23' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_dates ADD `state` VARCHAR(255) NOT NULL DEFAULT 'booked'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }
    
    if ( $row['version'] <= '2.28' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_groups ADD `color` VARCHAR(7)" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }
    
    if ( $row['version'] <= '2.30' ) {
        $database->query("ALTER TABLE ".TABLE_PREFIX."mod_bookings_settings ADD `always_link` ENUM('y','n') NOT NULL DEFAULT 'y'" );
        if($database->is_error()) {
        	  $admin->print_error($database->get_error(), $js_back);
        }
    }

}

?>
