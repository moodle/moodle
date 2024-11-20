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
 * @package   tool_redocerts
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once($CFG->dirroot.'/admin/tool/redocerts/lib.php');

admin_externalpage_setup('toolredocerts');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pageheader', 'tool_redocerts'));

$form = new tool_redocerts_form();

if (!$data = $form->get_data()) {
    $form->display();
    echo $OUTPUT->footer();
    die();
}

// Scroll to the end when finished.
$PAGE->requires->js_init_code("window.scrollTo(0, 5000000);");

echo $OUTPUT->box_start();
do_redocerts($data->user, $data->course, $data->company, $data->idnumber, $data->fromdate, $data->todate, $data->userid, $data->courseid, $data->companyid);
echo $OUTPUT->box_end();

// Course caches are now rebuilt on the fly.

echo $OUTPUT->continue_button(new moodle_url('/admin/index.php'));

echo $OUTPUT->footer();
