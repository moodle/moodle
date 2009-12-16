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
 * Script to let a user manage their RSS feeds.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

require_login();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$deleterssid = optional_param('deleterssid', 0, PARAM_INTEGER);

if ($courseid == SITEID) {
    $courseid = 0;
}
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $PAGE->set_course($course);
    $context = $PAGE->context;
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
    $PAGE->set_context($context);
}

$managesharedfeeds = has_capability('block/rss_client:manageanyfeeds', $context);
if (!$managesharedfeeds) {
    require_capability('block/rss_client:manageownfeeds', $context);
}

$urlparams = array();
$extraparams = '';
if ($courseid) {
    $urlparams['courseid'] = $courseid;
    $extraparams = '&courseid=' . $courseid;
}
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
    $extraparams = '&returnurl=' . $returnurl;
}
$PAGE->set_url('blocks/rss_client/managefeeds.php', $urlparams);

// Process any actions
if ($deleterssid && confirm_sesskey()) {
    $DB->delete_records('block_rss_client', array('id'=>$deleterssid));

    redirect($PAGE->url, get_string('feeddeleted', 'block_rss_client'));
}

// Display the list of feeds.
if ($managesharedfeeds) {
    $select = '(userid = ' . $USER->id . ' OR shared = 1)';
} else {
    $select = 'userid = ' . $USER->id;
}
$feeds = $DB->get_records_select('block_rss_client', $select, null, $DB->sql_order_by_text('title'));

$strmanage = get_string('managefeeds', 'block_rss_client');

$PAGE->set_pagelayout('form');
$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);

$settingsurl = new moodle_url($CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=blocksettingrss_client');
$managefeeds = new moodle_url($CFG->wwwroot . '/blocks/rss_client/managefeeds.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('feedstitle', 'block_rss_client'), $settingsurl);
$PAGE->navbar->add(get_string('managefeeds', 'block_rss_client'), $managefeeds);
echo $OUTPUT->header();

$table = new flexible_table('rss-display-feeds');

$table->define_columns(array('feed', 'actions'));
$table->define_headers(array(get_string('feed', 'block_rss_client'), get_string('actions', 'moodle')));

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'rssfeeds');
$table->set_attribute('class', 'generaltable generalbox');
$table->column_class('feed', 'feed');
$table->column_class('actions', 'actions');

$table->setup();

foreach($feeds as $feed) {
    if (!empty($feed->preferredtitle)) {
        $feedtitle = $feed->preferredtitle;
    } else {
        $feedtitle =  $feed->title;
    }

    $viewlink = new html_link();
    $viewlink->url = $CFG->wwwroot .'/blocks/rss_client/viewfeed.php?rssid=' . $feed->id . $extraparams;
    $viewlink->text = $feedtitle;

    $feedinfo = '<div class="title">' . $OUTPUT->link($viewlink) . '</div>' .
        '<div class="url">' . $OUTPUT->link($feed->url, $feed->url) .'</div>' .
        '<div class="description">' . $feed->description . '</div>';

    $editaction = new moodle_action_icon();
    $editaction->link->url = $CFG->wwwroot .'/blocks/rss_client/editfeed.php?rssid=' . $feed->id . $extraparams;
    $editaction->link->title = get_string('edit');
    $editaction->image->src = $OUTPUT->pix_url('t/edit');
    $editaction->image->alt = get_string('edit');

    $deleteaction = new moodle_action_icon();
    $deleteaction->link->url = $CFG->wwwroot .'/blocks/rss_client/managefeeds.php?deleterssid=' . $feed->id .
            '&sesskey=' . sesskey() . $extraparams;
    $deleteaction->link->title = get_string('delete');
    $deleteaction->image->src = $OUTPUT->pix_url('t/delete');
    $deleteaction->image->alt = get_string('delete');
    $deleteaction->link->add_confirm_action(get_string('deletefeedconfirm', 'block_rss_client'));

    $feedicons = $OUTPUT->action_icon($editaction) . ' ' . $OUTPUT->action_icon($deleteaction);

    $table->add_data(array($feedinfo, $feedicons));
}

$table->print_html();

$button = new html_form();
$button->method = 'get';
$button->url = $CFG->wwwroot . '/blocks/rss_client/editfeed.php?' . substr($extraparams, 1);
$button->showbutton = true;
$button->button->text = get_string('addnewfeed', 'block_rss_client');
echo '<div class="actionbuttons">' . $OUTPUT->button($button) . '</div>';


if ($returnurl) {
    echo '<div class="backlink">' . $OUTPUT->link($returnurl, get_string('back')) . '</div>';
}

echo $OUTPUT->footer();
