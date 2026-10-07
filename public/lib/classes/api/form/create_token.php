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
     * HTML id of the fieldset wrapping the scope checkboxes.
     *
     * @var string
     */
    protected const string SCOPES_FIELDSET_ID = 'id_scopesfieldset';

    /**
     * Classes applied to the scopes fieldset when it carries a validation error.
     *
     * Bootstrap's own error styling (the Name field's red box) is a `.is-invalid` class, but that
     * class only paints a border on `.form-control`/`.form-select` elements, not on a plain
     * fieldset. These border utility classes are already compiled into Boost's CSS, so they give
     * the fieldset the same kind of persistent, deliberate error box without a new stylesheet
     * rule, and independent of any browser's own (and differently coloured) focus ring.
     *
     * @var string
     */
    protected const string SCOPES_FIELDSET_ERROR_CLASS = 'border border-danger rounded p-2 mb-2';

    /**
     * The "Scopes" label text, including its required-field marker.
     *
     * Shared by the static label row and the fieldset's legend, so both read the same thing.
     *
     * @var string
     */
    protected string $scopeslabel = '';

    /**
     * The element holding the scopes fieldset's opening tag and legend.
     *
     * Its text is rewritten in {@see display()} once the scopes error, if any, is known.
     *
     * @var \HTML_QuickForm_html|null
     */
    protected ?\HTML_QuickForm_html $scopesfieldsetopen = null;

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

        $this->scopeslabel = get_string('pat_scopes') . ' ' . $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));

        // A plain notification() call renders as an assertive alert that a core script re-fires
        // on every page load regardless of whether the scopes error is present, which would
        // compete with, and duplicate, the fieldset's own focus announcement on a failed
        // submit. set_announce(false) keeps the info box exactly as it looks, without that
        // competing announcement.
        $infonotification = new \core\output\notification(
            get_string('pat_scopesinfo'),
            \core\output\notification::NOTIFY_INFO,
            false,
        );
        $infonotification->set_announce(false);

        // The hint carries the label, so "Scopes" sits in the label column like every other
        // field on this form and level with something to read. Not on the first checkbox: an
        // advcheckbox carrying a label renders its text in a described-by span rather than as
        // the label itself, which leaves a trailing line box and makes that row taller.
        // Marked required by hand: the requirement is that any one of the boxes is ticked, which
        // is not something a rule on a single element expresses.
        $mform->addElement(
            'static',
            self::SCOPE_LABEL,
            $this->scopeslabel,
            // What the scopes are for, rather than that one is required: the marker beside the
            // label says that already, and so does the error when none is ticked.
            $OUTPUT->render($infonotification),
        );

        // A real fieldset around the checkboxes, so their shared "select at least one" error
        // describes the group as a unit rather than any single checkbox. Built from raw 'html'
        // elements rather than mform's own grouping: a group renders its members with the
        // "-inline" template, which would collapse the one-checkbox-per-row layout below onto a
        // single line. Whether it is invalid is not known until the form has been validated, so
        // the opening tag starts out plain and is rewritten in display() once that is known.
        $this->scopesfieldsetopen = $mform->addElement('html', $this->get_scopes_fieldset_open(false));

        // A checkbox per scope, each its own form row: a form group would lay them out inline,
        // and a description too wide for the rest of the line drops beneath its own checkbox.
        foreach ($this->get_scope_elements() as $elementname => $identifier) {
            $scope = $scopes[$identifier];
            $mform->addElement(
                'advcheckbox',
                $elementname,
                '',
                html_writer::div(
                    $scope::get_summary() . ' - ' .
                        html_writer::tag('code', $identifier, ['class' => 'fw-normal text-muted']),
                    'fw-bold',
                ) .
                    html_writer::div($scope::get_description(), 'text-muted small'),
            );
            $mform->setType($elementname, PARAM_BOOL);
        }

        $mform->addElement('html', html_writer::end_tag('fieldset'));

        $this->add_action_buttons(true, get_string('pat_create'));
    }

    /**
     * Build the scopes fieldset's opening tag and its legend.
     *
     * @param bool $invalid Whether the scopes error is present, so the fieldset should be
     *                      exposed as invalid once the page renders.
     * @param bool $hasautofocus Whether autofocus should be set on the scopes fieldset.
     * @return string
     */
    protected function get_scopes_fieldset_open(bool $invalid, bool $hasautofocus = false): string {
        $attributes = ['id' => self::SCOPES_FIELDSET_ID];
        $legend = $this->scopeslabel;

        if ($invalid) {
            // Mirrors what mform already does for a single invalid control such as Name:
            // aria-invalid and the error-state class gives it the same kind of persistent
            // visual box the Name field gets from is-invalid. tabindex makes the fieldset
            // focusable at all; autofocus is left in as a harmless progressive-enhancement
            // fallback, but {@see display()} is what actually moves focus here, since
            // autofocus's content attribute is only specified to reliably do that on
            // focusable form-associated elements (button, input, select, textarea), not a
            // plain fieldset.
            $attributes += [
                'tabindex' => '-1',
                'autofocus' => 'autofocus',
                'aria-invalid' => 'true',
                'class' => self::SCOPES_FIELDSET_ERROR_CLASS,
            ];

            if ($hasautofocus) {
                $attributes['autofocus'] = 'autofocus';
            }

            // The error joins the legend, which is the fieldset's accessible name.
            // This ensures that the validation error will be reliably announced on focus by every screen reader.
            $legend .= ' ' . get_string('apitokennoscopes', 'error');
        }

        return html_writer::start_tag('fieldset', $attributes) .
            html_writer::tag('legend', $legend, ['class' => 'visually-hidden']);
    }

    /**
     * Print html form.
     *
     * Once the form has been through validation, the scopes error (if any) is known, so the
     * fieldset opening tag added in {@see definition()} is rewritten here to carry it.
     */
    #[\Override]
    public function display(): void {
        if ($this->_form->getElementError(self::SCOPE_LABEL)) {
            global $PAGE;

            // Find the first element with an error by iterating in order they were added.
            $firsterrorelement = null;
            foreach ($this->_form->_elements as $element) {
                $elementname = $element->getName();
                // Skip elements without names.
                if ($elementname === null || $elementname === '') {
                    continue;
                }
                // Check if this element has an error.
                if (isset($this->_form->_errors[$elementname])) {
                    $firsterrorelement = $elementname;
                    break;
                }
            }

            // Only add autofocus/focus if scopes is the first error element.
            $isscopesfirsterror = ($firsterrorelement === self::SCOPE_LABEL);
            $this->scopesfieldsetopen->setText($this->get_scopes_fieldset_open(true, $isscopesfirsterror));

            if ($isscopesfirsterror) {
                // Force focus onto the fieldset explicitly rather than relying on the autofocus
                // attribute alone: this runs once the whole page (including the fieldset itself) has
                // loaded, so it works regardless of whether a given browser honours autofocus on a
                // plain fieldset.
                $PAGE->requires->js_init_code(
                    'document.getElementById(' . json_encode(self::SCOPES_FIELDSET_ID) . ').focus();',
                );
            }
        }

        parent::display();
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
