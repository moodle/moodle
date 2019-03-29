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
 * A scheduled task for chat cron.
 *
 * @package    mod_chat
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_chat\task;

defined('MOODLE_INTERNAL') || die();

/**
 * The main schedule task for the chat module.
 *
 * @package   mod_chat
 * @copyright 2019 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'mod_chat');
    }

    /**
     * Run chat cron.
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/chat/lib.php');

        chat_update_chat_times();
        chat_delete_old_users();

        $timenow = time();
        $subselect = "SELECT c.keepdays
                        FROM {chat} c
                       WHERE c.id = {chat_messages}.chatid";
        $DB->delete_records_select('chat_messages', "($subselect) > 0 AND timestamp < (? - ($subselect) * ?)",
                [$timenow, DAYSECS]);

        $DB->delete_records_select('chat_messages_current', "timestamp < ?", [$timenow - 8 * HOURSECS]);
    }
}
