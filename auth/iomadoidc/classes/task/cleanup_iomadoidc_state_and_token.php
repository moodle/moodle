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
 * A scheduled task to clean up iomadoidc state and invalid token.
 *
 * @package auth_iomadoidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_iomadoidc\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;

/**
 * A scheduled task that cleans up iomadoidc states and tokens.
 */
class cleanup_iomadoidc_state_and_token extends scheduled_task {
    /**
     * Get a descriptive name for the task.
     */
    public function get_name() {
        return get_string('task_cleanup_iomadoidc_state_and_token', 'auth_iomadoidc');
    }

    /**
     * Clean up iomadoidc state and invalid iomadoidc token.
     */
    public function execute() {
        global $DB;

        // Clean up iomadoidc state.
        $DB->delete_records_select('auth_iomadoidc_state', 'timecreated < ?', [strtotime('-5 min')]);

        // Clean up invalid iomadoidc token.
        $DB->delete_records('auth_iomadoidc_token', ['userid' => 0]);
    }
}
