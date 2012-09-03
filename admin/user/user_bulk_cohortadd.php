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
 * script for bulk user multi cohort add
 *
 * @package    core
 * @subpackage user
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('user_bulk_cohortadd_form.php');
require_once("$CFG->dirroot/cohort/lib.php");

$sort = optional_param('sort', 'fullname', PARAM_ALPHA);
$dir  = optional_param('dir', 'asc', PARAM_ALPHA);

admin_externalpage_setup('userbulk');
require_capability('moodle/cohort:assign', context_system::instance());

$users = $SESSION->bulk_users;

$strnever = get_string('never');

$cohorts = array(''=>get_string('choosedots'));
$allcohorts = $DB->get_records('cohort');
foreach ($allcohorts as $c) {
    if (!empty($c->component)) {
        // external cohorts can not be modified
        continue;
    }
    $context = context::instance_by_id($c->contextid);
    if (!has_capability('moodle/cohort:assign', $context)) {
        continue;
    }

    if (empty($c->idnumber)) {
        $cohorts[$c->id] = format_string($c->name);
    } else {
        $cohorts[$c->id] = format_string($c->name) . ' [' . $c->idnumber . ']';
    }
}
unset($allcohorts);

if (count($cohorts) < 2) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('bulkadd', 'core_cohort'));
    echo $OUTPUT->notification(get_string('bulknocohort', 'core_cohort'));
    echo $OUTPUT->continue_button(new moodle_url('/admin/user/user_bulk.php'));
    echo $OUTPUT->footer();
    die;
}

$countries = get_string_manager()->get_list_of_countries(true);
foreach ($users as $key => $id) {
    $user = $DB->get_record('user', array('id'=>$id, 'deleted'=>0), 'id, firstname, lastname, username, email, country, lastaccess, city');
    $user->fullname = fullname($user, true);
    $user->country = @$countries[$user->country];
    unset($user->firstname);
    unset($user->lastname);
    $users[$key] = $user;
}
unset($countries);

$mform = new user_bulk_cohortadd_form(null, $cohorts);

if (empty($users) or $mform->is_cancelled()) {
    redirect(new moodle_url('/admin/user/user_bulk.php'));

} else if ($data = $mform->get_data()) {
    // process request
    foreach ($users as $user) {
        if (!$DB->record_exists('cohort_members', array('cohortid'=>$data->cohort, 'userid'=>$user->id))) {
            cohort_add_member($data->cohort, $user->id);
        }
    }
    redirect(new moodle_url('/admin/user/user_bulk.php'));
}

// Need to sort by date
function sort_compare($a, $b) {
    global $sort, $dir;
    if ($sort == 'lastaccess') {
        $rez = $b->lastaccess - $a->lastaccess;
    } else {
        $rez = strcasecmp(@$a->$sort, @$b->$sort);
    }
    return $dir == 'desc' ? -$rez : $rez;
}
usort($users, 'sort_compare');

$table = new html_table();
$table->width = "95%";
$columns = array('fullname', 'email', 'city', 'country', 'lastaccess');
foreach ($columns as $column) {
    $strtitle = get_string($column);
    if ($sort != $column) {
        $columnicon = '';
        $columndir = 'asc';
    } else {
        $columndir = ($dir == 'asc') ? 'desc' : 'asc';
        $columnicon = ' <img src="'.$OUTPUT->pix_url('t/'.($dir == 'asc' ? 'down' : 'up' )).'" alt="" />';
    }
    $table->head[] = '<a href="user_bulk_cohortadd.php?sort='.$column.'&amp;dir='.$columndir.'">'.$strtitle.'</a>'.$columnicon;
    $table->align[] = 'left';
}

foreach ($users as $user) {
    $table->data[] = array (
        '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->fullname.'</a>',
        $user->email,
        $user->city,
        $user->country,
        $user->lastaccess ? format_time(time() - $user->lastaccess) : $strnever
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('bulkadd', 'core_cohort'));

echo html_writer::table($table);

echo $OUTPUT->box_start();
$mform->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
