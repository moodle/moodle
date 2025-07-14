<?php
// This file is part of Moodle - https://moodle.org/
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
 * Check and create missing default prediction models.
 *
 * @package     tool_analytics
 * @copyright   2019 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
\core_analytics\manager::check_can_manage_models();

if (!\core_analytics\manager::is_analytics_enabled()) {
    $PAGE->set_context(\context_system::instance());
    $renderer = $PAGE->get_renderer('tool_analytics');
    echo $renderer->render_analytics_disabled();
    exit(0);
}

$confirmed = optional_param('confirmed', false, PARAM_BOOL);
$restoreids = optional_param_array('restoreid', [], PARAM_ALPHANUM);

$returnurl = new \moodle_url('/admin/tool/analytics/index.php');
$myurl = new \moodle_url('/admin/tool/analytics/restoredefault.php');

\tool_analytics\output\helper::set_navbar(get_string('restoredefault', 'tool_analytics'), $myurl);

if (data_submitted()) {
    require_sesskey();

    if (empty($restoreids)) {
        $message = get_string('restoredefaultempty', 'tool_analytics');
        $type = \core\output\notification::NOTIFY_WARNING;
        redirect($myurl, $message, null, $type);
    }

    $numcreated = 0;

    foreach (\core_analytics\manager::load_default_models_for_all_components() as $componentname => $modelslist) {
        foreach ($modelslist as $definition) {
            if (!in_array(\core_analytics\manager::model_declaration_identifier($definition), $restoreids)) {
                // This model has not been selected by the user.
                continue;
            }

            list($target, $indicators) = \core_analytics\manager::get_declared_target_and_indicators_instances($definition);

            if (\core_analytics\model::exists($target, $indicators)) {
                // This model exists (normally this should not happen as we do not show such models in the UI to select).
                continue;
            }

            \core_analytics\manager::create_declared_model($definition);
            $numcreated++;
        }
    }

    $message = get_string('restoredefaultsome', 'tool_analytics', ['count' => $numcreated]);
    $type = \core\output\notification::NOTIFY_SUCCESS;

    redirect($returnurl, $message, null, $type);
}

$models = \core_analytics\manager::load_default_models_for_all_components();
$ui = new \tool_analytics\output\restorable_models($models);

echo $OUTPUT->header();
echo $PAGE->get_renderer('tool_analytics')->render($ui);
echo $OUTPUT->footer();
