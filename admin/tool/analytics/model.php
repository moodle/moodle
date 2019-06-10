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
require_once($CFG->libdir . '/filelib.php');

$id = required_param('id', PARAM_INT);
$action = required_param('action', PARAM_ALPHANUMEXT);

require_login();

$model = new \core_analytics\model($id);
\core_analytics\manager::check_can_manage_models();

$returnurl = new \moodle_url('/admin/tool/analytics/index.php');
$params = array('id' => $id, 'action' => $action);
$url = new \moodle_url('/admin/tool/analytics/model.php', $params);

switch ($action) {

    case 'edit':
        $title = get_string('editmodel', 'tool_analytics', $model->get_name());
        break;
    case 'evaluate':
        $title = get_string('evaluatemodel', 'tool_analytics');
        break;
    case 'scheduledanalysis':
        $title = get_string('analysis', 'tool_analytics');
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
    case 'delete':
        $title = get_string('delete');
        break;
    case 'exportdata':
        $title = get_string('exporttrainingdata', 'tool_analytics');
        break;
    case 'exportmodel':
        $title = get_string('exportmodel', 'tool_analytics');
        break;
    case 'clear':
        $title = get_string('clearpredictions', 'tool_analytics');
        break;
    case 'invalidanalysables':
        $title = get_string('invalidanalysables', 'tool_analytics');
        break;
    default:
        throw new moodle_exception('errorunknownaction', 'analytics');
}

\tool_analytics\output\helper::set_navbar($title, $url);

$onlycli = get_config('analytics', 'onlycli');
if ($onlycli === false) {
    // Default applied if no config found.
    $onlycli = 1;
}

switch ($action) {

    case 'enable':
        confirm_sesskey();

        $model->enable();
        redirect($returnurl);
        break;

    case 'disable':
        confirm_sesskey();

        $model->update(0, false, false);
        redirect($returnurl);
        break;

    case 'delete':
        confirm_sesskey();

        $model->delete();
        redirect($returnurl);
        break;

    case 'edit':
        confirm_sesskey();

        $customdata = array(
            'id' => $model->get_id(),
            'trainedmodel' => $model->is_trained(),
            'staticmodel' => $model->is_static(),
            'indicators' => $model->get_potential_indicators(),
            'timesplittings' => \core_analytics\manager::get_all_time_splittings(),
            'predictionprocessors' => \core_analytics\manager::get_all_prediction_processors()
        );
        $mform = new \tool_analytics\output\form\edit_model(null, $customdata);

        if ($mform->is_cancelled()) {
            redirect($returnurl);

        } else if ($data = $mform->get_data()) {

            $timesplitting = \tool_analytics\output\helper::option_to_class($data->timesplitting);

            if (!$model->is_static()) {
                // Converting option names to class names.
                $indicators = array();
                foreach ($data->indicators as $indicator) {
                    $indicatorclass = \tool_analytics\output\helper::option_to_class($indicator);
                    $indicators[] = \core_analytics\manager::get_indicator($indicatorclass);
                }
                $predictionsprocessor = \tool_analytics\output\helper::option_to_class($data->predictionsprocessor);
            } else {
                // These fields can not be modified.
                $indicators = false;
                $predictionsprocessor = false;
            }

            $model->update($data->enabled, $indicators, $timesplitting, $predictionsprocessor);
            redirect($returnurl);
        }

        echo $OUTPUT->header();

        $modelobj = $model->get_model_obj();

        $callable = array('\tool_analytics\output\helper', 'class_to_option');
        $modelobj->indicators = array_map($callable, json_decode($modelobj->indicators));
        $modelobj->timesplitting = \tool_analytics\output\helper::class_to_option($modelobj->timesplitting);
        $modelobj->predictionsprocessor = \tool_analytics\output\helper::class_to_option($modelobj->predictionsprocessor);
        $mform->set_data($modelobj);
        $mform->display();
        break;

    case 'evaluate':
        confirm_sesskey();

        if ($model->is_static()) {
            throw new moodle_exception('errornostaticevaluate', 'tool_analytics');
        }

        if ($onlycli) {
            throw new moodle_exception('erroronlycli', 'tool_analytics');
        }

        // Web interface is used by people who can not use CLI nor code stuff, always use
        // cached stuff as they will change the model through the web interface as well
        // which invalidates the previously analysed stuff.
        $options = ['reuseprevanalysed' => true];

        $mode = optional_param('mode', false, PARAM_ALPHANUM);
        if ($mode == 'trainedmodel') {
            $options['mode'] = 'trainedmodel';
        } else {

            // All is the default in core_analytics\model::evaluate() as well.
            $timesplitting = optional_param('timesplitting', 'all', PARAM_ALPHANUMEXT);
            if ($timesplitting === 'current') {
                $options['timesplitting'] = \core_analytics\manager::get_time_splitting($model->get_model_obj()->timesplitting);
            } else if ($timesplitting !== 'all') {
                $options['timesplitting'] = \core_analytics\manager::get_time_splitting(
                    \tool_analytics\output\helper::option_to_class($timesplitting)
                );
            }
        }

        $results = $model->evaluate($options);

        // We reset the theme and the output as some indicators may be using external functions
        // which reset $PAGE.
        \tool_analytics\output\helper::reset_page();
        echo $OUTPUT->header();

        $renderer = $PAGE->get_renderer('tool_analytics');
        echo $renderer->render_evaluate_results($results, $model->get_analyser()->get_logs());
        break;

    case 'scheduledanalysis':
        confirm_sesskey();

        if ($onlycli) {
            throw new moodle_exception('erroronlycli', 'tool_analytics');
        }

        $trainresults = $model->train();
        $trainlogs = $model->get_analyser()->get_logs();

        // Looks dumb to get a new instance but better be conservative.
        $model = new \core_analytics\model($model->get_model_obj());
        if ($model->is_trained()) {
            $predictresults = $model->predict();
            $predictlogs = $model->get_analyser()->get_logs();
        } else {
            $predictresults = false;
            $predictlogs = array();
        }

        // We reset the theme and the output as some indicators may be using external functions
        // which reset $PAGE.
        \tool_analytics\output\helper::reset_page();
        echo $OUTPUT->header();

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

    case 'exportdata':

        if ($model->is_static() || !$model->is_trained()) {
            throw new moodle_exception('errornoexport', 'tool_analytics');
        }

        $file = $model->get_training_data();
        if (!$file) {
            redirect($returnurl, get_string('errortrainingdataexport', 'tool_analytics'),
                null, \core\output\notification::NOTIFY_ERROR);
        }

        $filename = 'training-data.' . $model->get_id() . '.' . time() . '.csv';
        send_file($file, $filename, null, 0, false, true);
        break;

    case 'exportmodel':

        $includeweights = optional_param('includeweights', 1, PARAM_INT);

        $zipfilename = 'model-' . $model->get_unique_id() . '-' . microtime(false) . '.zip';
        $zipfilepath = $model->export_model($zipfilename, $includeweights);
        send_temp_file($zipfilepath, $zipfilename);
        break;

    case 'clear':
        confirm_sesskey();

        $model->clear();
        redirect($returnurl);
        break;

    case 'invalidanalysables':

        echo $OUTPUT->header();

        $page = optional_param('page', 0, PARAM_INT);
        // No option in the UI to change this, only for url hackers ;).
        $perpage = optional_param('perpage', 10, PARAM_INT);

        $renderable = new \tool_analytics\output\invalid_analysables($model, $page, $perpage);
        $renderer = $PAGE->get_renderer('tool_analytics');
        echo $renderer->render($renderable);

        break;
}

echo $OUTPUT->footer();
