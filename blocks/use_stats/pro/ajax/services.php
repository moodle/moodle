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

/*
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

/**
 * Ajax services for Use Stats block. Receives "Keep-alive" queries from user agent
 * to feed continuous session track in logs
 */
require('../../../config.php');
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');

$courseid = required_param('course', PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT); // ID of course module if in course module context.

// Security.

require_login($courseid, false, null, false, true); // Prevent redirect to avoid prelogin_catch_url effect.

// Fakes a log track in the relevant context (site course or course module).

if ($cmid) {
    $cm = $DB->get_record('course_modules', array('id' => $cmid));
    $event = \block_use_stats\event\block_use_stats_keepalive::create_from_cm($cm);
    $event->trigger();
} else {
    $event = \block_use_stats\event\block_use_stats_keepalive::create_from_cm(null);
    $event->trigger();
}