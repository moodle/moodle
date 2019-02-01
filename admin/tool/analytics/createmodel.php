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
 * Create model form.
 *
 * @package    tool_analytics
 * @copyright  2019 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
\core_analytics\manager::check_can_manage_models();

$returnurl = new \moodle_url('/admin/tool/analytics/index.php');
$url = new \moodle_url('/admin/tool/analytics/createmodel.php');
$title = get_string('createmodel', 'tool_analytics');

\tool_analytics\output\helper::set_navbar($title, $url);

// Static targets are not editable, we discard them.
$targets = array_filter(\core_analytics\manager::get_all_targets(), function($target) {
    return (!$target->based_on_assumptions());
});

$customdata = array(
    'trainedmodel' => false,
    'targets' => $targets,
    'indicators' => \core_analytics\manager::get_all_indicators(),
    'timesplittings' => \core_analytics\manager::get_enabled_time_splitting_methods(),
    'predictionprocessors' => \core_analytics\manager::get_all_prediction_processors(),
);
$mform = new \tool_analytics\output\form\edit_model(null, $customdata);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {

    // Converting option names to class names.
    $targetclass = \tool_analytics\output\helper::option_to_class($data->target);
    if (empty($targets[$targetclass])) {
        throw new \moodle_exception('errorinvalidtarget', 'analytics', '', $targetclass);
    }
    $target = $targets[$targetclass];

    $indicators = array();
    foreach ($data->indicators as $indicator) {
        $indicatorinstance = \core_analytics\manager::get_indicator(
            \tool_analytics\output\helper::option_to_class($indicator)
        );
        $indicators[$indicatorinstance->get_id()] = $indicatorinstance;
    }
    $timesplitting = \tool_analytics\output\helper::option_to_class($data->timesplitting);
    $predictionsprocessor = \tool_analytics\output\helper::option_to_class($data->predictionsprocessor);

    // Insert the model into db.
    $model = \core_analytics\model::create($target, []);

    // Filter out indicators that can not be used by this target.
    $invalidindicators = array_diff_key($indicators, $model->get_potential_indicators());
    if ($invalidindicators) {
        $indicators = array_diff_key($indicators, $invalidindicators);
    }

    // Update the model with the valid list of indicators.
    $model->update($data->enabled, $indicators, $timesplitting, $predictionsprocessor);

    $message = '';
    $messagetype = \core\output\notification::NOTIFY_SUCCESS;
    if (!empty($invalidindicators)) {
        $message = get_string('invalidindicatorsremoved', 'tool_analytics');
    }
    redirect($returnurl, $message, 0, $messagetype);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
