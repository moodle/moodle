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
 * Post installation code for enrol_lti.
 *
 * @package    enrol_lti
 * @copyright  2022 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Stub for database installation.
 */
function xmldb_enrol_lti_install() {
    global $CFG, $OUTPUT;

    // LTI 1.3: Set a private key for this site (which is acting as a tool in LTI 1.3).
    require_once($CFG->dirroot . '/enrol/lti/upgradelib.php');

    $warning = enrol_lti_verify_private_key();
    if (!empty($warning)) {
        echo $OUTPUT->notification($warning, 'notifyproblem');
    }
}
