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
 * Report of unsupported role assignments,
 * unsupported role assignments can be dropped from here.
 *
 * @package    tool
 * @subpackage unsuproles
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);

$syscontext = context_system::instance();

require_login();
admin_externalpage_setup('toolunsuproles'); // checks permissions specified in settings.php

if ($action === 'delete') {
    $contextlevel = required_param('contextlevel', PARAM_INT);
    $roleid       = required_param('roleid', PARAM_INT);
    $confirm      = optional_param('confirm', 0, PARAM_BOOL);

    $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

    if ($confirm and confirm_sesskey()) {
        $sql = "SELECT ra.*
                  FROM {role_assignments} ra
                  JOIN {context} c ON c.id = ra.contextid
             LEFT JOIN {role_context_levels} rcl ON (rcl.roleid = ra.roleid AND rcl.contextlevel = c.contextlevel)
                 WHERE rcl.id IS NULL AND ra.roleid = :roleid AND c.contextlevel = :contextlevel";
        $ras = $DB->get_records_sql($sql, array('roleid'=>$roleid, 'contextlevel'=>$contextlevel));
        foreach ($ras as $ra) {
            if (!empty($ra->component)) {
                //bad luck, we can not mess with plugin ras!
                //TODO: explain why not possible to remove ras
                continue;
            }
            role_unassign($ra->roleid, $ra->userid, $ra->contextid);
        }
        redirect($PAGE->url);
    }
    //show confirmation
    echo $OUTPUT->header();
    $yesurl = new moodle_url($PAGE->url, array('roleid'=>$roleid, 'contextlevel'=>$contextlevel, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey()));
    $levelname = get_contextlevel_name($contextlevel);
    $rolename = format_string($role->name);
    $message = get_string('confirmdelete', 'tool_unsuproles', array('level'=>$levelname, 'role'=>$rolename));
    echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
    echo $OUTPUT->footer();
    die();
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'tool_unsuproles'));

$sql = "SELECT r.id AS roleid, c.contextlevel, r.sortorder, COUNT(ra.id) AS racount
          FROM {role} r
          JOIN {role_assignments} ra ON ra.roleid = r.id
          JOIN {context} c ON c.id = ra.contextid
     LEFT JOIN {role_context_levels} rcl ON (rcl.roleid = r.id AND rcl.contextlevel = c.contextlevel)
         WHERE rcl.id IS NULL
      GROUP BY r.id, c.contextlevel, r.sortorder
      ORDER BY c.contextlevel ASC, r.sortorder ASC";
//print the overview table

$problems = array();
$rs = $DB->get_recordset_sql($sql);
foreach ($rs as $problem) {
    $problems[] = $problem;
}
$rs->close();

if (!$problems) {
    echo $OUTPUT->notification(get_string('noprolbems', 'tool_unsuproles'), 'notifysuccess');
} else {
    $roles = get_all_roles();
    $data = array();
    foreach ($problems as $problem) {
        $levelname = get_contextlevel_name($problem->contextlevel);
        $rolename = role_get_name($roles[$problem->roleid]);
        //TODO: show list of users if count low
        $count = $problem->racount;
        $edit = array();
        $aurl = new moodle_url('/admin/roles/define.php', array('roleid'=>$problem->roleid, 'action'=>'edit'));
        $edit[] = html_writer::link($aurl, html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/edit'), 'alt'=>get_string('edit'), 'class'=>'smallicon')));
        $aurl = new moodle_url($PAGE->url, array('roleid'=>$problem->roleid, 'contextlevel'=>$problem->contextlevel, 'action'=>'delete'));
        $edit[] = html_writer::link($aurl, html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'smallicon')));
        $data[] = array($levelname, $rolename, $count, implode('&nbsp;', $edit));
    }
    $table = new html_table();
    $table->head  = array(get_string('contextlevel', 'tool_unsuproles'), get_string('role'), get_string('count', 'tool_unsuproles'), get_string('edit'));
    $table->size  = array('40%', '40%', '10%', '10%');
    $table->align = array('left', 'left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    echo html_writer::table($table);
}

echo $OUTPUT->footer();