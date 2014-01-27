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
 * Displays live view of recent logs
 *
 * This file generates live view of recent logs.
 *
 * @package    report_loglive
 * @copyright  2011 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/course/lib.php');

if (!defined('REPORT_LOGLIVE_REFRESH')) {
    define('REPORT_LOGLIVE_REFRESH', 60); // Seconds
}

$id      = optional_param('id', $SITE->id, PARAM_INT);
$page    = optional_param('page', 0, PARAM_INT);
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

if ($course->id == SITEID) {
    require_login();
    $PAGE->set_context(context_system::instance());
} else {
    require_login($course);
}

$context = context_course::instance($course->id);
require_capability('report/loglive:view', $context);

$strlivelogs = get_string('livelogs', 'report_loglive');
if (!empty($page)) {
    $strlogs = get_string('logs'). ": ". get_string('page', 'report_loglive', $page + 1);
} else {
    $strlogs = get_string('logs');
}

if ($inpopup) {
    \core\session\manager::write_close();

    $date = time() - 3600;

    $url = new moodle_url('/report/loglive/index.php', array('id'=>$course->id, 'user'=>0, 'date'=>$date, 'inpopup'=>1));

    $strupdatesevery = get_string('updatesevery', 'moodle', REPORT_LOGLIVE_REFRESH);

    $coursename = format_string($course->fullname, true, array('context'=>$context));

    $PAGE->set_url($url);
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title("$coursename: $strlivelogs ($strupdatesevery)");
    $PAGE->set_periodic_refresh_delay(REPORT_LOGLIVE_REFRESH);
    $PAGE->set_heading($strlivelogs);
    echo $OUTPUT->header();

    // Trigger a content view event.
    $event = \report_loglive\event\content_viewed::create(array('courseid' => $course->id,
                                                                'other'    => array('content' => 'loglive')));
    $event->set_page_detail();
    $event->set_legacy_logdata(array($course->id, 'course', 'report live', "report/loglive/index.php?id=$course->id", $course->id));
    $event->trigger();

    print_log($course, 0, $date, "l.time DESC", $page, 500, $url);

    echo $OUTPUT->footer();
    exit;
}


if ($course->id == SITEID) {
    admin_externalpage_setup('reportloglive', '', null, '', array('pagelayout'=>'report'));
    echo $OUTPUT->header();

} else {
    $PAGE->set_url('/report/log/live.php', array('id'=>$course->id));
    $PAGE->set_title($course->shortname .': '. $strlivelogs);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
}

echo $OUTPUT->heading(get_string('pluginname', 'report_loglive'));

echo $OUTPUT->container_start('info');
$link = new moodle_url('/report/loglive/index.php', array('id'=>$course->id, 'inpopup'=>1));
echo $OUTPUT->action_link($link, $strlivelogs, new popup_action('click', $link, 'livelog', array('height' => 500, 'width' => 800)));
echo $OUTPUT->container_end();

echo $OUTPUT->footer();
