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
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_libs();
require_once('provider.php');

require_login();

if (!is_siteadmin($USER->id)) {
    print_error('no_permission', 'local_online', '/my');
}

$provider = new online_enrollment_provider();

$confirmed = optional_param('confirm', null, PARAM_INT);

$semesters = ues_semester::in_session(time());

$baseurl = new moodle_url('/local/online/reprocess.php');

$s = ues::gen_str('local_online');

$pluginname = $s('pluginname');
$heading = $s('reprocess');

$adminplugin = new moodle_url('/admin/settings.php', array('section' => 'local_online'));

$PAGE->set_url($baseurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_title("$pluginname: $heading");
$PAGE->set_heading("$pluginname: $heading");
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add($pluginname, $adminplugin);
$PAGE->navbar->add($heading);

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

if ($confirmed) {
    $ues = enrol_get_plugin('ues');

    echo html_writer::start_tag('pre');
    $provider->postprocess($ues);
    echo html_writer::end_tag('pre');

    echo $OUTPUT->continue_button($adminplugin);

} else {

    $confirm = new moodle_url($baseurl, array('confirm' => 1));
    echo $OUTPUT->confirm($s('reprocess_confirm'), $confirm, $adminplugin);
}

echo $OUTPUT->footer();
