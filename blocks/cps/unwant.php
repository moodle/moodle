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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('classes/lib.php');
require_once('unwant_form.php');

require_login();

if (!cps_unwant::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_unwant::name());
}

if (!ues_user::is_teacher()) {
    print_error('not_teacher', 'block_cps');
}

$teacher = ues_teacher::get(array('userid' => $USER->id));

$sections = $teacher->sections(true);

if (empty($sections)) {
    print_error('no_section', 'block_cps');
}

$PAGE->requires->jquery();
$PAGE->requires->js('/blocks/cps/js/unwanted.js');

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_unwant::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': '. $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/cps/unwant.php');

$form = new unwant_form(null, array('sections' => $sections));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $form->get_data()) {

    $unwants = cps_unwant::get_all(array('userid' => $USER->id));

    // Perform Selected.
    $fields = get_object_vars($data);
    foreach ($fields as $name => $value) {
        if (preg_match('/^section_(\d+)/', $name, $matches)) {
            $sectionid = $matches[1];

            $params = array('userid' => $USER->id, 'sectionid' => $sectionid);
            $unwant = cps_unwant::get($params);

            if (!$unwant) {
                $unwant = new cps_unwant();
                $unwant->fill_params($params);
            }

            $unwant->save();
            $unwant->apply();

            unset($unwants[$unwant->id]);
        }
    }

    // Erase deselected.
    foreach ($unwants as $unwant) {
        cps_unwant::delete($unwant->id);
        $unwant->unapply();
    }

    $success = true;
}

$unwants = cps_unwant::get_all(array('userid' => $USER->id));
$formdata = array();

foreach ($unwants as $unwant) {
    $formdata['section_' . $unwant->sectionid] = 1;
}

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'unwant', 'block_cps');

if (isset($success) and $success) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
}

$form->set_data($formdata);
$form->display();

echo $OUTPUT->footer();
