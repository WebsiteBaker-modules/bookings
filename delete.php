<?php

/**
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) year, Authors name
  Contact me: author(at)domain.xxx, http://authorwebsite.xxx

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

$database->query("DELETE FROM `" .TABLE_PREFIX ."mod_bookings_dates` WHERE `section_id` = '$section_id'");
$database->query("DELETE FROM `" .TABLE_PREFIX ."mod_bookings_groups` WHERE `section_id` = '$section_id'");
$database->query("DELETE FROM `" .TABLE_PREFIX ."mod_bookings_settings` WHERE `section_id` = '$section_id'");

?>