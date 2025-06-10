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
require_once('material_form.php');

require_login();

if (!cps_material::is_enabled()) {
    print_error('not_enabled', 'block_cps', '', cps_material::name());
}

if (!ues_user::is_teacher()) {
    print_error('not_teacher', 'block_cps');
}

$teacher = ues_teacher::get(array('userid' => $USER->id));

$nonprimaries = (bool) get_config('block_cps', 'material_nonprimary');

$sections = $teacher->sections(!$nonprimaries);

if (empty($sections)) {
    print_error('no_section', 'block_cps');
}

$s = ues::gen_str('block_cps');

$blockname = $s('pluginname');
$heading = cps_material::name();

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_heading($blockname . ': '. $heading);
$PAGE->navbar->add($blockname);
$PAGE->navbar->add($heading);
$PAGE->set_title($heading);
$PAGE->set_url('/blocks/cps/material.php');

$form = new material_form(null, array('sections' => $sections));

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $form->get_data()) {
    $fields = get_object_vars($data);

    $currentselections = cps_material::get_all(array('userid' => $USER->id));

    foreach ($fields as $name => $value) {
        if (!preg_match('/^material_(\d+)/', $name, $matches)) {
            continue;
        }

        $params = array('userid' => $USER->id, 'courseid' => $matches[1]);

        $material = cps_material::get($params);

        if (!$material) {
            $material = new cps_material();
            $material->fill_params($params);
        }

        $material->save();
        $material->apply();

        unset($currentselections[$material->id]);
    }

    // Remove deselected.
    foreach ($currentselections as $material) {
        $material->unapply();
        $material->delete($material->id);
    }

    $success = true;
}

$allmaterials = cps_material::get_all(array('userid' => $USER->id));
$data = array();
foreach ($allmaterials as $material) {
    $data['material_' . $material->courseid] = 1;
}
$form->set_data($data);

echo $OUTPUT->header();
echo $OUTPUT->heading_with_help($heading, 'material', 'block_cps');

if (isset($success) and $success) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
}

$form->display();

echo $OUTPUT->footer();