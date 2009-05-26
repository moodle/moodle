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

/** NO_MOODLE_COOKIES = true */
    define('NO_MOODLE_COOKIES', true);
    /** NO_UPGRADE_CHECK = true */
    define('NO_UPGRADE_CHECK', true);

    include('../config.php');

    $output = "// Javascript from Moodle modules\n";

    if ($mods = get_list_of_plugins('mod')) {
        foreach ($mods as $mod) {
            if (is_readable($CFG->dirroot.'/mod/'.$mod.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/mod/'.$mod.'/javascript.php');
            }
        }
    }

    if ($filters = get_list_of_plugins('filter')) {
        foreach ($filters as $filter) {
            if (is_readable($CFG->dirroot.'/filter/'.$filter.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/filter/'.$filter.'/javascript.php');
            }
        }
    }

    if ($blocks = get_list_of_plugins('blocks')) {
        foreach ($blocks as $block) {
            if (is_readable($CFG->dirroot.'/blocks/'.$block.'/javascript.php')) {
                $output .= file_get_contents($CFG->dirroot.'/blocks/'.$block.'/javascript.php');
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

?>
