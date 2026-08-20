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

/**
 * OAuth 2 Client edit form.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_client_form extends base_client_form {
    /**
     * Form definition.
     */
    public function definition(): void {
        $cliententity = $this->_customdata['cliententity'];

        // Add the Name and Description fields.
        $this->add_client_details();

        // Populate the fields with default values.
        $this->set_data((object) [
            'name' => $cliententity->getName(),
            'description' => $cliententity->get_description(),
        ]);

        // Redirect URIs only apply to Authorization Code clients.
        if (in_array('authorization_code', $cliententity->get_grant_types(), true)) {
            $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);
            $redirecturis = $clientmanager->get_redirect_uris($cliententity->get_id());

            // Add redirect URI fields and populate existing values.
            $this->add_redirect_uri_elements($redirecturis);

            // Populate the fields with default values.
            $this->set_data((object) [
                'redirecturi' => array_values($redirecturis),
            ]);
        }

        // Action buttons.
        $this->add_action_buttons(true);
    }

    /**
     * Server-side validation.
     *
     * @param array $data Submitted form data.
     * @param array $files Submitted files.
     * @return array Array of errors indexed by field name.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        $cliententity = $this->_customdata['cliententity'];
        $hasauthorizationcode = in_array('authorization_code', $cliententity->get_grant_types(), true);

        // Only validate redirect URIs when this client uses the Authorization Code grant.
        if ($hasauthorizationcode) {
            $errors = array_merge($errors, $this->validate_redirect_uris($data, true));
        }

        return $errors;
    }
}
