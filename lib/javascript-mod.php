<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Searches modules, filters and blocks for any Javascript files
 * that should be called on every page
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    define('NO_MOODLE_COOKIES', true);
    define('NO_UPGRADE_CHECK', true);

    include('../config.php');

    $output = "// Javascript from Moodle modules\n";

    $plugintypes = array('mod', 'filter', 'block');
    foreach ($plugintypes as $plugintype) {
        if ($mods = get_plugin_list($plugintype)) {
            foreach ($mods as $mod => $moddir) {
                if (is_readable($moddir.'/javascript.php')) {
                    $output .= file_get_contents($moddir.'/javascript.php');
                }
            }
        }
    }

    $lifetime = '86400';

    @header('Content-type: text/javascript');
    @header('Content-length: '.strlen($output));
    @header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    @header('Cache-control: max-age='.$lifetime);
    @header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
    @header('Pragma: ');

    echo $output;
