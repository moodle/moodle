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
 * Adaptive testing main view page script.
 *
 * @package    mod_adaptivequiz
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot.'/mod/adaptivequiz/locallib.php');

use mod_adaptivequiz\local\report\questions_difficulty_range;
use mod_adaptivequiz\local\report\users_attempts\filter\filter;
use mod_adaptivequiz\local\report\users_attempts\filter\filter_form;
use mod_adaptivequiz\local\report\users_attempts\filter\filter_options;
use mod_adaptivequiz\local\report\users_attempts\user_preferences\filter_user_preferences;
use mod_adaptivequiz\local\user_attempts_table;
use mod_adaptivequiz\local\report\users_attempts\users_attempts_table;
use mod_adaptivequiz\local\report\users_attempts\user_preferences\user_preferences_form;
use mod_adaptivequiz\local\report\users_attempts\user_preferences\user_preferences_repository;
use mod_adaptivequiz\local\report\users_attempts\user_preferences\user_preferences;
use mod_adaptivequiz\output\user_attempt_summary;

$id = optional_param('id', 0, PARAM_INT);
$downloadusersattempts = optional_param('download', '', PARAM_ALPHA);
$n  = optional_param('n', 0, PARAM_INT);
$resetfilter = optional_param('resetfilter', 0, PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('adaptivequiz', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $adaptivequiz  = $DB->get_record('adaptivequiz', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($n) {
    $adaptivequiz  = $DB->get_record('adaptivequiz', ['id' => $n], '*', MUST_EXIST);
    $course     = $DB->get_record('course', ['id' => $adaptivequiz->course], '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('adaptivequiz', $adaptivequiz->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('invalidarguments');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/adaptivequiz/view.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->add_body_class('limitedwidth');

/** @var mod_adaptivequiz_renderer $renderer */
$renderer = $PAGE->get_renderer('mod_adaptivequiz');

$canviewattemptsreport = has_capability('mod/adaptivequiz:viewreport', $context);
if ($canviewattemptsreport) {
    $reportuserprefs = user_preferences_repository::get();

    $reportuserprefsform = new user_preferences_form($PAGE->url->out());
    if ($prefsformdata = $reportuserprefsform->get_data()) {
        $reportuserprefs = user_preferences::from_plain_object($prefsformdata);

        if (!$reportuserprefs->persistent_filter() && $reportuserprefs->has_filter_preference()) {
            $reportuserprefs = $reportuserprefs->without_filter_preference();
        }

        user_preferences_repository::save($reportuserprefs);
    }
    $reportuserprefsform->set_data($reportuserprefs->as_array());

    $filter = filter::from_vars($adaptivequiz->id, groups_get_activity_group($cm, true));
    if ($resetfilter) {
        $filter->fill_from_array(['users' => filter_options::users_option_default(),
            'includeinactiveenrolments' => filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT]);
    }

    $reportfilterform = new filter_form($PAGE->url->out(), ['actionurl' => $PAGE->url]);
    if ($reportuserprefs->persistent_filter() && $reportuserprefs->has_filter_preference()) {
        $filter->fill_from_preference($reportuserprefs->filter());
        $reportfilterform->set_data($reportuserprefs->filter()->as_array());
    }
    if ($resetfilter) {
        $filterdefaultsarray = ['users' => filter_options::users_option_default(),
            'includeinactiveenrolments' => filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT];

        $filter->fill_from_array($filterdefaultsarray);

        if ($reportuserprefs->persistent_filter()) {
            user_preferences_repository::save(
                $reportuserprefs->with_filter_preference(filter_user_preferences::from_array($filterdefaultsarray))
            );
        }

        $reportfilterform->set_data($filterdefaultsarray);
    }
    if ($filterformdata = $reportfilterform->get_data()) {
        $filter->fill_from_array((array) $filterformdata);

        if ($reportuserprefs->persistent_filter()) {
            user_preferences_repository::save(
                $reportuserprefs->with_filter_preference(filter_user_preferences::from_array((array) $filterformdata))
            );
        }
    }

    $attemptsreporttable = new users_attempts_table($renderer, $cm->id,
        questions_difficulty_range::from_activity_instance($adaptivequiz), $PAGE->url, $context, $filter);
    $attemptsreporttable->is_downloading($downloadusersattempts,
        get_string('reportattemptsdownloadfilename', 'adaptivequiz', format_string($adaptivequiz->name)));
    if ($attemptsreporttable->is_downloading()) {
        $attemptsreporttable->out(1, false);
        exit;
    }
}

$event = \mod_adaptivequiz\event\course_module_viewed::create([
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
]);
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $adaptivequiz);
$event->trigger();

$PAGE->set_title(format_string($adaptivequiz->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (has_capability('mod/adaptivequiz:attempt', $context)) {
    $completedattemptscount = adaptivequiz_count_user_previous_attempts($adaptivequiz->id, $USER->id);

    echo $renderer->container_start('attempt-controls-or-notification-container pb-3');
    echo $renderer->attempt_controls_or_notification($cm->id,
        adaptivequiz_allowed_attempt($adaptivequiz->attempts, $completedattemptscount), $adaptivequiz->browsersecurity);
    echo $renderer->container_end();

    $allattemptscount = $DB->count_records('adaptivequiz_attempt',
        ['instance' => $adaptivequiz->id, 'userid' => $USER->id]);
    if ($allattemptscount && $adaptivequiz->attempts == 1) {
        $sql = 'SELECT id, attemptstate, measure, timemodified
            FROM {adaptivequiz_attempt}
            WHERE instance = ? AND userid = ?
            ORDER BY timemodified DESC';
        if ($userattempts = $DB->get_records_sql($sql, [$adaptivequiz->id, $USER->id], 0, 1)) {
            $userattempt = $userattempts[array_key_first($userattempts)];

            echo $renderer->heading(get_string('attempt_summary', 'adaptivequiz'), 3, 'text-center');
            echo $renderer->render(user_attempt_summary::from_db_records($userattempt, $adaptivequiz));
        }
    }
    if ($allattemptscount && $adaptivequiz->attempts != 1) {
        echo $renderer->heading(get_string('attemptsuserprevious', 'adaptivequiz'), 3);

        $attemptstable = new user_attempts_table($renderer);
        $attemptstable->init($PAGE->url, $adaptivequiz, $USER->id);
        $attemptstable->out(10, false);
    }
    if (!$allattemptscount) {
        echo html_writer::div(get_string('attemptsusernoprevious', 'adaptivequiz'), 'alert alert-info text-center');
    }
}

if ($canviewattemptsreport) {
    echo $renderer->heading(get_string('activityreports', 'adaptivequiz'), '3', 'text-center');

    groups_print_activity_menu($cm, new moodle_url('/mod/adaptivequiz/view.php', ['id' => $cm->id]));

    echo $renderer->container_start('usersattemptstable-wrapper');
    $attemptsreporttable->out($reportuserprefs->rows_per_page(), $reportuserprefs->show_initials_bar());
    echo $renderer->container_end();

    $reportuserprefsform->display();

    $reportfilterform->display();

    $resetfilterul = $PAGE->url;
    $resetfilterul->param('resetfilter', 1);
    echo $renderer->reset_users_attempts_filter_action($resetfilterul);
}

echo $OUTPUT->footer();
