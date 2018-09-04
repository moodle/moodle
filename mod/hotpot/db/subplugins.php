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
 * This file defines the HotPot directories that can be searched
 * by the get_plugin_list() function (in "lib/moodlelib.php")
 *
 * In order to allow for a flexible plugin strategy, the $subplugins
 * array is generated dynamically to include all subfolders under the
 * main subplugins folders
 *
 * @package    mod
 * @subpackage hotpot
 * @copyright  2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$subplugins = array(
    'hotpotattempt' => 'mod/hotpot/attempt',
    'hotpotsource'  => 'mod/hotpot/source',
    'hotpotreport'  => 'mod/hotpot/report'
);
