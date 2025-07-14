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

use core\report_helper;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

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

if (!\core_analytics\manager::is_analytics_enabled()) {
    $renderer = $PAGE->get_renderer('report_insights');
    echo $renderer->render_analytics_disabled();
    exit(0);
}

\core_analytics\manager::check_can_list_insights($context);

// Get all models that are enabled, trained and have predictions at this context.
$othermodels = \core_analytics\manager::get_all_models(true, true, $context);
array_filter($othermodels, function($model) use ($context) {

    // Discard insights that are not linked unless you are a manager.
    if (!$model->get_target()->link_insights_report()) {
        try {
            \core_analytics\manager::check_can_manage_models();
        } catch (\required_capability_exception $e) {
            return false;
        }
    }
    return true;
});

if (!$modelid && count($othermodels)) {
    // Autoselect the only available model.
    $model = reset($othermodels);
    $modelid = $model->get_id();
}
if ($modelid) {
    unset($othermodels[$modelid]);
}

// The URL in navigation only contains the contextid.
$params = array('contextid' => $contextid);
$navurl = new \moodle_url('/report/insights/insights.php', $params);

// This is the real page url, we need it to include the modelid so pagination and
// other stuff works as expected.
$url = clone $navurl;
if ($modelid) {
    $url->param('modelid', $modelid);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

if ($context->contextlevel === CONTEXT_SYSTEM) {
    admin_externalpage_setup('reportinsights', '', $url->params(), $url->out(false), array('pagelayout' => 'report'));
} else if ($context->contextlevel === CONTEXT_USER) {
    $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);
    $PAGE->navigation->extend_for_user($user);
    $PAGE->add_report_nodes($user->id, array(
        'name' => get_string('insights', 'report_insights'),
        'url' => $url
    ));
}
$PAGE->navigation->override_active_url($navurl);

$renderer = $PAGE->get_renderer('report_insights');

// No models with insights available at this context level.
if (!$modelid) {
    echo $renderer->render_no_insights($context);
    exit(0);
}

$model = new \core_analytics\model($modelid);

if (!$model->get_target()->link_insights_report()) {

    // Only manager access if this target does not link the insights report.
    \core_analytics\manager::check_can_manage_models();
}

$insightinfo = new stdClass();
// Don't show prefix for course-level context.
$withprefix = $context->contextlevel <> CONTEXT_COURSE;
$insightinfo->contextname = $context->get_context_name($withprefix);
$insightinfo->insightname = $model->get_target()->get_name();

if (!$model->is_enabled()) {
    echo $renderer->render_model_disabled($insightinfo);
    exit(0);
}

if (!$model->uses_insights()) {
    echo $renderer->render_no_insights_model($context);
    exit(0);
}

if ($context->id == SYSCONTEXTID) {
    $PAGE->set_heading(get_site()->shortname);
} else {
    $PAGE->set_heading($insightinfo->contextname);
}
$PAGE->set_title($insightinfo->insightname);

// Some models generate one single prediction per context. We can directly show the prediction details in this case.
if ($model->get_analyser()::one_sample_per_analysable()) {

    // Param $perpage to 2 so we can detect if this model's analyser is using one_sample_per_analysable incorrectly.
    $predictionsdata = $model->get_predictions($context, true, 0, 2);
    if ($predictionsdata) {
        list($total, $predictions) = $predictionsdata;
        if ($total > 1) {
            throw new \coding_exception('This model\'s analyser processed more than one sample for a single analysable element.' .
                'Therefore, the analyser\'s one_sample_per_analysable() method should return false.');
        }
        $prediction = reset($predictions);
        $redirecturl = new \moodle_url('/report/insights/prediction.php', ['id' => $prediction->get_prediction_data()->id]);
        redirect($redirecturl);
    }
}

echo $OUTPUT->header();

if ($course) {
    // Print selected drop down.
    $pluginname = get_string('pluginname', 'report_insights');
    report_helper::print_report_selector($pluginname);
}

$renderable = new \report_insights\output\insights_list($model, $context, $othermodels, $page, $perpage);
echo $renderer->render($renderable);

$eventdata = array (
    'context' => $context,
    'other' => array('modelid' => $model->get_id())
);
\core\event\insights_viewed::create($eventdata)->trigger();

echo $OUTPUT->footer();
