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
 * Plan page.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');

$id = required_param('id', PARAM_INT);

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}
\core_competency\api::require_enabled();

$plan = \core_competency\api::read_plan($id);
$url = new moodle_url('/admin/tool/lp/plan.php', array('id' => $id));

list($title, $subtitle) = \tool_lp\page_helper::setup_for_plan($plan->get('userid'), $url, $plan);

$output = $PAGE->get_renderer('tool_lp');
echo $output->header();

$page = new \tool_lp\output\plan_page($plan);
echo $output->render($page);

// Trigger viewed event.
\core_competency\api::plan_viewed($plan);

echo $output->footer();
