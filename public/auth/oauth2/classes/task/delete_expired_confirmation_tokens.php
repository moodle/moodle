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

namespace auth_oauth2\task;

use auth_oauth2\linked_login;
use core\exception\coding_exception;
use core\task\scheduled_task;
use dml_exception;
use lang_string;

/**
 * Task to delete expired confirmation tokens.
 *
 * @package   auth_oauth2
 * @copyright 2026 eDaktik GmbH {@link https://www.edaktik.at/}
 * @author    Christian Abila <christian.abila@edaktik.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_expired_confirmation_tokens extends scheduled_task {
    /**
     * Return the task's name.
     *
     * @return lang_string|string
     * @throws coding_exception
     */
    public function get_name(): lang_string|string {
        return get_string('deleteexpiredconfirmtokens', 'auth_oauth2');
    }

    /**
     * Execute the task
     *
     * @return void
     * @throws dml_exception
     */
    public function execute(): void {
        linked_login::delete_expired_confirmation_tokens();
    }
}
