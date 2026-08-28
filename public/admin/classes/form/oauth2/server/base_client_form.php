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

namespace core_admin\form\oauth2\server;

use moodleform;

/**
 * Base form for OAuth 2 client create/edit forms.
 *
 * Contains functionality shared between the create and edit forms.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_client_form extends moodleform {
    /**
     * Add the common client name and description fields.
     *
     * @return void
     */
    protected function add_client_details(): void {
        $mform = $this->_form;

        // Name field.
        $mform->addElement('text', 'name', get_string('name'), ['size' => '60']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');

        // Description field.
        $mform->addElement('textarea', 'description', get_string('description'), ['rows' => 3]);
        $mform->setType('description', PARAM_TEXT);
    }

    /**
     * Add the repeatable redirect URI fields.
     *
     * @param array $redirecturis Existing redirect URIs.
     * @return void
     */
    protected function add_redirect_uri_elements(array $redirecturis = []): void {
        $mform = $this->_form;

        // Redirect URI group elements.
        $groupelements = [];
        $groupelements[] = $mform->createElement('text', 'redirecturi', '', ['size' => '60']);
        $groupelements[] = $mform->createElement(
            'submit',
            'delete_redirecturi_field',
            '✕',
            ['class' => 'ps-1'],
            false,
            ['customclassoverride' => 'btn btn-outline-secondary'],
        );

        $repeatarray = [];
        $repeatarray[] = $mform->createElement('group', 'redirecturigroup', '', $groupelements, '', false);

        $mform->setType('redirecturi', PARAM_RAW_TRIMMED);
        $mform->registerNoSubmitButton('delete_redirecturi_field');

        $repeatoptions = [
            'redirecturigroup' => [
                'redirecturi' => ['type' => PARAM_RAW_TRIMMED],
            ],
        ];

        // Always display at least one row.
        $repeatcount = max(1, count($redirecturis));

        $this->repeat_elements(
            $repeatarray,
            $repeatcount,
            $repeatoptions,
            'redirecturi_repeats',
            'add_redirecturi_fields',
            1,
            get_string('oauth2server_clientaddcallbackuri', 'admin'),
            true,
            'delete_redirecturi_field'
        );

        $this->format_first_redirect_uri_row();

        // Help text.
        $mform->addElement(
            'static',
            'redirecturis_footer',
            '',
            \html_writer::span(get_string('oauth2server_clientcallbackurisdesc', 'admin'), 'text-muted small d-block'),
        );
    }

    /**
     * Format the first redirect URI row.
     *
     * The first row is always present and cannot be deleted.
     *
     * @return void
     */
    protected function format_first_redirect_uri_row(): void {
        $mform = $this->_form;

        if (!$mform->elementExists('redirecturigroup[0]')) {
            return;
        }

        $firstredirecturi = $mform->getElement('redirecturigroup[0]');
        $firstredirecturi->setLabel(get_string('oauth2server_clientcallbackuris', 'admin'));

        // Remove the delete button from the first row.
        $filteredelements = [];

        foreach ($firstredirecturi->getElements() as $element) {
            if (strpos($element->getName(), 'delete_redirecturi_field') === false) {
                $filteredelements[] = $element;
            }
        }

        $firstredirecturi->setElements($filteredelements);
    }

    /**
     * Validate redirect URI fields.
     *
     * Returns errors for invalid URI values and optionally requires at least
     * one URI to be provided.
     *
     * @param array $data Submitted form data.
     * @param bool $required Whether at least one redirect URI is required.
     * @return array Array of errors indexed by form element name.
     */
    protected function validate_redirect_uris(array $data, bool $required = true): array {
        $errors = [];

        $redirecturis = $data['redirecturi'] ?? [];

        if (!is_array($redirecturis)) {
            $redirecturis = [$redirecturis];
        }

        // Tracks whether at least one URI has been set.
        $hasuriset = false;

        foreach ($redirecturis as $index => $redirecturi) {
            $uri = trim((string) ($redirecturi ?? ''));

            // Empty rows are allowed.
            if ($uri === '') {
                continue;
            }

            // URI has been set. Next, let's validate it.
            $hasuriset = true;

            $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);

            try {
                $clientmanager->validate_redirect_uri_format($uri);
            } catch (\moodle_exception $e) {
                $errors["redirecturigroup[{$index}]"] = get_string('oauth2server_clientcallbackurisinvalid', 'admin');
                continue;
            }
        }

        // At least one URI is required when requested by the form.
        if ($required && !$hasuriset) {
            $errors['redirecturigroup[0]'] = get_string('oauth2server_clientcallbackurirequired', 'admin');
        }

        return $errors;
    }
}
