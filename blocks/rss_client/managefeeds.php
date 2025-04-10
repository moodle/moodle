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
 * @package   block_rss_client
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

require_login();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$deleterssid = optional_param('deleterssid', 0, PARAM_INT);

if ($courseid == SITEID) {
    $courseid = 0;
}
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $PAGE->set_course($course);
    $context = $PAGE->context;
} else {
    $context = context_system::instance();
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
$baseurl = new moodle_url('/blocks/rss_client/managefeeds.php', $urlparams);
$PAGE->set_url($baseurl);

if ($managesharedfeeds) {
    $select = '(userid = :userid OR shared = 1)';
} else {
    $select = 'userid = :userid';
}

// Process any actions
if ($deleterssid && confirm_sesskey()) {

    $deleterssid = $DB->get_field_select('block_rss_client', 'id', "id = :id AND {$select}", [
        'id' => $deleterssid,
        'userid' => $USER->id
    ], MUST_EXIST);

    $DB->delete_records('block_rss_client', ['id' => $deleterssid]);

    redirect($PAGE->url, get_string('feeddeleted', 'block_rss_client'));
}

// Display the list of feeds.
$feeds = $DB->get_records_select('block_rss_client', $select, ['userid' => $USER->id], $DB->sql_order_by_text('title'));

$strmanage = get_string('managefeeds', 'block_rss_client');

$PAGE->set_pagelayout('standard');
$PAGE->set_title($strmanage);
$PAGE->set_heading($strmanage);

$managefeeds = new moodle_url('/blocks/rss_client/managefeeds.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_rss_client'));
$PAGE->navbar->add(get_string('managefeeds', 'block_rss_client'), $managefeeds);
echo $OUTPUT->header();

$table = new flexible_table('rss-display-feeds');

$table->define_columns(array('feed', 'actions'));
$table->define_headers(array(get_string('feed', 'block_rss_client'), get_string('actions', 'moodle')));
$table->define_baseurl($baseurl);

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'rssfeeds');
$table->set_attribute('class', 'table generaltable');
$table->column_class('feed', 'feed');
$table->column_class('actions', 'actions');

$table->setup();

foreach($feeds as $feed) {
    if (!empty($feed->preferredtitle)) {
        $feedtitle = s($feed->preferredtitle);
    } else {
        $feedtitle = $feed->title;
    }

    $viewlink = html_writer::link($CFG->wwwroot .'/blocks/rss_client/viewfeed.php?rssid=' . $feed->id . $extraparams, $feedtitle);

    $feedinfo = '<div class="title">' . $viewlink . '</div>' .
        '<div class="url">' . html_writer::link($feed->url, $feed->url) .'</div>' .
        '<div class="description">' . $feed->description . '</div>';
    if ($feed->skipuntil) {
        $skipuntil = userdate($feed->skipuntil, get_string('strftimedatetime', 'langconfig'));
        $skipmsg = get_string('failedfeed', 'block_rss_client', $skipuntil);
        $notification = new \core\output\notification($skipmsg, 'error');
        $notification->set_show_closebutton(false);
        $feedinfo .= $OUTPUT->render($notification);
    }

    $editurl = new moodle_url('/blocks/rss_client/editfeed.php?rssid=' . $feed->id . $extraparams);
    $editaction = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));

    $deleteurl = new moodle_url('/blocks/rss_client/managefeeds.php?deleterssid=' . $feed->id . '&sesskey=' . sesskey() . $extraparams);
    $deleteicon = new pix_icon('t/delete', get_string('delete'));
    $deleteaction = $OUTPUT->action_icon($deleteurl, $deleteicon, new confirm_action(get_string('deletefeedconfirm', 'block_rss_client')));

    $feedicons = $editaction . ' ' . $deleteaction;

    $table->add_data(array($feedinfo, $feedicons));
}

$table->print_html();

$url = $CFG->wwwroot . '/blocks/rss_client/editfeed.php?' . substr($extraparams, 1);
echo '<div class="actionbuttons">' . $OUTPUT->single_button($url, get_string('addnewfeed', 'block_rss_client'), 'get') . '</div>';


if ($returnurl) {
    echo '<div class="backlink">' . html_writer::link($returnurl, get_string('back')) . '</div>';
}

echo $OUTPUT->footer();
