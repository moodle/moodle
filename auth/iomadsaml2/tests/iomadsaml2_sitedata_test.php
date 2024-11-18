<?php
// This file is part of IOMAD SAML2 Authentication Plugin
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../_autoload.php');

/**
 * Saml2 site data test.
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_sitedata_test extends advanced_testcase {
    public function test_it_creates_the_directory_if_it_does_not_exist() {
        global $CFG;

        $expected = "{$CFG->dataroot}/iomadsaml2";
        self::assertFalse(file_exists($expected));

        /** @var auth_plugin_iomadsaml2 $iomadsaml2 */
        $iomadsaml2 = get_auth_plugin('iomadsaml2');
        self::assertTrue(file_exists($expected));

        rmdir($expected);
        $actual = $iomadsaml2->get_iomadsaml2_directory();
        self::assertTrue(file_exists($expected));

        self::assertSame($expected, $actual);
    }
    public function test_it_emits_an_event_when_saml_certificate_regenerated() {
        global $CFG, $DB;
        $this->resetAfterTest();
        // To test event is emitted to logstore table.
        $this->preventResetByRollback();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        set_config('buffersize', 0, 'logstore_standard');

        // The cert files don't exist now so including setup.php here will regenerate them.
        require($CFG->dirroot . '/auth/iomadsaml2/setup.php');

        // Get log records and check for presence of the cert_regenerated event.
        $logmanger = get_log_manager();
        $readers = $logmanger->get_readers('\core\log\reader');
        $reader = reset($readers);
        $eventarray = $reader->get_events_select("eventname = ?", ['\auth_iomadsaml2\event\cert_regenerated'], 'id ASC', 0, 0);
        self::assertEquals(1, count($eventarray));

        $event = reset($eventarray);
        $eventdata = $event->get_data();
        $expecteddata = [
            'reason' => "= Missing cert pem file! =\n= Missing cert crt file! = \nNow regenerating iomadsaml2 certificates..."
        ];
        self::assertEquals($expecteddata['reason'], $eventdata['other']['reason']);
        self::assertEquals('\auth_iomadsaml2\event\cert_regenerated', $eventdata['eventname']);
    }
}
