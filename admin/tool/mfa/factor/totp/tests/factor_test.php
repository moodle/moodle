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

namespace factor_totp;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../extlib/OTPHP/OTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/ParameterTrait.php');
require_once(__DIR__.'/../extlib/OTPHP/OTP.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTP.php');

require_once(__DIR__.'/../extlib/Assert/Assertion.php');
require_once(__DIR__.'/../extlib/Assert/AssertionFailedException.php');
require_once(__DIR__.'/../extlib/Assert/InvalidArgumentException.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Base32.php');

/**
 * Tests for TOTP factor.
 *
 * @covers      \factor_totp\factor
 * @package     factor_totp
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Test code validation of the TOTP factor
     */
    public function test_validate_code() {
        global $DB;

        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        // Setup test staples.
        $totp = \OTPHP\TOTP::create('fakekey');
        $window = 10;

        set_config('enabled', 1, 'factor_totp');
        $totpfactor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $factorinstance = $totpfactor->setup_user_factor((object) $totpdata);

        // First check that a valid code is actually valid.
        $code = $totp->at(time());
        // Manually set timeverified of factor.
        $DB->set_field('tool_mfa', 'lastverified', time() - WEEKSECS, ['id' => $factorinstance->id]);
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_VALID, $result);

        // Now update timeverified to 2 mins ago, and check codes within window are blocked.
        $code = $totp->at(time() - (2 * MINSECS));
        $DB->set_field('tool_mfa', 'lastverified', time() - (2 * MINSECS), ['id' => $factorinstance->id]);
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_USED, $result);

        // Now update timeverified to 2 mins ago, and check codes within window are blocked.
        $code = $totp->at(time());
        $DB->set_field('tool_mfa', 'lastverified', time() - (2 * MINSECS), ['id' => $factorinstance->id]);
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_USED, $result);

        // Now update timeverified to 2 mins ago, and check codes within window are blocked.
        $code = $totp->at(time() - (4 * MINSECS));
        $DB->set_field('tool_mfa', 'lastverified', time() - (2 * MINSECS), ['id' => $factorinstance->id]);
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_USED, $result);

        // Now check future codes.
        $window = 1;
        $code = $totp->at(time() + (2 * MINSECS));
        $DB->set_field('tool_mfa', 'lastverified', time() - WEEKSECS, ['id' => $factorinstance->id]);
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_FUTURE, $result);

        // Codes in far future are invalid.
        $code = $totp->at(time() + (20 * MINSECS));
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_INVALID, $result);

        // Do the same for past codes.
        $window = 1;
        $code = $totp->at(time() - (2 * MINSECS));
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_OLD, $result);

        // Codes in far future are invalid.
        $code = $totp->at(time() - (20 * MINSECS));
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_INVALID, $result);

        // Check incorrect codes are invalid.
        // Note code has a 1 in 30,000,000 chance of failing.
        $code = '123456';
        $result = $totpfactor->validate_code($code, $window, $totp, $factorinstance);
        $this->assertEquals($totpfactor::TOTP_INVALID, $result);
    }

    /**
     * Do not store the TOTP secret + user combination more than once
     *
     * @covers ::setup_user_factor
     */
    public function test_wont_store_same_secret_twice() {
        global $DB;
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_config('enabled', 1, 'factor_totp');
        $totpfactor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $totpfactor->setup_user_factor((object) $totpdata);

        // Trying to add the same TOTP should return null.
        $anotherecord = $totpfactor->setup_user_factor((object) $totpdata);
        $this->assertNull($anotherecord);

        // The total count for factors added should be 1 at this point.
        $count = $DB->count_records('tool_mfa');
        $this->assertEquals(1, $count);
    }
}
