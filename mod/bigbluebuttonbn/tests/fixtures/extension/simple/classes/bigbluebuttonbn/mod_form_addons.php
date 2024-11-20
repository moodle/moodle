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
namespace bbbext_simple\bigbluebuttonbn;

use stdClass;

/**
 * A class for the main mod form extension
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class mod_form_addons extends \mod_bigbluebuttonbn\local\extension\mod_form_addons {
    /**
     * Allows modules to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data passed by reference
     */
    public function data_postprocessing(\stdClass &$data): void {
        // Nothing for now.
    }

    /**
     * Allow module to modify  the data at the pre-processing stage.
     *
     * This method is also called in the bulk activity completion form.
     *
     * @param array|null $defaultvalues
     */
    public function data_preprocessing(?array &$defaultvalues): void {
        // This is where we can add the data from the flexurl table to the data provided.
        if (!empty($defaultvalues['id'])) {
            global $DB;
            $flexurlrecord = $DB->get_record('bbbext_simple', [
                'bigbluebuttonbnid' => $defaultvalues['id'],
            ]);
            if ($flexurlrecord) {
                $defaultvalues['newfield'] = $flexurlrecord->newfield;
            }
        }
    }

    /**
     * Can be overridden to add custom completion rules if the module wishes
     * them. If overriding this, you should also override completion_rule_enabled.
     * <p>
     * Just add elements to the form as needed and return the list of IDs. The
     * system will call disabledIf and handle other behaviour for each returned
     * ID.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules(): array {
        $this->mform->addElement('advcheckbox', 'completionextraisehandtwice',
            get_string('completionextraisehandtwice', 'bbbext_simple'),
            get_string('completionextraisehandtwice_desc', 'bbbext_simple'));

        $this->mform->addHelpButton('completionextraisehandtwice', 'completionextraisehandtwice',
            'bbbext_simple');
        $this->mform->disabledIf('completionextraisehandtwice', 'completion', 'neq', COMPLETION_AGGREGATION_ANY);
        return ['completionextraisehandtwice' . $this->suffix];
    }

    /**
     * Called during validation. Override to indicate, based on the data, whether
     * a custom completion rule is enabled (selected).
     *
     * @param array $data Input data (not yet validated)
     * @return bool True if one or more rules is enabled, false if none are;
     *   default returns false
     */
    public function completion_rule_enabled(array $data): bool {
        return !empty($data['completionextraisehandtwice' . $this->suffix]);
    }

    /**
     * Form adjustments after setting data
     *
     * @return void
     */
    public function definition_after_data() {
        // Nothing for now.
    }

    /**
     * Add new form field definition
     */
    public function add_fields(): void {
        $this->mform->addElement('header', 'simple', get_string('pluginname', 'bbbext_simple'));
        $this->mform->addElement('text', 'newfield', get_string('newfield', 'bbbext_simple'));
        $this->mform->setType('newfield', PARAM_TEXT);
    }

    /**
     * Validate form and returns an array of errors indexed by field name
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation(array $data, array $files): array {
        $errors = [];
        if (empty($data['newfield' . $this->suffix])) {
            $errors['newfield'] = get_string('newfielderror', 'bbbext_simple');
        }
        return $errors;
    }
}
