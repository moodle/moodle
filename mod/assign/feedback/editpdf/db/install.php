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
 * Install code for the feedback_editpdf module.
 *
 * @package   assignfeedback_editpdf
 * @copyright 2013 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * EditPDF install code
 */
function xmldb_assignfeedback_editpdf_install() {
    global $CFG;

    // List of default stamps.
    $defaultstamps = array('smile.png', 'sad.png', 'tick.png', 'cross.png');

    // Stamp file object.
    $filerecord = new stdClass;
    $filerecord->component = 'assignfeedback_editpdf';
    $filerecord->contextid = context_system::instance()->id;
    $filerecord->userid    = get_admin()->id;
    $filerecord->filearea  = 'stamps';
    $filerecord->filepath  = '/';
    $filerecord->itemid    = 0;

    $fs = get_file_storage();

    // Load all default stamps.
    foreach ($defaultstamps as $stamp) {
        $filerecord->filename = $stamp;
        $fs->create_file_from_pathname($filerecord,
            $CFG->dirroot . '/mod/assign/feedback/editpdf/pix/' . $filerecord->filename);
    }
}
