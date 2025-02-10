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
 * Process binding username claim tool.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

use core_text;
use core_user;
use csv_import_reader;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Class process represents the process binding username claim tool.
 */
class process {
    const ROUTE_AUTH_OIDC_RENAME = 1;
    const ROUTE_AUTH_OTHER_MATCH = 2;

    /** @var csv_import_reader */
    protected $cir;
    /** @var array */
    protected $filecolumns = null;
    /** @var stdClass */
    protected $formdata;
    /** @var update_progress_tracker */
    protected $upt;
    /** @var int */
    protected $userserrors = 0;
    /** @var int */
    protected $usersupdated = 0;

    /**
     * Process constructor.
     *
     * @param csv_import_reader $cir
     */
    public function __construct(csv_import_reader $cir) {
        $this->cir = $cir;
    }

    /**
     * Get the file columns.
     *
     * @return array
     * @throws moodle_exception
     */
    public function get_file_columns() : array {
        if ($this->filecolumns === null) {
            $columns = $this->cir->get_columns();
            if (count($columns) != 2) {
                $this->cir->close();
                $this->cir->cleanup();
                throw new moodle_exception('error_invalid_upload_file', 'auth_oidc');
            }

            $stdfields = ['username', 'new_username'];
            $this->filecolumns = [];

            foreach ($columns as $key => $unused) {
                $field = $columns[$key];
                $field = trim($field);
                $lcfield = core_text::strtolower($field);
                if (in_array($field, $stdfields) or in_array($lcfield, $stdfields)) {
                    $newfield = $lcfield;
                }
                if (in_array($newfield, $this->filecolumns)) {
                    $this->cir->close();
                    $this->cir->cleanup();
                    throw new moodle_exception('duplicate_upload_field', 'auth_oidc', '', $field);
                }
                $this->filecolumns[$key] = $newfield;
            }
        }

        return $this->filecolumns;
    }

    /**
     * Set the form data.
     *
     * @param stdClass $formdata
     */
    public function set_form_data(stdClass $formdata) {
        $this->formdata = $formdata;
    }

    /**
     * Process the CSV file.
     */
    public function process() {
        // Initialise the CSV import reader.
        $this->cir->init();

        $this->upt = new upload_process_tracker();
        $this->upt->start();

        $linenum = 1; // Column header is first line.
        while ($line = $this->cir->next()) {
            $this->upt->flush();
            $linenum++;

            $this->upt->track('line', $linenum);
            $this->process_line($line);
        }

        $this->upt->close();
        $this->cir->close();
        $this->cir->cleanup(true);
    }

    /**
     * Process a line from the CSV file.
     *
     * @param array $line
     */
    protected function process_line(array $line) {
        global $DB;

        $username = '';
        $lcusername = '';
        $newusername = '';
        $lcnewusername = '';

        foreach ($line as $keynum => $value) {
            $key = $this->get_file_columns()[$keynum];
            if ($key == 'username') {
                $username = $value;
                $lcusername = core_text::strtolower($username);
                $this->upt->track('username', $username);
            } else if ($key == 'new_username') {
                $newusername = $value;
                $lcnewusername = core_text::strtolower($newusername);
            }
        }

        if (!$username || !$lcusername || !$newusername || !$lcnewusername) {
            $this->upt->track('status', get_string('update_error_incomplete_line', 'auth_oidc'));
            $this->userserrors++;

            return;
        }

        $user = core_user::get_user_by_username($lcusername);
        if (!$user) {
            $user = core_user::get_user_by_email($lcusername);
            $this->upt->track('status', get_string('update_warning_email_match', 'auth_oidc'));
        }

        if ($user && $user->auth == 'oidc') {
            $route = self::ROUTE_AUTH_OIDC_RENAME;
        } else {
            $route = self::ROUTE_AUTH_OTHER_MATCH;
        }

        if ($newusername !== core_user::clean_field($newusername, 'username')) {
            $this->upt->track('status', get_string('update_error_invalid_new_username', 'auth_oidc'));
            $this->userserrors++;

            return;
        }

        $this->upt->track('new_username', $newusername);

        // All check passed, update the user record.
        $userupdated = false;
        $authoidctokenupdated = false;
        $localo365objectupdated = false;

        // Step 1: Update the user object, if route is auth_oidc rename.
        if ($route == self::ROUTE_AUTH_OIDC_RENAME) {
            $this->upt->track('id', $user->id);

            $user->username = $lcnewusername;
            try {
                user_update_user($user, false);
                $userupdated = true;
            } catch (moodle_exception $e) {
                $this->upt->track('status', get_string('update_error_user_update_failed', 'auth_oidc'));
                $this->userserrors++;

                return;
            }
        }

        // Step 2: Update the token record.
        if ($route == self::ROUTE_AUTH_OIDC_RENAME) {
            if ($tokenrecord = $DB->get_record('auth_oidc_token', ['userid' => $user->id])) {
                $tokenrecord->username = $lcnewusername;
                $tokenrecord->useridentifier = $newusername;
                $DB->update_record('auth_oidc_token', $tokenrecord);
                $authoidctokenupdated = true;
            }
        } else {
            $sql = "SELECT *
                      FROM {auth_oidc_token}
                     WHERE lower(useridentifier) = ?";
            if ($tokenrecord = $DB->get_record_sql($sql, [$lcusername])) {
                $tokenrecord->useridentifier = $newusername;
                $DB->update_record('auth_oidc_token', $tokenrecord);
                $authoidctokenupdated = true;
            }
        }

        // Step 3: Update connection record in local_o365_object table.
        if (auth_oidc_is_local_365_installed()) {
            if ($route == static::ROUTE_AUTH_OIDC_RENAME) {
                if ($connectionrecord = $DB->get_record('local_o365_objects', ['type' => 'user', 'moodleid' => $user->id])) {
                    $connectionrecord->o365name = $newusername;
                    $DB->update_record('local_o365_objects', $connectionrecord);
                    $localo365objectupdated = true;
                }
            } else {
                $sql = "SELECT *
                          FROM {local_o365_objects}
                         WHERE type = 'user'
                           AND lower(o365name) = ?";
                if ($connectionrecord = $DB->get_record_sql($sql, [$lcusername])) {
                    $connectionrecord->o365name = $newusername;
                    $DB->update_record('local_o365_objects', $connectionrecord);
                    $localo365objectupdated = true;
                }
            }
        }

        if ($userupdated) {
            $this->upt->track('status', get_string('update_success_username', 'auth_oidc'));
        }

        if ($authoidctokenupdated) {
            $this->upt->track('status', get_string('update_success_token', 'auth_oidc'));
        }

        if ($localo365objectupdated) {
            $this->upt->track('status', get_string('update_success_o365', 'auth_oidc'));
        }

        if ($userupdated || $authoidctokenupdated || $localo365objectupdated) {
            // At least one of the records has been updated.
            $this->usersupdated++;
        } else {
            $this->upt->track('status', get_string('update_error_nothing_updated', 'auth_oidc'));
            $this->userserrors++;
        }
    }

    /**
     * Return stats about the process.
     *
     * @return array
     */
    public function get_stats() : array {
        $lines = [];

        $lines[] = get_string('update_stats_users_updated', 'auth_oidc', $this->usersupdated);
        $lines[] = get_string('update_stats_users_errors', 'auth_oidc', $this->userserrors);

        return $lines;
    }
}
