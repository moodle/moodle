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

namespace core\api\form;

use core\api\token_manager;
use core\output\html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form to create a personal access token, whose fields are fixed once it exists.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_token extends \moodleform {
    /**
     * Prefix for the per-scope checkbox elements.
     *
     * @var string
     */
    protected const string SCOPE_ELEMENT_PREFIX = 'scope_';

    /**
     * Name of the element that labels the scope list and carries its error.
     *
     * @var string
     */
    protected const string SCOPE_LABEL = 'scopeslabel';

    /**
     * Map the available scopes to form element names, keyed by element name.
     *
     * @return string[] Scope identifiers keyed by element name.
     */
    protected function get_scope_elements(): array {
        /** @var token_manager $manager */
        $manager = $this->_customdata['manager'];
        $elements = [];

        // Colons are not usable in element names, so each becomes an underscore. Deriving the
        // name from the identifier rather than the position keeps it stable as scopes change.
        foreach (array_keys($manager->get_available_scopes()) as $identifier) {
            $elements[self::SCOPE_ELEMENT_PREFIX . str_replace(':', '_', $identifier)] = $identifier;
        }

        return $elements;
    }

    /**
     * Define the form.
     */
    protected function definition(): void {
        global $OUTPUT;

        $mform = $this->_form;
        /** @var token_manager $manager */
        $manager = $this->_customdata['manager'];

        $mform->addElement('text', 'name', get_string('pat_name'), ['maxlength' => 255]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('textarea', 'description', get_string('pat_description'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);

        // The offered periods are the manager's to state; the form only lists them.
        $mform->addElement('select', 'expirypreset', get_string('pat_expiry'), $manager->get_expiry_choices());
        $mform->setDefault('expirypreset', token_manager::DEFAULT_EXPIRY_PRESET);

        $scopes = $manager->get_available_scopes();

        // The hint carries the label, so "Scopes" sits in the label column like every other
        // field on this form and level with something to read. Not on the first checkbox: an
        // advcheckbox carrying a label renders its text in a described-by span rather than as
        // the label itself, which leaves a trailing line box and makes that row taller.
        // Marked required by hand: the requirement is that any one of the boxes is ticked, which
        // is not something a rule on a single element expresses.
        $mform->addElement(
            'static',
            self::SCOPE_LABEL,
            get_string('pat_scopes') . ' ' . $OUTPUT->pix_icon('req', get_string('requiredelement', 'form')),
            // What the scopes are for, rather than that one is required: the marker beside the
            // label says that already, and so does the error when none is ticked.
            $OUTPUT->notification(get_string('pat_scopesinfo'), 'info', false),
        );

        // A checkbox per scope, each its own form row: a form group would lay them out inline,
        // and a description too wide for the rest of the line drops beneath its own checkbox.
        foreach ($this->get_scope_elements() as $elementname => $identifier) {
            $scope = $scopes[$identifier];
            $mform->addElement(
                'advcheckbox',
                $elementname,
                '',
                html_writer::div($scope::get_summary(), 'fw-bold') .
                    html_writer::div($scope::get_description(), 'text-muted small'),
            );
            $mform->setType($elementname, PARAM_BOOL);
        }

        $this->add_action_buttons(true, get_string('pat_create'));
    }

    /**
     * Validate the submitted data.
     *
     * @param array $data The submitted data.
     * @param array $files The submitted files.
     * @return array Errors keyed by element name.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (empty($this->get_submitted_scopes($data))) {
            // Reported against the label, which is the row that names the list.
            $errors[self::SCOPE_LABEL] = get_string('apitokennoscopes', 'error');
        }

        return $errors;
    }

    /**
     * Resolve the chosen period to an expiry timestamp.
     *
     * @param \stdClass $data The submitted data.
     * @return int The expiry timestamp.
     */
    public function get_expiry_time(\stdClass $data): int {
        /** @var token_manager $manager */
        $manager = $this->_customdata['manager'];

        // Resolved now rather than when the form was rendered, so a form left open overnight still
        // yields the number of days the user actually chose.
        return $manager->get_expiry_presets()[(int) $data->expirypreset];
    }

    /**
     * Resolve the checked scope elements back to scope identifiers.
     *
     * @param array $data The submitted data.
     * @return string[] The identifiers of the checked scopes.
     */
    public function get_submitted_scopes(array $data): array {
        $selected = [];

        foreach ($this->get_scope_elements() as $elementname => $identifier) {
            if (!empty($data[$elementname])) {
                $selected[] = $identifier;
            }
        }

        return $selected;
    }
}
