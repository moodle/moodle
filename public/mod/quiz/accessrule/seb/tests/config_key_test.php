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

namespace quizaccess_seb;

/**
 * PHPUnit Tests for config_key class.
 *
 * @package   quizaccess_seb
 * @author    Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \quizaccess_seb\config_key
 */
final class config_key_test extends \advanced_testcase {

    /**
     * Test that trying to generate the hash key with bad xml will result in an error.
     */
    public function test_config_key_not_generated_with_bad_xml(): void {
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage("Invalid a PList XML string, representing SEB config");
        config_key::generate("<?xml This is some bad xml for sure.");
    }

    /**
     * Test that a config key is generated with empty configuration. SEB would be using defaults for all settings.
     */
    public function test_config_key_hash_generated_with_empty_string(): void {
        $hash = config_key::generate('')->get_hash();
        $this->assertEquals('4f53cda18c2baa0c0354bb5f9a3ecbe5ed12ab4d8e11ba873c2f11161202b945', $hash);
    }

    /**
     * Test config key hash is derived correctly by Moodle.
     *
     * @param string $config The SEB config file name.
     * @param string $hash The correct config key hash for this file.
     *
     * @dataProvider real_ck_hash_provider
     */
    public function test_config_key_hash_is_derived_correctly($config, $hash): void {
        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, $config));
        $derivedhash = config_key::generate($xml)->get_hash();
        $this->assertEquals($hash, $derivedhash);
    }

    /**
     * Check that the Config Key hash is not altered if the originatorVersion is present in the XML or not.
     */
    public function test_presence_of_originator_version_does_not_effect_hash(): void {
        $xmlwithoriginatorversion = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'simpleunencrypted.seb'));
        $xmlwithoutoriginatorversion = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'simpleunencryptedwithoutoriginator.seb'));
        $hashwithorigver = config_key::generate($xmlwithoriginatorversion)->get_hash();
        $hashwithoutorigver = config_key::generate($xmlwithoutoriginatorversion)->get_hash();
        $this->assertEquals($hashwithorigver, $hashwithoutorigver);
    }

    /**
     * Provide a seb file, the expected Config Key and a password if encrypted.
     *
     * @return array
     */
    public static function real_ck_hash_provider(): array {
        return [
            'unencrypted_mac2.1.4' => ['unencrypted_mac_001.seb',
                    '4fa9af8ec8759eb7c680752ef4ee5eaf1a860628608fccae2715d519849f9292', ''],
            'unencrypted_win2.2.3' => ['unencrypted_win_223.seb',
                    '2534e4e9f3188f9f9133bf7cf7b4c5d898292bbd7e8d0230f39d1176636a1431', ''],
        ];
    }
}
