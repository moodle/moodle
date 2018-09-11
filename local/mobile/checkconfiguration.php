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
 * Check the plugin configuration to see if everything is correctly set-up
 *
 * @package    local_mobile
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$PAGE->set_url(new moodle_url('/admin/settings.php?section=local_mobile'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

require_login();
require_sesskey();
require_capability('moodle/site:config', context_system::instance());

$strheading = get_string('checkpluginconfiguration', 'local_mobile');
$PAGE->navbar->add($strheading);

$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

echo $OUTPUT->header();

$table = new html_table();
$table->head = array(get_string('step', 'webservice'), get_string('status'), get_string('description'));
$table->colclasses = array('leftalign step', 'leftalign status', 'leftalign description');
$table->id = 'onesystemcontrol';
$table->attributes['class'] = 'admintable wsoverview generaltable';
$table->data = array();

// 1. Enable Web Services.
$row = array();
$url = new moodle_url("/admin/search.php?query=enablewebservices");
$row[0] = "1. " . html_writer::tag('a', get_string('enablews', 'webservice'),
                array('href' => $url));
$status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
if ($CFG->enablewebservices) {
    $status = get_string('yes');
}
$row[1] = $status;
$row[2] = get_string('enablewsdescription', 'webservice');
$table->data[] = $row;

// 2. Enable Mobile services.
$row = array();
$url = new moodle_url("/admin/settings.php?section=mobile");
$row[0] = "2. " . html_writer::tag('a', get_string('enablemobilewebservice', 'admin'), array('href' => $url));
$status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
if ($CFG->enablemobilewebservice) {
    $status = get_string('yes');
}
$row[1] = $status;
$enablemobiledocurl = new moodle_url(get_docs_url('Enable_mobile_web_services'));
$enablemobiledoclink = html_writer::link($enablemobiledocurl, new lang_string('documentation'));
$row[2] = get_string('configenablemobilewebservice', 'admin', $enablemobiledoclink);
$table->data[] = $row;

// 3. Enable the Moodle Mobile additional features service.
$row = array();
$service = $DB->get_record('external_services', array('shortname' => 'local_mobile'));
$url = new moodle_url("/admin/webservice/service.php?id=" . $service->id);
$row[0] = "3. " . html_writer::tag('a', get_string('enableadditionalservice', 'local_mobile'), array('href' => $url));
$status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
if ($service->enabled) {
    $status = get_string('yes');
}
$row[1] = $status;
$row[2] = get_string('enableadditionalservicedescription', 'local_mobile');
$table->data[] = $row;

// 4. Allow permissions.
$row = array();
$url = new moodle_url("/admin/roles/define.php?action=edit&roleid=" . $CFG->defaultuserroleid);
$row[0] = "4. " . html_writer::tag('a', get_string('allowpermissions', 'local_mobile'), array('href' => $url));
$status = html_writer::tag('span', get_string('no'), array('class' => 'statuscritical'));
if ($DB->record_exists('role_capabilities', array('permission' => CAP_ALLOW, 'roleid' => $CFG->defaultuserroleid,
                                                    'capability' => 'moodle/webservice:createtoken'))) {
    $status = get_string('yes');
}
$row[1] = $status;
$row[2] = get_string('allowpermissionsdescription', 'local_mobile');
$table->data[] = $row;


echo html_writer::table($table);

echo $OUTPUT->footer();
