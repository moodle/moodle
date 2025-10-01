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
 * Script to let a user view the output of a particular RSS feed.
 *
 * @package   block_rss_client
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir .'/simplepie/moodle_simplepie.php');

require_login();

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$rssid = required_param('rssid', PARAM_INT);

if ($courseid = SITEID) {
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

$urlparams = array('rssid' => $rssid);
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$PAGE->set_url('/blocks/rss_client/viewfeed.php', $urlparams);
$PAGE->set_pagelayout('popup');

if ($managesharedfeeds) {
    $select = 'id = :id AND (userid = :userid OR shared = 1)';
} else {
    $select = 'id = :id AND userid = :userid';
}

$rssrecord = $DB->get_record_select('block_rss_client', $select, [
    'id' => $rssid,
    'userid' => $USER->id,
], '*', MUST_EXIST);

$rss = new moodle_simplepie($rssrecord->url);
if ($rss->error()) {
    debugging($rss->error());
    throw new \moodle_exception('errorfetchingrssfeed');
}

$strviewfeed = get_string('viewfeed', 'block_rss_client');

$PAGE->set_title($strviewfeed);
$PAGE->set_heading($strviewfeed);

$managefeeds = new moodle_url('/blocks/rss_client/managefeeds.php', $urlparams);
$PAGE->navbar->add(get_string('blocks'));
$PAGE->navbar->add(get_string('pluginname', 'block_rss_client'));
$PAGE->navbar->add(get_string('managefeeds', 'block_rss_client'), $managefeeds);
$PAGE->navbar->add($strviewfeed);
echo $OUTPUT->header();

if (!empty($rssrecord->preferredtitle)) {
    $feedtitle = $rssrecord->preferredtitle;
} else {
    $feedtitle =  $rss->get_title();
}
echo '<table class="table-reboot" align="center" width="50%" cellspacing="1">' . "\n";
echo '<tr><td colspan="2"><strong>'. s($feedtitle) .'</strong></td></tr>'."\n";
foreach ($rss->get_items() as $item) {
    echo '<tr><td valign="middle">'."\n";
    echo '<a href="'.$item->get_link().'" target="_blank"><strong>';
    echo s($item->get_title());
    echo '</strong></a>'."\n";
    echo '</td>'."\n";
    echo '</tr>'."\n";
    echo '<tr><td colspan="2"><small>';
    echo format_text($item->get_description(), FORMAT_HTML) .'</small></td></tr>'."\n";
}
echo '</table>'."\n";

echo $OUTPUT->footer();
