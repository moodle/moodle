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
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * On this page the administrator select if he wants to register on Moodle.org or
 * a specific hub
*/

require('../../config.php');

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/hublib.php');

admin_externalpage_setup('registrationindex');

$renderer = $PAGE->get_renderer('core', 'register');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('registeron', 'hub'), 3, 'main');
echo $renderer->registrationselector();
echo $OUTPUT->footer();
