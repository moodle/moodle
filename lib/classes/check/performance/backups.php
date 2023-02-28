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
 * Backups check
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\performance;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * Backups check
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backups extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_backup', 'report_performance');
    }

    /**
     * A link to a place to action this
     *
     * @return \action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url('/admin/settings.php', ['section' => 'automated']),
            get_string('automatedsetup', 'backup'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $CFG;

        require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

        $automatedbackupsenabled = get_config('backup', 'backup_auto_active');
        if ($automatedbackupsenabled == \backup_cron_automated_helper::AUTO_BACKUP_ENABLED) {
            $status = result::WARNING;
            $summary = get_string('check_backup_comment_enable', 'report_performance');
        } else {
            $status = result::OK;
            $summary = get_string('check_backup_comment_disable', 'report_performance');
        }

        $details = get_string('check_backup_details', 'report_performance');

        return new result($status, $summary, $details);
    }
}

