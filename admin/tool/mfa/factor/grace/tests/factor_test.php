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

namespace factor_grace;

/**
 * Tests for grace factor.
 *
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class factor_test extends \advanced_testcase {

    /**
     * Test affecting factors
     *
     * @covers ::get_affecting_factors
     * @return void
     */
    public function test_affecting_factors(): void {
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Disable the email factor (enabled by default).
        set_config('enabled', 0, 'factor_email');

        $grace = \tool_mfa\plugininfo\factor::get_factor('grace');
        $affecting = $grace->get_affecting_factors();
        $this->assertEquals(0, count($affecting));

        set_config('enabled', 1, 'factor_totp');
        $totpfactor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $totpfactor->setup_user_factor((object) $totpdata);

        // Confirm that MFA is the only affecting factor.
        $affecting = $grace->get_affecting_factors();
        $this->assertEquals(1, count($affecting));
        $totp = reset($affecting);
        $this->assertTrue($totp instanceof \factor_totp\factor);

        // Now put it in the ignorelist.
        set_config('ignorelist', 'totp', 'factor_grace');
        // Confirm that MFA is the only affecting factor.
        $affecting = $grace->get_affecting_factors();
        $this->assertEquals(0, count($affecting));
    }
}
