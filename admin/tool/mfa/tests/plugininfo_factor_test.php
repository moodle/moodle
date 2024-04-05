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

namespace tool_mfa;

/**
 * Tests for plugininfo.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo_factor_test extends \advanced_testcase {

    /**
     * Tests getting next user factor
     *
     * @covers ::get_next_user_login_factor
     * @covers ::setup_user_factor
     * @covers ::get_enabled_factors
     * @covers ::is_enabled
     * @covers ::has_setup
     * @covers ::get_active_user_factor_types
     */
    public function test_get_next_user_login_factor() {

        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Test that with no enabled factors, fallback is returned.
        $this->assertEquals('fallback', \tool_mfa\plugininfo\factor::get_next_user_login_factor()->name);

        // Setup enabled totp factor for user.
        set_config('enabled', 1, 'factor_totp');
        $totpfactor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $this->assertNotEmpty($totpfactor->setup_user_factor((object) $totpdata));

        // Test that factor now appears (from STATE_UNKNOWN).
        $this->assertEquals('totp', \tool_mfa\plugininfo\factor::get_next_user_login_factor()->name);

        // Now pass this factor, check for fallback.
        $totpfactor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals('fallback', \tool_mfa\plugininfo\factor::get_next_user_login_factor()->name);

        // Add in a no-input factor.
        set_config('enabled', 1, 'factor_auth');
        $this->assertEquals(2, count(\tool_mfa\plugininfo\factor::get_enabled_factors()));

        $authfactor = \tool_mfa\plugininfo\factor::get_factor('auth');
        $this->assertTrue($authfactor->is_enabled());
        $this->assertFalse($authfactor->has_setup());

        // Check that the next factor is still the fallback factor.
        $this->assertEquals(2, count(\tool_mfa\plugininfo\factor::get_active_user_factor_types()));
        $this->assertEquals('fallback', \tool_mfa\plugininfo\factor::get_next_user_login_factor()->name);
    }

    /**
     * Tests if a user has more than one active factor.
     *
     * @covers ::user_has_more_than_one_active_factors
     */
    public function test_user_has_more_than_one_active_factors(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create two active user factors.
        set_config('enabled', 1, 'factor_totp');
        set_config('enabled', 1, 'factor_webauthn');

        $data = new \stdClass();
        $data->userid = $user->id;
        $data->factor = 'totp';
        $data->label = 'testtotp';
        $data->revoked = 0;
        $DB->insert_record('tool_mfa', $data);

        $data = new \stdClass();
        $data->userid = $user->id;
        $data->factor = 'webauthn';
        $data->label = 'testwebauthn';
        $data->revoked = 0;
        $factorid = $DB->insert_record('tool_mfa', $data);

        // Test there is more than one active factor.
        $hasmorethanonefactor = \tool_mfa\plugininfo\factor::user_has_more_than_one_active_factors();
        $this->assertTrue($hasmorethanonefactor);

        // Revoke a factor.
        $DB->set_field('tool_mfa', 'revoked', 1, ['id' => $factorid]);

        // There should no longer be more than one active factor.
        $hasmorethanonefactor = \tool_mfa\plugininfo\factor::user_has_more_than_one_active_factors();
        $this->assertFalse($hasmorethanonefactor);
    }
}
