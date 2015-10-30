<?php

/**
 *  BENNU - PHP iCalendar library
 *  (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org). All rights reserved.
 *
 *  Released under the LGPL.
 *
 *  See http://bennu.sourceforge.net/ for more information and downloads.
 *
 * @author Ioannis Papaioannou 
 * @version $Id$
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

if(!defined('_BENNU_VERSION')) {
    define('_BENNU_VERSION', '0.1');
    include('bennu.class.php');
    include('iCalendar_rfc2445.php');
    include('iCalendar_components.php');
    include('iCalendar_properties.php');
    include('iCalendar_parameters.php');
}
