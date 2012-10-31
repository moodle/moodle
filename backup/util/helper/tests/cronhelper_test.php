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
 * Unit tests for backups cron helper.
 *
 * @package   core_backup
 * @category  phpunit
 * @copyright 2012 Frédéric Massart <fred@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

/**
 * Unit tests for backup cron helper
 */
class backup_cron_helper_testcase extends advanced_testcase {

    /**
     * Test {@link backup_cron_automated_helper::calculate_next_automated_backup}.
     */
    public function test_next_automated_backup() {
        $this->resetAfterTest();
        set_config('backup_auto_active', '1', 'backup');

        // Notes
        // - backup_auto_weekdays starts on Sunday
        // - Tests cannot be done in the past
        // - Only the DST on the server side is handled.

        // Every Tue and Fri at 11pm.
        set_config('backup_auto_weekdays', '0010010', 'backup');
        set_config('backup_auto_hour', '23', 'backup');
        set_config('backup_auto_minute', '0', 'backup');
        $timezone = 99;

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-23:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-23:00', date('w-H:i', $next));

        // Every Sun and Sat at 12pm.
        set_config('backup_auto_weekdays', '1000001', 'backup');
        set_config('backup_auto_hour', '0', 'backup');
        set_config('backup_auto_minute', '0', 'backup');
        $timezone = 99;

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-00:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        // Every Sun at 4am.
        set_config('backup_auto_weekdays', '1000000', 'backup');
        set_config('backup_auto_hour', '4', 'backup');
        set_config('backup_auto_minute', '0', 'backup');
        $timezone = 99;

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-04:00', date('w-H:i', $next));

        // Every day but Wed at 8:30pm.
        set_config('backup_auto_weekdays', '1110111', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '30', 'backup');
        $timezone = 99;

        $now = strtotime('next Monday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('1-20:30', date('w-H:i', $next));

        $now = strtotime('next Tuesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-20:30', date('w-H:i', $next));

        $now = strtotime('next Wednesday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Thursday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-20:30', date('w-H:i', $next));

        $now = strtotime('next Friday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('5-20:30', date('w-H:i', $next));

        $now = strtotime('next Saturday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-20:30', date('w-H:i', $next));

        $now = strtotime('next Sunday 17:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-20:30', date('w-H:i', $next));

        // Sun, Tue, Thu, Sat at 12pm.
        set_config('backup_auto_weekdays', '1010101', 'backup');
        set_config('backup_auto_hour', '0', 'backup');
        set_config('backup_auto_minute', '0', 'backup');
        $timezone = 99;

        $now = strtotime('next Monday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-00:00', date('w-H:i', $next));

        $now = strtotime('next Tuesday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-00:00', date('w-H:i', $next));

        $now = strtotime('next Wednesday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('4-00:00', date('w-H:i', $next));

        $now = strtotime('next Thursday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Friday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('6-00:00', date('w-H:i', $next));

        $now = strtotime('next Saturday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0-00:00', date('w-H:i', $next));

        $now = strtotime('next Sunday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('2-00:00', date('w-H:i', $next));

        // None.
        set_config('backup_auto_weekdays', '0000000', 'backup');
        set_config('backup_auto_hour', '15', 'backup');
        set_config('backup_auto_minute', '30', 'backup');
        $timezone = 99;

        $now = strtotime('next Sunday 13:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals('0', $next);

        // Playing with timezones.
        set_config('backup_auto_weekdays', '1111111', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '00', 'backup');

        $timezone = 99;
        date_default_timezone_set('Australia/Perth');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        $timezone = 99;
        date_default_timezone_set('Europe/Brussels');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        $timezone = 99;
        date_default_timezone_set('America/New_York');
        $now = strtotime('18:00:00');
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        // Viva Australia! (UTC+8).
        date_default_timezone_set('Australia/Perth');
        $now = strtotime('18:00:00');

        $timezone = -10.0; // 12am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-14:00', strtotime('tomorrow')), date('w-H:i', $next));

        $timezone = -5.0; // 5am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-09:00', strtotime('tomorrow')), date('w-H:i', $next));

        $timezone = 0.0;  // 10am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-04:00', strtotime('tomorrow')), date('w-H:i', $next));

        $timezone = 3.0; // 1pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-01:00', strtotime('tomorrow')), date('w-H:i', $next));

        $timezone = 8.0; // 6pm for the user (same than the server).
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-20:00'), date('w-H:i', $next));

        $timezone = 9.0; // 7pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-19:00'), date('w-H:i', $next));

        $timezone = 13.0; // 12am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $this->assertEquals(date('w-15:00', strtotime('tomorrow')), date('w-H:i', $next));

        // Let's have a Belgian beer! (UTC+1 / UTC+2 DST).
        // Warning: Some of these tests will fail if executed "around"
        // 'Europe/Brussels' DST changes (last Sunday in March and
        // last Sunday in October right now - 2012). Once Moodle
        // moves to PHP TZ support this could be fixed properly.
        date_default_timezone_set('Europe/Brussels');
        $now = strtotime('18:00:00');
        $dst = date('I', $now);

        $timezone = -10.0; // 7am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-07:00', strtotime('tomorrow')) : date('w-08:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = -5.0; // 12pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-02:00', strtotime('tomorrow')) : date('w-03:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 0.0;  // 5pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-21:00') : date('w-22:00');
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 3.0; // 8pm for the user (note the expected time is today while in DST).
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-18:00', strtotime('tomorrow')) : date('w-19:00');
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 8.0; // 1am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-13:00', strtotime('tomorrow')) : date('w-14:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 9.0; // 2am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-12:00', strtotime('tomorrow')) : date('w-13:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 13.0; // 6am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-08:00', strtotime('tomorrow')) : date('w-09:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        // The big apple! (UTC-5 / UTC-4 DST).
        // Warning: Some of these tests will fail if executed "around"
        // 'America/New_York' DST changes (2nd Sunday in March and
        // 1st Sunday in November right now - 2012). Once Moodle
        // moves to PHP TZ support this could be fixed properly.
        date_default_timezone_set('America/New_York');
        $now = strtotime('18:00:00');
        $dst = date('I', $now);

        $timezone = -10.0; // 1pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-01:00', strtotime('tomorrow')) : date('w-02:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = -5.0; // 6pm for the user (server time).
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-20:00') : date('w-21:00');
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 0.0;  // 11pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-15:00', strtotime('tomorrow')) : date('w-16:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 3.0; // 2am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-12:00', strtotime('tomorrow')) : date('w-13:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 8.0; // 7am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-07:00', strtotime('tomorrow')) : date('w-08:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 9.0; // 8am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-06:00', strtotime('tomorrow')) : date('w-07:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 13.0; // 6am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? date('w-02:00', strtotime('tomorrow')) : date('w-03:00', strtotime('tomorrow'));
        $this->assertEquals($expected, date('w-H:i', $next));

        // Some more timezone tests
        set_config('backup_auto_weekdays', '0100001', 'backup');
        set_config('backup_auto_hour', '20', 'backup');
        set_config('backup_auto_minute', '00', 'backup');

        // Note: These tests should not fail because they are "unnafected"
        // by DST changes, as far as execution always happens on Monday and
        // Saturday and those week days are not, right now, the ones rulez
        // to peform the DST changes (Sunday is). This may change if rules
        // are modified in the future.
        date_default_timezone_set('Europe/Brussels');
        $now = strtotime('next Monday 18:00:00');
        $dst = date('I', $now);

        $timezone = -12.0;  // 1pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '2-09:00' : '2-10:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = -4.0;  // 1pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '2-01:00' : '2-02:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 0.0;  // 5pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '1-21:00' : '1-22:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 2.0;  // 7pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '1-19:00' : '1-20:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 4.0;  // 9pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '6-17:00' : '6-18:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 12.0;  // 6am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '6-09:00' : '6-10:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        // Some more timezone tests
        set_config('backup_auto_weekdays', '0100001', 'backup');
        set_config('backup_auto_hour', '02', 'backup');
        set_config('backup_auto_minute', '00', 'backup');

        // Note: These tests should not fail because they are "unnafected"
        // by DST changes, as far as execution always happens on Monday and
        // Saturday and those week days are not, right now, the ones rulez
        // to peform the DST changes (Sunday is). This may change if rules
        // are modified in the future.
        date_default_timezone_set('America/New_York');
        $now = strtotime('next Monday 04:00:00');
        $dst = date('I', $now);

        $timezone = -12.0;  // 8pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '1-09:00' : '1-10:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = -4.0;  // 4am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '6-01:00' : '6-02:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 0.0;  // 8am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '5-21:00' : '5-22:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 2.0;  // 10am for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '5-19:00' : '5-20:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 4.0;  // 12pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '5-17:00' : '5-18:00';
        $this->assertEquals($expected, date('w-H:i', $next));

        $timezone = 12.0;  // 8pm for the user.
        $next = backup_cron_automated_helper::calculate_next_automated_backup($timezone, $now);
        $expected = !$dst ? '5-09:00' : '5-10:00';
        $this->assertEquals($expected, date('w-H:i', $next));

    }
}
