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
 * Model-related actions.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$id = required_param('id', PARAM_INT);
$action = required_param('action', PARAM_ALPHANUMEXT);

$context = context_system::instance();

require_login();

$model = new \core_analytics\model($id);
\core_analytics\manager::check_can_manage_models();

$params = array('id' => $id, 'action' => $action);
$url = new \moodle_url('/admin/tool/analytics/model.php', $params);

switch ($action) {

    case 'edit':
        $title = get_string('editmodel', 'tool_analytics', $model->get_target()->get_name());
        break;
    case 'evaluate':
        $title = get_string('evaluatemodel', 'tool_analytics');
        break;
    case 'getpredictions':
        $title = get_string('getpredictions', 'tool_analytics');
        break;
    case 'log':
        $title = get_string('viewlog', 'tool_analytics');
        break;
    case 'enable':
        $title = get_string('enable');
        break;
    case 'disable':
        $title = get_string('disable');
        break;
    case 'export':
        $title = get_string('export', 'tool_analytics');
        break;

    default:
        throw new moodle_exception('errorunknownaction', 'analytics');
}

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
$PAGE->set_heading($title);

switch ($action) {

    case 'enable':
        $model->enable();
        redirect(new \moodle_url('/admin/tool/analytics/index.php'));

    case 'disable':
        $model->update(0, false, false);
        redirect(new \moodle_url('/admin/tool/analytics/index.php'));

    case 'edit':

        if ($model->is_static()) {
            echo $OUTPUT->header();
            throw new moodle_exception('errornostaticedit', 'tool_analytics');
        }

        $customdata = array(
            'id' => $model->get_id(),
            'model' => $model,
            'indicators' => $model->get_potential_indicators(),
            'timesplittings' => \core_analytics\manager::get_enabled_time_splitting_methods()
        );
        $mform = new \tool_analytics\output\form\edit_model(null, $customdata);

        if ($mform->is_cancelled()) {
            redirect(new \moodle_url('/admin/tool/analytics/index.php'));

        } else if ($data = $mform->get_data()) {
            confirm_sesskey();

            // Converting option names to class names.
            $indicators = array();
            foreach ($data->indicators as $indicator) {
                $indicatorclass = \tool_analytics\output\helper::option_to_class($indicator);
                $indicators[] = \core_analytics\manager::get_indicator($indicatorclass);
            }
            $timesplitting = \tool_analytics\output\helper::option_to_class($data->timesplitting);
            $model->update($data->enabled, $indicators, $timesplitting);
            redirect(new \moodle_url('/admin/tool/analytics/index.php'));
        }

        echo $OUTPUT->header();

        $modelobj = $model->get_model_obj();

        $callable = array('\tool_analytics\output\helper', 'class_to_option');
        $modelobj->indicators = array_map($callable, json_decode($modelobj->indicators));
        $modelobj->timesplitting = \tool_analytics\output\helper::class_to_option($modelobj->timesplitting);
        $mform->set_data($modelobj);
        $mform->display();
        break;

    case 'evaluate':
        echo $OUTPUT->header();

        if ($model->is_static()) {
            throw new moodle_exception('errornostaticevaluate', 'tool_analytics');
        }

        // Web interface is used by people who can not use CLI nor code stuff, always use
        // cached stuff as they will change the model through the web interface as well
        // which invalidates the previously analysed stuff.
        $results = $model->evaluate(array('reuseprevanalysed' => true));
        $renderer = $PAGE->get_renderer('tool_analytics');
        echo $renderer->render_evaluate_results($results, $model->get_analyser()->get_logs());
        break;

    case 'getpredictions':
        echo $OUTPUT->header();

        $trainresults = $model->train();
        $trainlogs = $model->get_analyser()->get_logs();

        // Looks dumb to get a new instance but better be conservative.
        $model = new \core_analytics\model($model->get_model_obj());
        $predictresults = $model->predict();
        $predictlogs = $model->get_analyser()->get_logs();

        $renderer = $PAGE->get_renderer('tool_analytics');
        echo $renderer->render_get_predictions_results($trainresults, $trainlogs, $predictresults, $predictlogs);
        break;

    case 'log':
        echo $OUTPUT->header();

        if ($model->is_static()) {
            throw new moodle_exception('errornostaticlog', 'tool_analytics');
        }

        $renderer = $PAGE->get_renderer('tool_analytics');
        $modellogstable = new \tool_analytics\output\model_logs('model-' . $model->get_id(), $model);
        echo $renderer->render_table($modellogstable);
        break;

    case 'export':

        if ($model->is_static() || !$model->is_trained()) {
            throw new moodle_exception('errornoexport', 'tool_analytics');
        }

        $file = $model->get_training_data();
        if (!$file) {
            redirect(new \moodle_url('/admin/tool/analytics/index.php'), get_string('errortrainingdataexport', 'tool_analytics'),
                null, \core\output\notification::NOTIFY_ERROR);
        }

        $filename = 'training-data.' . $model->get_id() . '.' . time() . '.csv';
        send_file($file, $filename, null, 0, false, true);
        break;
}

echo $OUTPUT->footer();
