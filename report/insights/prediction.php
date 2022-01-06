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
 * View an insight.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$predictionid = required_param('id', PARAM_INT);

if (!\core_analytics\manager::is_analytics_enabled()) {
    $PAGE->set_context(\context_system::instance());
    $renderer = $PAGE->get_renderer('report_insights');
    echo $renderer->render_analytics_disabled();
    exit(0);
}

list($model, $prediction, $context) = \core_analytics\manager::get_prediction($predictionid, true);
if ($context->contextlevel < CONTEXT_COURSE) {
    // Only for higher levels than course.
    $PAGE->set_context($context);
}

$params = array('id' => $prediction->get_prediction_data()->id);
$url = new \moodle_url('/report/insights/prediction.php', $params);
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$navurl = new \moodle_url('/report/insights/insights.php', array('contextid' => $context->id));
if ($context->contextlevel === CONTEXT_SYSTEM) {
    admin_externalpage_setup('reportinsights', '', null, '', array('pagelayout' => 'report'));
} else if ($context->contextlevel === CONTEXT_USER) {
    $user = \core_user::get_user($context->instanceid, '*', MUST_EXIST);
    $PAGE->navigation->extend_for_user($user);

    $modelinsightsurl = clone $navurl;
    $modelinsightsurl->param('modelid', $model->get_id());
    $PAGE->add_report_nodes($user->id, array(
        'name' => get_string('insights', 'report_insights'),
        'url' => $url
    ));
}
$PAGE->navigation->override_active_url($navurl);

$renderer = $PAGE->get_renderer('report_insights');

$insightinfo = new stdClass();
$insightinfo->contextname = $context->get_context_name();
$insightinfo->insightname = $model->get_target()->get_name();

$modelready = $model->is_enabled() && $model->is_trained() && $model->predictions_exist($context);
if (!$modelready) {
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

echo $OUTPUT->header();

$renderable = new \report_insights\output\insight($prediction, $model, false, $context);
echo $renderer->render($renderable);

echo $OUTPUT->footer();
