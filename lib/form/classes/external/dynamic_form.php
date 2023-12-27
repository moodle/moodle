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

namespace core_form\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Implements the external functions provided by the core_form subsystem.
 *
 * @copyright 2020 Marina Glancy
 * @package   core_form
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamic_form extends external_api {

    /**
     * Parameters for modal form
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'form' => new external_value(PARAM_RAW_TRIMMED, 'Form class', VALUE_REQUIRED),
            'formdata' => new external_value(PARAM_RAW, 'url-encoded form data', VALUE_REQUIRED),
        ]);
    }

    /**
     * Submit a form from a modal dialogue.
     *
     * @param string $formclass
     * @param string $formdatastr
     * @return array
     * @throws \moodle_exception
     */
    public static function execute(string $formclass, string $formdatastr): array {
        global $PAGE, $OUTPUT;

        $params = self::validate_parameters(self::execute_parameters(), [
            'form' => $formclass,
            'formdata' => $formdatastr,
        ]);
        $formclass = $params['form'];
        parse_str($params['formdata'], $formdata);

        self::autoload_block_edit_form($formclass);

        if (!class_exists($formclass) || !is_subclass_of($formclass, \core_form\dynamic_form::class)) {
            // For security reason we don't throw exception "class does not exist" but rather an access exception.
            throw new \moodle_exception('nopermissionform', 'core_form');
        }

        /** @var \core_form\dynamic_form $form */
        $form = new $formclass(null, null, 'post', '', [], true, $formdata, true);
        $form->set_data_for_dynamic_submission();
        if (!$form->is_cancelled() && $form->is_submitted() && $form->is_validated()) {
            // Form was properly submitted, process and return results of processing. No need to render it again.
            return ['submitted' => true, 'data' => json_encode($form->process_dynamic_submission())];
        }

        // Render actual form.

        if ($form->no_submit_button_pressed()) {
            // If form has not been submitted, we have to recreate the form for being able to properly handle non-submit action
            // like "repeat elements" to include additional JS.
            /** @var \core_form\dynamic_form $form */
            $form = new $formclass(null, null, 'post', '', [], true, $formdata, true);
            $form->set_data_for_dynamic_submission();
        }
        // Hack alert: Forcing bootstrap_renderer to initiate moodle page.
        $OUTPUT->header();

        $PAGE->start_collecting_javascript_requirements();
        $data = $form->render();
        $jsfooter = $PAGE->requires->get_end_code();
        $output = ['submitted' => false, 'html' => $data, 'javascript' => $jsfooter];
        return $output;
    }

    /**
     * Special autoloading for block forms.
     *
     * @param string $formclass
     * @return void
     */
    protected static function autoload_block_edit_form(string $formclass): void {
        global $CFG;
        if (preg_match('/^block_([\w_]+)_edit_form$/', $formclass, $matches)) {
            \block_manager::get_block_edit_form_class($matches[1]);
        }
        if ($formclass === 'block_edit_form') {
            require_once($CFG->dirroot . '/blocks/edit_form.php');
        }
    }

    /**
     * Return for modal
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            array(
                'submitted' => new external_value(PARAM_BOOL, 'If form was submitted and validated'),
                'data' => new external_value(PARAM_RAW, 'JSON-encoded return data from form processing method', VALUE_OPTIONAL),
                'html' => new external_value(PARAM_RAW, 'HTML fragment of the form', VALUE_OPTIONAL),
                'javascript' => new external_value(PARAM_RAW, 'JavaScript fragment of the form', VALUE_OPTIONAL)
            )
        );
    }
}
