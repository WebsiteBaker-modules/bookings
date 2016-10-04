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

require('../../config.php');
require(WB_PATH.'/modules/admin.php');
require('functions.php');

Bookings_save_Settings( $section_id );

// Print admin footer
$admin->print_footer()

?>
