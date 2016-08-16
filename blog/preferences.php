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
 * Form page for blog preferences
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/blog/lib.php');
require_once('preferences_form.php');

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$modid    = optional_param('modid', null, PARAM_INT);
$userid   = optional_param('userid', null, PARAM_INT);
$tagid    = optional_param('tagid', null, PARAM_INT);
$groupid      = optional_param('groupid', null, PARAM_INT);

$url = new moodle_url('/blog/preferences.php');
if ($courseid !== SITEID) {
    $url->param('courseid', $courseid);
}
if ($modid !== null) {
    $url->param('modid', $modid);
}
if ($userid !== null) {
    $url->param('userid', $userid);
}
if ($tagid !== null) {
    $url->param('tagid', $tagid);
}
if ($groupid !== null) {
    $url->param('groupid', $groupid);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$sitecontext = context_system::instance();
$usercontext = context_user::instance($USER->id);
$PAGE->set_context($usercontext);
require_login($courseid);

if (empty($CFG->enableblogs)) {
    print_error('blogdisable', 'blog');
}

if (isguestuser()) {
    print_error('noguest');
}

// The preference is site wide not blog specific. Hence user should have permissions in site level.
require_capability('moodle/blog:view', $sitecontext);

// If data submitted, then process and store.

$mform = new blog_preferences_form('preferences.php');
$mform->set_data(array('pagesize' => get_user_preferences('blogpagesize')));

if (!$mform->is_cancelled() && $data = $mform->get_data()) {
    $pagesize = $data->pagesize;

    if ($pagesize < 1) {
        print_error('invalidpagesize');
    }
    set_user_preference('blogpagesize', $pagesize);
}

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/user/preferences.php');
}

$site = get_site();

$strpreferences = get_string('preferences');
$strblogs       = get_string('blogs', 'blog');

$title = "$site->shortname: $strblogs : $strpreferences";
$PAGE->set_title($title);
$PAGE->set_heading(fullname($USER));

echo $OUTPUT->header();

echo $OUTPUT->heading("$strblogs : $strpreferences", 2);

$mform->display();

echo $OUTPUT->footer();
