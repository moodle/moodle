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
 * Model edit form.
 *
 * @package   tool_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Model edit form.
 *
 * @package   tool_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_model extends \moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        if ($this->_customdata['trainedmodel'] && $this->_customdata['staticmodel'] === false) {
            $message = get_string('edittrainedwarning', 'tool_analytics');
            $mform->addElement('html', $OUTPUT->notification($message, \core\output\notification::NOTIFY_WARNING));
        }

        $mform->addElement('advcheckbox', 'enabled', get_string('enabled', 'tool_analytics'));

        // Target.
        if (!empty($this->_customdata['targets'])) {
            $targets = array('' => '');
            foreach ($this->_customdata['targets'] as $classname => $target) {
                $optionname = \tool_analytics\output\helper::class_to_option($classname);
                $targets[$optionname] = $target->get_name();
            }

            $mform->addElement('select', 'target', get_string('target', 'tool_analytics'), $targets);
            $mform->addHelpButton('target', 'target', 'tool_analytics');
            $mform->addRule('target', get_string('required'), 'required', null, 'client');
        }

        if (!empty($this->_customdata['targetname']) && !empty($this->_customdata['targetclass'])) {
            $mform->addElement('static', 'targetname', get_string('target', 'tool_analytics'), $this->_customdata['targetname']);
            $mform->addElement('hidden', 'target',
                \tool_analytics\output\helper::class_to_option($this->_customdata['targetclass']));
            // We won't update the model's target so no worries about its format (we can't use PARAM_ALPHANUMEXT
            // because of class_to_option).
            $mform->setType('target', PARAM_TEXT);
        }

        // Indicators.
        if (!$this->_customdata['staticmodel']) {
            $indicators = array();
            foreach ($this->_customdata['indicators'] as $classname => $indicator) {
                $optionname = \tool_analytics\output\helper::class_to_option($classname);
                $indicators[$optionname] = $indicator->get_name();
            }
            $options = array(
                'multiple' => true
            );
            $mform->addElement('autocomplete', 'indicators', get_string('indicators', 'tool_analytics'), $indicators, $options);
            $mform->setType('indicators', PARAM_ALPHANUMEXT);
            $mform->addHelpButton('indicators', 'indicators', 'tool_analytics');
        }

        // Time-splitting methods.
        if (!empty($this->_customdata['invalidcurrenttimesplitting'])) {
            $mform->addElement('html', $OUTPUT->notification(
                get_string('invalidcurrenttimesplitting', 'tool_analytics'),
                \core\output\notification::NOTIFY_WARNING)
            );
        }

        $timesplittings = array('' => '');
        foreach ($this->_customdata['timesplittings'] as $classname => $timesplitting) {
            $optionname = \tool_analytics\output\helper::class_to_option($classname);
            $timesplittings[$optionname] = $timesplitting->get_name();
        }
        $mform->addElement('select', 'timesplitting', get_string('timesplittingmethod', 'analytics'), $timesplittings);
        $mform->addHelpButton('timesplitting', 'timesplittingmethod', 'analytics');

        // Predictions processor.
        if (!$this->_customdata['staticmodel']) {
            $defaultprocessor = \core_analytics\manager::get_predictions_processor_name(
                \core_analytics\manager::get_predictions_processor()
            );
            $predictionprocessors = ['' => get_string('defaultpredictoroption', 'analytics', $defaultprocessor)];
            foreach ($this->_customdata['predictionprocessors'] as $classname => $predictionsprocessor) {
                if ($predictionsprocessor->is_ready() !== true) {
                    continue;
                }
                $optionname = \tool_analytics\output\helper::class_to_option($classname);
                $predictionprocessors[$optionname] = \core_analytics\manager::get_predictions_processor_name($predictionsprocessor);
            }

            $mform->addElement('select', 'predictionsprocessor', get_string('predictionsprocessor', 'analytics'),
                $predictionprocessors);
            $mform->addHelpButton('predictionsprocessor', 'predictionsprocessor', 'analytics');
        }

        if (!empty($this->_customdata['id'])) {
            $mform->addElement('hidden', 'id', $this->_customdata['id']);
            $mform->setType('id', PARAM_INT);

            $mform->addElement('hidden', 'action', 'edit');
            $mform->setType('action', PARAM_ALPHANUMEXT);
        }

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['timesplitting'])) {
            $timesplittingclass = \tool_analytics\output\helper::option_to_class($data['timesplitting']);
            if (\core_analytics\manager::is_valid($timesplittingclass, '\core_analytics\local\time_splitting\base') === false) {
                $errors['timesplitting'] = get_string('errorinvalidtimesplitting', 'analytics');
            }

            $targetclass = \tool_analytics\output\helper::option_to_class($data['target']);
            $timesplitting = \core_analytics\manager::get_time_splitting($timesplittingclass);
            $target = \core_analytics\manager::get_target($targetclass);
            if (!$target->can_use_timesplitting($timesplitting)) {
                $errors['timesplitting'] = get_string('invalidtimesplitting', 'tool_analytics');
            }
        }

        if (!$this->_customdata['staticmodel']) {
            if (empty($data['indicators'])) {
                $errors['indicators'] = get_string('errornoindicators', 'analytics');
            } else {
                foreach ($data['indicators'] as $indicator) {
                    $realindicatorname = \tool_analytics\output\helper::option_to_class($indicator);
                    if (\core_analytics\manager::is_valid($realindicatorname, '\core_analytics\local\indicator\base') === false) {
                        $errors['indicators'] = get_string('errorinvalidindicator', 'analytics', $realindicatorname);
                    }
                }
            }
        }

        if (!empty($data['enabled']) && empty($data['timesplitting'])) {
            $errors['enabled'] = get_string('errorcantenablenotimesplitting', 'tool_analytics');
        }

        return $errors;
    }
}
