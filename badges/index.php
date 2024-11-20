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
 * Page for badges management
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

use core_badges\reportbuilder\local\systemreports\badges;
use core_reportbuilder\system_report_factory;

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');

$type       = required_param('type', PARAM_INT);
$courseid   = optional_param('id', 0, PARAM_INT);
$deactivate = optional_param('lock', 0, PARAM_INT);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$delete     = optional_param('delete', 0, PARAM_INT);
$archive    = optional_param('archive', 0, PARAM_INT);
$msg        = optional_param('msg', '', PARAM_TEXT);

require_login();

if (empty($CFG->enablebadges)) {
    throw new \moodle_exception('badgesdisabled', 'badges');
}

if (empty($CFG->badges_allowcoursebadges) && ($type == BADGE_TYPE_COURSE)) {
    throw new \moodle_exception('coursebadgesdisabled', 'badges');
}

if ($type == BADGE_TYPE_COURSE && !$courseid) {
    throw new \moodle_exception('courseidnotfound');
}

$urlparams = ['type' => $type];

if ($course = $DB->get_record('course', ['id' => $courseid])) {
    $urlparams['id'] = $course->id;
}

$returnurl = new moodle_url('/badges/index.php', $urlparams);
$PAGE->set_url($returnurl);
$PAGE->add_body_class('limitedwidth');

if ($type == BADGE_TYPE_SITE) {
    $title = get_string('sitebadges', 'badges');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('admin');
    $PAGE->set_heading(get_string('administrationsite'));
    navigation_node::override_active_url(new moodle_url('/badges/index.php', ['type' => BADGE_TYPE_SITE]), true);
    $eventotherparams = ['badgetype' => BADGE_TYPE_SITE];
} else {
    require_login($course);
    $coursecontext = context_course::instance($course->id);
    $title = get_string('coursebadges', 'badges');
    $PAGE->set_context($coursecontext);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_heading(format_string($course->fullname, true, ['context' => $coursecontext]));
    navigation_node::override_active_url(
        new moodle_url('/badges/index.php', ['type' => BADGE_TYPE_COURSE, 'id' => $course->id])
    );
    $eventotherparams = ['badgetype' => BADGE_TYPE_COURSE, 'courseid' => $course->id];
}

if (!has_any_capability([
        'moodle/badges:viewbadges',
        'moodle/badges:viewawarded',
        'moodle/badges:createbadge',
        'moodle/badges:awardbadge',
        'moodle/badges:configurecriteria',
        'moodle/badges:configuremessages',
        'moodle/badges:configuredetails',
        'moodle/badges:deletebadge'], $PAGE->context)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_title($title);

/** @var core_badges_renderer $output */
$output = $PAGE->get_renderer('core', 'badges');

if ($delete || $archive) {
    $badgeid = ($archive != 0) ? $archive : $delete;
    $badge = new badge($badgeid);
    require_capability('moodle/badges:deletebadge', $badge->get_context());
    if (!$confirm) {
        echo $output->header();
        // Archive this badge?
        echo $output->heading(get_string('archivebadge', 'badges', $badge->name));
        $archivebutton = $output->single_button(
                            new moodle_url($PAGE->url, ['archive' => $badge->id, 'confirm' => 1]),
                            get_string('archiveconfirm', 'badges'));
        echo $output->box(get_string('archivehelp', 'badges') . $archivebutton, 'generalbox');

        // Delete this badge?
        echo $output->heading(get_string('delbadge', 'badges', $badge->name));
        $deletebutton = $output->single_button(
                            new moodle_url($PAGE->url, ['delete' => $badge->id, 'confirm' => 1]),
                            get_string('delconfirm', 'badges'));
        echo $output->box(get_string('deletehelp', 'badges') . $deletebutton, 'generalbox');

        // Go back.
        echo $output->action_link($returnurl, get_string('cancel'));

        echo $output->footer();
        die();
    } else {
        require_sesskey();
        $archiveonly = ($archive != 0) ? true : false;
        $badge->delete($archiveonly);
        redirect($returnurl);
    }
}

if ($deactivate && has_capability('moodle/badges:configuredetails', $PAGE->context)) {
    require_sesskey();
    $badge = new badge($deactivate);
    if ($badge->is_locked()) {
        $badge->set_status(BADGE_STATUS_INACTIVE_LOCKED);
    } else {
        $badge->set_status(BADGE_STATUS_INACTIVE);
    }
    $msg = 'deactivatesuccess';
    $returnurl->param('msg', $msg);
    redirect($returnurl);
}

echo $OUTPUT->header();

echo $OUTPUT->container_start('badges-heading');
if ($type == BADGE_TYPE_SITE) {
    echo $OUTPUT->heading_with_help($title, 'sitebadges', 'badges');
} else {
    echo $OUTPUT->heading($title);
}
$actionbar = new \core_badges\output\standard_action_bar(
    page: $PAGE,
    type: $type,
    showaddbadge: true,
);
echo $output->render_tertiary_navigation($actionbar);
echo $OUTPUT->container_end();

echo $OUTPUT->box('', 'notifyproblem hide', 'check_connection');

if ($course && $course->startdate > time()) {
    echo $OUTPUT->notification(
        get_string('error:notifycoursedate', 'badges', userdate($course->startdate)),
        'warning'
    );
}

if ($msg !== '') {
    echo $OUTPUT->notification(get_string($msg, 'badges'), 'notifysuccess');
}

$report = system_report_factory::create(badges::class, $PAGE->context);

echo $report->output();
$PAGE->requires->js_call_amd('core_badges/actions', 'init');

// Trigger event, badge listing viewed.
$eventparams = ['context' => $PAGE->context, 'other' => $eventotherparams];
$event = \core\event\badge_listing_viewed::create($eventparams);
$event->trigger();

echo $OUTPUT->footer();
