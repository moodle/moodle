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
 * View model insights.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$contextid = required_param('contextid', PARAM_INT);
$modelid = optional_param('modelid', false, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 100, PARAM_INT);

if ($perpage > 1000) {
    $perpage = 1000;
}

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
if ($context->contextlevel < CONTEXT_COURSE) {
    // Only for higher levels than course.
    $PAGE->set_context($context);
}
\core_analytics\manager::check_can_list_insights($context);

// Get all models that are enabled, trained and have predictions at this context.
$othermodels = \core_analytics\manager::get_all_models(true, true, $context);
if (!$modelid && count($othermodels)) {
    // Autoselect the only available model.
    $model = reset($othermodels);
    $modelid = $model->get_id();
}
if ($modelid) {
    unset($othermodels[$modelid]);
}

$params = array('contextid' => $contextid);
$url = new \moodle_url('/report/insights/insights.php', $params);
if ($modelid) {
    $url->param('modelid', $modelid);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$renderer = $PAGE->get_renderer('report_insights');

// No models with insights available at this context level.
if (!$modelid) {
    echo $renderer->render_no_insights($context);
    exit(0);
}

$model = new \core_analytics\model($modelid);

$insightinfo = new stdClass();
$insightinfo->contextname = $context->get_context_name();
$insightinfo->insightname = $model->get_target()->get_name();
$title = get_string('insightinfo', 'analytics', $insightinfo);

if (!$model->is_enabled() && !has_capability('moodle/analytics:managemodels', $context)) {
    echo $renderer->render_model_disabled($insightinfo);
    exit(0);
}

if (!$model->uses_insights()) {
    echo $renderer->render_no_insights_model($context);
    exit(0);
}

$PAGE->set_title($title);
$PAGE->set_heading($title);

echo $OUTPUT->header();

$renderable = new \report_insights\output\insights_list($model, $context, $othermodels, $page, $perpage);
echo $renderer->render($renderable);

echo $OUTPUT->footer();
