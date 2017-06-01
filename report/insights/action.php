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
 * Forwards the user to the action they selected.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$predictionid = required_param('predictionid', PARAM_INT);
$actionname = required_param('action', PARAM_ALPHANUMEXT);
$forwardurl = required_param('forwardurl', PARAM_LOCALURL);

if (!$predictionobj = $DB->get_record('analytics_predictions', array('id' => $predictionid))) {
    throw new \moodle_exception('errorpredictionnotfound', 'report_insights');
}

$context = context::instance_by_id($predictionobj->contextid);

if ($context->contextlevel === CONTEXT_MODULE) {
    list($course, $cm) = get_module_from_cmid($context->instanceid);
    require_login($course, true, $cm);
} else if ($context->contextlevel >= CONTEXT_COURSE) {
    $coursecontext = $context->get_course_context(true);
    require_login($coursecontext->instanceid);
} else {
    require_login();
    $PAGE->set_context($context);
}

require_capability('moodle/analytics:listinsights', $context);

$params = array('predictionid' => $predictionobj->id, 'action' => $actionname, 'forwardurl' => $forwardurl);
$url = new \moodle_url('/report/insights/action.php', $params);

$model = new \core_analytics\model($predictionobj->modelid);
$sampledata = $model->prediction_sample_data($predictionobj);
$prediction = new \core_analytics\prediction($predictionobj, $sampledata);

$PAGE->set_url($url);

// Check that the provided action exists.
$actions = $model->get_target()->prediction_actions($prediction);
if (!isset($actions[$actionname])) {
    throw new \moodle_exception('errorunknownaction', 'report_insights');
}

$modelready = $model->is_enabled() && $model->is_trained() && $model->predictions_exist($context);
if (!$modelready && !has_capability('moodle/analytics:managemodels', $context)) {

    $PAGE->set_pagelayout('report');

    // We don't want to disclose the name of the model if it has not been enabled.
    $PAGE->set_title($context->get_context_name());
    $PAGE->set_heading($context->get_context_name());
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('disabledmodel', 'analytics'), \core\output\notification::NOTIFY_INFO);
    echo $OUTPUT->footer();
    exit(0);
}

$eventdata = array (
    'context' => $context,
    'objectid' => $predictionid,
    'other' => array('actionname' => $actionname)
);
\core_analytics\event\action_clicked::create($eventdata)->trigger();

redirect($forwardurl);
