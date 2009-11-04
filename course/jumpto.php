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
 * Jumps to a given relative or Moodle absolute URL.
 * Mostly used for accessibility.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require('../config.php');

$jump = optional_param('jump', '', PARAM_RAW);

$PAGE->set_url(new moodle_url($CFG->wwwroot.'/course/jumpto.php'));

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad');
}

if (strpos($jump, $CFG->wwwroot) === 0) {            // Anything on this site
    redirect(new moodle_url(urldecode($jump)));
} else if (preg_match('/^[a-z]+\.php\?/', $jump)) {
    redirect(new moodle_url(urldecode($jump)));
}

if(isset($_SERVER['HTTP_REFERER'])) {
    redirect(new moodle_url($_SERVER['HTTP_REFERER']));   // Return to sender, just in case
}

