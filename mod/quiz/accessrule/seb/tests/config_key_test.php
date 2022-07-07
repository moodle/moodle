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
 * PHPUnit Tests for config_key class.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use quizaccess_seb\config_key;

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit Tests for config_key class.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_seb_config_key_testcase extends advanced_testcase {

    /**
     * Test that trying to generate the hash key with bad xml will result in an error.
     */
    public function test_config_key_not_generated_with_bad_xml() {
        $this->expectException(invalid_parameter_exception::class);
        $this->expectExceptionMessage("Invalid a PList XML string, representing SEB config");
        config_key::generate("<?xml This is some bad xml for sure.");
    }

    /**
     * Test that a config key is generated with empty configuration. SEB would be using defaults for all settings.
     */
    public function test_config_key_hash_generated_with_empty_string() {
        $hash = config_key::generate('')->get_hash();
        $this->assertEquals('4f53cda18c2baa0c0354bb5f9a3ecbe5ed12ab4d8e11ba873c2f11161202b945', $hash);
    }

    /**
     * Check that the Config Key hash is not altered if the originatorVersion is present in the XML or not.
     */
    public function test_presence_of_originator_version_does_not_effect_hash() {
        $xmlwithoriginatorversion = file_get_contents(__DIR__ . '/fixtures/simpleunencrypted.seb');
        $xmlwithoutoriginatorversion = file_get_contents(__DIR__ . '/fixtures/simpleunencryptedwithoutoriginator.seb');
        $hashwithorigver = config_key::generate($xmlwithoriginatorversion)->get_hash();
        $hashwithoutorigver = config_key::generate($xmlwithoutoriginatorversion)->get_hash();
        $this->assertEquals($hashwithorigver, $hashwithoutorigver);
    }

    /**
     * Provide a seb file, the expected Config Key and a password if encrypted.
     *
     * @return array
     */
    public function real_ck_hash_provider() : array {
        return [
            'unencrypted_mac2.1.4' => ['unencrypted_mac_001.seb',
                    '4fa9af8ec8759eb7c680752ef4ee5eaf1a860628608fccae2715d519849f9292', ''],
            'unencrypted_win2.2.3' => ['unencrypted_win_223.seb',
                    'fc6f4ea5922717760f4d6d536c23b8d19bf20b52aa97940f5427a76e20f49026', ''],
        ];
    }
}
