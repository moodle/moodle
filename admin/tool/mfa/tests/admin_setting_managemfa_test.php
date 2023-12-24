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
 * Tests for MFA admin settings
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_managemfa_test extends \advanced_testcase {

    use tool_mfa_trait;

    /**
     * Tests getting the factor combinations
     *
     * @covers ::get_factor_combinations
     */
    public function test_get_factor_combinations_default() {
        $namagemfa = new \tool_mfa\local\admin_setting_managemfa();
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $namagemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(0, count($factors));
        $this->assertEquals(0, count($combinations));
    }

    /**
     * Data provider for test_get_factor_combinations_with_data_provider().
     *
     * @return array
     */
    public function get_factor_combinations_provider() {
        $provider = [];

        $factors = [];
        $provider[] = [$factors, 0];

        $factors = [];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 90];
        $provider[] = [$factors, 0];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 100];
        $provider[] = [$factors, 1];

        $factors = [];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 150];
        $provider[] = [$factors, 1];

        $factors = [];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 40];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 40];
        $provider[] = [$factors, 0];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 90];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 40];
        $provider[] = [$factors, 1];

        $factors = [];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 100];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 100];
        $provider[] = [$factors, 2];

        $factors = [];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 100];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 100];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 100];
        $provider[] = [$factors, 3];

        $factors = [];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 90];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 30];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 40];
        $provider[] = [$factors, 2];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 30];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 40];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 90];
        $provider[] = [$factors, 3];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 30];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 40];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 90];
        $factors[] = ['name' => 'auth', 'enabled' => 1, 'weight' => 90];
        $provider[] = [$factors, 7];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'auth', 'enabled' => 1, 'weight' => 50];
        $provider[] = [$factors, 6];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 0, 'weight' => 50];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'totp', 'enabled' => 0, 'weight' => 50];
        $factors[] = ['name' => 'auth', 'enabled' => 1, 'weight' => 50];
        $provider[] = [$factors, 1];

        $factors = [];
        $factors[] = ['name' => 'email', 'enabled' => 0, 'weight' => 50];
        $factors[] = ['name' => 'iprange', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'totp', 'enabled' => 1, 'weight' => 50];
        $factors[] = ['name' => 'auth', 'enabled' => 1, 'weight' => 50];
        $provider[] = [$factors, 3];

        return $provider;
    }

    /**
     * Tests getting the factor combinations with data provider
     *
     * @covers ::get_factor_combinations
     * @dataProvider get_factor_combinations_provider
     * @param array $factorset configured factors
     * @param int $combinationscount expected count of available combinations
     */
    public function test_get_factor_combinations_with_data_provider($factorset, $combinationscount) {
        $this->resetAfterTest();
        $enabledcount = 0;

        foreach ($factorset as $factor) {
            $this->set_factor_state($factor['name'], $factor['enabled'], $factor['weight']);
            if ($factor['enabled'] == 1) {
                $enabledcount++;
            }
        }

        $managemfa = new \tool_mfa\local\admin_setting_managemfa();
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);

        foreach ($combinations as $combination) {
            $this->assertGreaterThanOrEqual(100, $combination['totalweight']);
            $this->assertLessThan(200, $combination['totalweight']);
            $this->assertGreaterThanOrEqual(1, count($combination['combination']));
            foreach ($combination['combination'] as $combinationfactor) {
                $this->assertInstanceOf('\tool_mfa\local\factor\object_factor', $combinationfactor);
            }
        }

        $this->assertEquals($enabledcount, count($factors));
        $this->assertEquals($combinationscount, count($combinations));
    }

    /**
     * Tests checking the factor combinations
     *
     * @covers ::get_factor_combinations
     */
    public function test_factor_combination_checker() {
        $this->resetAfterTest();
        $managemfa = new \tool_mfa\local\admin_setting_managemfa();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Test combination with 2 valid compatible factors.
        $this->set_factor_state('email', 1, 50);
        $this->set_factor_state('totp', 1, 50);

        // Check that there is only 1 valid combination.
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(1, count($combinations));

        // Change weights to 100 for each, and check for 2 valid.
        $this->set_factor_state('email', 1, 100);
        $this->set_factor_state('totp', 1, 100);
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(2, count($combinations));

        // Add another compatible factors, and check for 3 combinations.
        $this->set_factor_state('email', 1, 50);
        $this->set_factor_state('totp', 1, 50);
        $this->set_factor_state('iprange', 1, 50);
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(3, count($combinations));

        // Now same tests again, with an invalid combination set.
        $this->set_factor_state('email', 1, 100);
        $this->set_factor_state('totp', 0, 100);
        $this->set_factor_state('iprange', 0, 50);
        $this->set_factor_state('nosetup', 1, 100);
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(2, count($combinations));

        $this->set_factor_state('totp', 1, 50);
        $this->set_factor_state('email', 0, 50);
        $this->set_factor_state('nosetup', 1, 50);
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(0, count($combinations));

        $this->set_factor_state('email', 1, 50);
        $this->set_factor_state('nosetup', 1, 50);
        $this->set_factor_state('totp', 1, 50);
        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();
        $combinations = $managemfa->get_factor_combinations($factors, 0, count($factors) - 1);
        $this->assertEquals(1, count($combinations));
    }
}
