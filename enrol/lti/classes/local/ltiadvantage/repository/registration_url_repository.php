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

namespace enrol_lti\local\ltiadvantage\repository;

use enrol_lti\local\ltiadvantage\entity\registration_url;

/**
 * Class registration_url_repository, for saving registration_url instances to the store.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_url_repository {

    /** @var string the name of the table in which the registration token identifying the valid URL is stored. */
    private $regtokentable = 'enrol_lti_reg_token';

    /**
     * Save a registration_url instance.
     *
     * @param registration_url $regurl the instance.
     * @return registration_url the instance that was saved.
     */
    public function save(registration_url $regurl): registration_url {
        // A new URL will always override an existing URL, so no need to check for existence.
        // If the new URL is valid, just delete the old one and insert the new one.
        global $DB;
        $created = time();
        $expirytime = $regurl->get_expiry_time();
        $DB->delete_records($this->regtokentable);
        $DB->insert_record($this->regtokentable,
            ['token' => $regurl->get_param('token'), 'expirytime' => $expirytime, 'timecreated' => $created]);

        return $regurl;
    }

    /**
     * Get the registration_url if present.
     *
     * @return registration_url|null the registration_url instance, or null if not found.
     */
    public function find(): ?registration_url {
        global $DB;

        try {
            $tokenrec = $DB->get_record_select($this->regtokentable, 'expirytime > :timenow', ['timenow' => time()],
                '*', MUST_EXIST);
            return new registration_url($tokenrec->expirytime, $tokenrec->token);
        } catch (\dml_missing_record_exception $ex) {
            return null;
        }
    }

    /**
     * Find a registration_url by its unique token.
     *
     * @param string $token the token.
     * @return registration_url|null the registration_url, or null if not found.
     */
    public function find_by_token(string $token): ?registration_url {
        global $DB;

        try {
            $tokenrec = $DB->get_record_select($this->regtokentable, 'token = :token AND expirytime > :timenow',
                ['token' => $token, 'timenow' => time()], '*', MUST_EXIST);
            return new registration_url($tokenrec->expirytime, $tokenrec->token);
        } catch (\dml_missing_record_exception $ex) {
            return null;
        }
    }

    /**
     * Delete the registration_url.
     */
    public function delete(): void {
        global $DB;
        $DB->delete_records($this->regtokentable);
    }
}
