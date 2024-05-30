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
use tool_mfa\tool_mfa_trait;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/tool_mfa_trait.php');

/**
 * Tests for base factor implementation methods.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   2023 Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class object_factor_base_test extends \advanced_testcase {

    use tool_mfa_trait;

    /**
     * Test deleting user's configured factors
     *
     * @covers ::setup_user_factor
     * @return void
     */
    public function test_revoke_user_factor(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->set_factor_state('totp', 1, 100);
        $totpfactor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $factorinstance = $totpfactor->setup_user_factor((object) $totpdata);
        $totpdata2 = [
            'secret' => 'fakekey2',
            'devicename' => 'fakedevice2',
        ];
        $totpfactor->setup_user_factor((object) $totpdata2);

        $this->assertFalse((bool) $factorinstance->revoked);
        $this->assertEquals(2, count($totpfactor->get_active_user_factors($user)));

        // Test that calling the revoke on the generic type revokes all.
        $totpfactor->revoke_user_factor();
        $this->assertEquals(0, count($totpfactor->get_active_user_factors($user)));

        // Add another factor for testing.
        $totpdata3 = [
            'secret' => 'fakekey3',
            'devicename' => 'fakedevice3',
        ];
        $factorinstance2 = $totpfactor->setup_user_factor((object) $totpdata3);

        // Now test you can't revoke another users factor.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);

        $this->assertFalse($totpfactor->revoke_user_factor($factorinstance2->id));
        $this->assertEquals(1, count($totpfactor->get_active_user_factors($user)));

        // Now revoke as ourselves.
        $this->setUser($user);
        $this->assertTrue($totpfactor->revoke_user_factor($factorinstance2->id));
        $this->assertEquals(0, count($totpfactor->get_active_user_factors($user)));
    }

    /**
     * Tests the replacement of a factor.
     *
     * @covers ::setup_user_factor
     * @covers ::replace_user_factor
     */
    public function test_replace_user_factor(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $factor = \tool_mfa\plugininfo\factor::get_factor('totp');

        // Set up the factor.
        $data1 = new \stdClass();
        $data1->secret = 'fakesecret1';
        $data1->devicename = 'fakedevice1';
        $factor1 = $factor->setup_user_factor($data1);

        // Prepare some replacement data.
        $data2 = new \stdClass();
        $data2->secret = 'fakesecret2';
        $data2->devicename = 'fakedevice2';

        // Replace the active factor with the replacement data.
        $factor2 = $factor->replace_user_factor($data2, $factor1->id);

        // Check the active factor is the newer one.
        $activefactors = $factor->get_active_user_factors($user);
        $this->assertEquals(1, count($activefactors));
        $this->assertEquals($factor2->id, $activefactors[0]->id);
    }
}
