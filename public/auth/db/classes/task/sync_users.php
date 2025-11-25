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

namespace auth_db\task;

/**
 * Sync users task class
 * @package   auth_db
 * @author    Guy Thomas <gthomas@moodlerooms.com>
 * @copyright Copyright (c) 2017 Blackboard Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_users extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('auth_dbsyncuserstask', 'auth_db');
    }

    /**
     * Run task for synchronising users.
     */
    public function execute() {
        if (!is_enabled_auth('db')) {
            mtrace('auth_db plugin is disabled, synchronisation stopped');
            return;
        }

        $dbauth = get_auth_plugin('db');
        $config = get_config('auth_db');
        $trace = new \text_progress_trace();
        $update = !empty($config->updateusers);
        $dbauth->sync_users($trace, $update);
    }

}
