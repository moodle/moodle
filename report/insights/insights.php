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
    admin_externalpage_setup('reportinsights', '', null, '', array('pagelayout' => 'report'));
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

$insightinfo = new stdClass();
$insightinfo->contextname = $context->get_context_name();
$insightinfo->insightname = $model->get_target()->get_name();

if (!$model->is_enabled()) {
    echo $renderer->render_model_disabled($insightinfo);
    exit(0);
}

if (!$model->uses_insights()) {
    echo $renderer->render_no_insights_model($context);
    exit(0);
}

$PAGE->set_title($insightinfo->insightname);
$PAGE->set_heading($insightinfo->contextname);

echo $OUTPUT->header();

$renderable = new \report_insights\output\insights_list($model, $context, $othermodels, $page, $perpage);
echo $renderer->render($renderable);

$eventdata = array (
    'context' => $context,
    'other' => array('modelid' => $model->get_id())
);
\core\event\insights_viewed::create($eventdata)->trigger();

echo $OUTPUT->footer();
