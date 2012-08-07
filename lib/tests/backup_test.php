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
 * Unit tests for backups.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2012 Frédéric Massart <fred@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

/**
 * Unit tests for backup system
 */
class backup_testcase extends advanced_testcase {

    public function test_next_automated_backup() {

        $this->resetAfterTest();
        $admin = get_admin();
        $timezone = $admin->timezone;

        // Notes
        // - The next automated backup will never be on the same date than $now
        // - backup_auto_weekdays starts on Sunday
        // - Tests cannot be done in the past.

        // Every Wed and Sat at 11pm.
        set_config('backup_auto_active', '1', 'backup');
        set_config('backup_auto_weekdays', '0010010', 'backup');
        set_config('backup_auto_hour', '23', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Thursday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Friday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Saturday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Sunday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        // Every Sun and Sat at 12pm.
        set_config('backup_auto_active', '1', 'backup');
        set_config('backup_auto_weekdays', '1000001', 'backup');
        set_config('backup_auto_hour', '0', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Thursday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Friday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Saturday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-00:00', date('w-H:i', $next));

        $now = strtotime('next Sunday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        // Every Sun at 4am.
        set_config('backup_auto_active', '1', 'backup');
        set_config('backup_auto_weekdays', '1000000', 'backup');
        set_config('backup_auto_hour', '4', 'backup');
        set_config('backup_auto_minute', '0', 'backup');

        $now = strtotime('next Monday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Thursday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Friday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Saturday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Sunday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        // Every day but Wed at 8:30pm.
        set_config('backup_auto_active', '1', 'backup');
        set_config('backup_auto_weekdays', '1110111', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '30', 'backup');

        $now = strtotime('next Monday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-20:30', date('w-H:i', $next));

        $now = strtotime('next Tuesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Wednesday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Thursday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-20:30', date('w-H:i', $next));

        $now = strtotime('next Friday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-20:30', date('w-H:i', $next));

        $now = strtotime('next Saturday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-20:30', date('w-H:i', $next));

        $now = strtotime('next Sunday');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('1-20:30', date('w-H:i', $next));

    }
}
