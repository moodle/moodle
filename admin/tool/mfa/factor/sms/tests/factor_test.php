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

namespace factor_sms;


/**
 * Tests for sms factor.
 *
 * @covers      \factor_sms\factor
 * @package     factor_sms
 * @copyright   2023 Raquel Ortega <raquel.ortega@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Data provider for test_format_number().
     *
     * @return array of different country codes and phone numbers.
     */
    public function format_number_provider(): array {

        return [
            'Phone number with local format' => [
                'phonenumber' => '0123456789',
                'expected' => '+34123456789',
                'countrycode' => '34',
            ],
            'Phone number without international format' => [
                'phonenumber' => '123456789',
                'expected' => '+34123456789',
                'countrycode' => '34',
            ],
            'Phone number with international format' => [
                'phonenumber' => '+39123456789',
                'expected' => '+39123456789',
            ],
            'Phone number with spaces using international format' => [
                'phonenumber' => '+34 123 456 789',
                'expected' => '+34123456789',
            ],
            'Phone number with spaces using local format with country code' => [
                'phonenumber' => '0 123 456 789',
                'expected' => '+34123456789',
                'countrycode' => '34',
            ],
            'Phone number with spaces using local format without country code' => [
                'phonenumber' => '0 123 456 789',
                'expected' => '123456789',
            ],
        ];
    }

    /**
     * Test format number with different phones and different country codes
     * @covers \factor_sms\helper::format_number
     * @dataProvider format_number_provider
     *
     * @param string $phonenumber Phone number.
     * @param string $expected Expected value.
     * @param string|null $countrycode Country code.
     */
    public function test_format_number(string $phonenumber, string $expected, ?string $countrycode = null): void {

        $this->resetAfterTest(true);

        set_config('countrycode', $countrycode, 'factor_sms');

        $this->assertEquals($expected, \factor_sms\helper::format_number($phonenumber));
    }

    /**
     * Data provider for test_is_valid__phonenumber().
     *
     * @return array with different phone numebr tests
     */
    public function is_valid_phonenumber_provider(): array {
        return [
            ['+919367788755', true],
            ['8989829304', false],
            ['+16308520397', true],
            ['786-307-3615', false],
            ['+14155552671', true],
            ['+551155256325', true],
            ['649709233', false],
            ['+34649709233', true],
            ['+aaasss', false],
        ];
    }

    /**
     * Test is valid phone number in E.164 format (https://en.wikipedia.org/wiki/E.164)
     * @covers \factor_sms\helper::is_valid_phonenumber
     * @dataProvider is_valid_phonenumber_provider
     *
     * @param string $phonenumber
     * @param bool $valid True if the given phone number is valid, false if is invalid
     */
    public function test_is_valid_phonenumber(string $phonenumber, bool $valid): void {
        $this->resetAfterTest(true);
        if ($valid) {
            $this->assertTrue(\factor_sms\helper::is_valid_phonenumber($phonenumber));
        } else {
            $this->assertFalse(\factor_sms\helper::is_valid_phonenumber($phonenumber));
        }
    }

    /**
     * Test set up user factor and verification code with a random phone number
     * @covers ::setup_user_factor
     * @covers ::check_verification_code
     * @covers ::revoke_user_factor
     */
    public function test_check_verification_code(): void {
        global $SESSION;

        $this->resetAfterTest(true);

        // Create and login a user and set up the phone number.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Generate a fake phone number and save it in session.
        $phonenumber = '+34' . (string)random_int(100000000, 999999999);
        $SESSION->tool_mfa_sms_number = $phonenumber;

        $smsfactor = \tool_mfa\plugininfo\factor::get_factor('sms');
        $rc = new \ReflectionClass($smsfactor::class);

        $smsdata = [];
        $factorinstance = $smsfactor->setup_user_factor((object) $smsdata);

        // Check if user factor was created successful.
        $this->assertNotEmpty($factorinstance);
        $this->assertEquals(1, count($smsfactor->get_active_user_factors($user)));

        // Create the secret code.
        $secretmanager = new \tool_mfa\local\secret_manager('sms');
        $secretcode = $secretmanager->create_secret(1800, true);

        // Check verification code.
        $rcm = $rc->getMethod('check_verification_code');
        $this->assertTrue($rcm->invoke($smsfactor, $secretcode));

        // Test that calling the revoke on the generic type revokes all.
        $smsfactor->revoke_user_factor($factorinstance->id);
        $this->assertEquals(0, count($smsfactor->get_active_user_factors($user)));

        unset($SESSION->tool_mfa_sms_number);
    }
}
