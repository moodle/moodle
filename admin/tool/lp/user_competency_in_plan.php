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
 * User competency page. Lists everything known about a user competency.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$userid = required_param('userid', PARAM_INT);
$competencyid = required_param('competencyid', PARAM_INT);
$planid = required_param('planid', PARAM_INT);

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}
\core_competency\api::require_enabled();

$params = array('userid' => $userid, 'competencyid' => $competencyid);
$params['planid'] = $planid;
$plan = \core_competency\api::read_plan($planid);
$url = new moodle_url('/admin/tool/lp/user_competency_in_plan.php', $params);
$competency = new \core_competency\competency($competencyid);
$framework = $competency->get_framework();

list($title, $subtitle) = \tool_lp\page_helper::setup_for_plan($userid, $url, $plan);

$output = $PAGE->get_renderer('tool_lp');
echo $output->header();
echo $output->heading($title);
// User competency plan navigation.
$baseurl = new moodle_url('/admin/tool/lp/user_competency_in_plan.php');
$nav = new \tool_lp\output\competency_plan_navigation($userid, $competencyid, $planid, $baseurl);

echo $output->render($nav);
$page = new \tool_lp\output\user_competency_summary_in_plan($competencyid, $planid);
echo $output->render($page);
// Trigger the viewed event.
$pc = \core_competency\api::get_plan_competency($plan, $competency->get('id'));
if ($plan->get('status') == \core_competency\plan::STATUS_COMPLETE) {
    $usercompetencyplan = $pc->usercompetencyplan;
    \core_competency\api::user_competency_plan_viewed($usercompetencyplan);
} else {
    $usercompetency = $pc->usercompetency;
    \core_competency\api::user_competency_viewed_in_plan($usercompetency, $plan->get('id'));
}

echo $output->footer();
