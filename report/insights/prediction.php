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
 * View a prediction.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$predictionid = required_param('id', PARAM_INT);

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

$params = array('id' => $predictionobj->id);
$url = new \moodle_url('/reports/insights/prediction.php', $params);

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$renderer = $PAGE->get_renderer('report_insights');

$model = new \core_analytics\model($predictionobj->modelid);
$sampledata = $model->prediction_sample_data($predictionobj);
$prediction = new \core_analytics\prediction($predictionobj, $sampledata);

$insightinfo = new stdClass();
$insightinfo->contextname = $context->get_context_name();
$insightinfo->insightname = $model->get_target()->get_name();
$title = get_string('insightinfo', 'report_insights', $insightinfo);

$modelready = $model->$model->is_enabled() && $model->is_trained() && $model->predictions_exist($context);
if (!$modelready && !has_capability('moodle/analytics:managemodels', $context)) {
    echo $renderer->render_model_disabled($insightinfo);
    exit(0);
}

$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

$renderable = new \core_analytics\output\prediction($prediction, $model);
echo $renderer->render($renderable);

echo $OUTPUT->footer();
