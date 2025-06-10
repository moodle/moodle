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

require_once('../../config.php');
require_once('lib.php');
require_once('query_form.php');

require_login();

$system = $DB->get_record('course', array('id' => SITEID), '*', MUST_EXIST);

$context = context_system::instance();

$shortname = optional_param('shortname', null, PARAM_TEXT);
$flash = optional_param('flash', null, PARAM_INT);

require_capability('block/post_grades:canconfigure', $context);

$s = ues::gen_str('block_post_grades');

$pluginname = $s('pluginname');
$heading = $s('reset_posting');

$baseurl = new moodle_url('/blocks/post_grades/reset.php');
$adminurl = new moodle_url('/admin/settings.php', array(
    'section' => 'blocksettingpost_grades'
));

$title = "$system->shortname: $heading";

$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_heading($title);
$PAGE->set_title($title);
$PAGE->navbar->add($pluginname, $adminurl);
$PAGE->navbar->add($heading);

$queryform = new query_form();

if ($queryform->is_cancelled()) {
    redirect($adminurl);
}

if ($shortname) {
    $data = new stdClass();
    $data->shortname = $shortname;
    $queryform->set_data($data);

    $entries = post_grades::find_postings_by_shortname($shortname);

    $resetform = new reset_form(null, array(
        'shortname' => $shortname,
        'entries' => $entries
    ));

    if ($resetform->is_cancelled()) {
        redirect($baseurl);
    } else if ($data = $resetform->get_data()) {
        $fields = (array)$data;
        unset($fields['submitbutton'], $fields['shortname']);

        $ids = implode(',', array_keys($fields));
        $DB->delete_records_select('block_post_grades_postings', "id IN ($ids)");

        redirect(new moodle_url($baseurl, array('flash' => 1)));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

if ($flash) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
}

$queryform->display();

if (!empty($resetform)) {
    $resetform->display();
}

echo $OUTPUT->footer();
