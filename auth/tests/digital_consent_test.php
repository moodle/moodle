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

namespace core_auth;

/**
 * Digital consent helper testcase.
 *
 * @package    core_auth
 * @copyright  2018 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class digital_consent_test extends \advanced_testcase {

    public function test_is_age_digital_consent_verification_enabled(): void {
        global $CFG;
        $this->resetAfterTest();

        // Age of digital consent verification is enabled.
        $CFG->agedigitalconsentverification = 0;

        $isenabled = \core_auth\digital_consent::is_age_digital_consent_verification_enabled();
        $this->assertFalse($isenabled);
    }

    public function test_is_minor(): void {
        global $CFG;
        $this->resetAfterTest();

        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, 14',
            'CZ, 13',
            'DE, 14',
            'DK, 13',
        ]);
        $CFG->agedigitalconsentmap = $agedigitalconsentmap;

        $usercountry1 = 'DK';
        $usercountry2 = 'AU';
        $userage1 = 12;
        $userage2 = 14;
        $userage3 = 16;

        // Test country exists in agedigitalconsentmap and user age is below the particular digital minor age.
        $isminor = \core_auth\digital_consent::is_minor($userage1, $usercountry1);
        $this->assertTrue($isminor);
        // Test country exists in agedigitalconsentmap and user age is above the particular digital minor age.
        $isminor = \core_auth\digital_consent::is_minor($userage2, $usercountry1);
        $this->assertFalse($isminor);
        // Test country does not exists in agedigitalconsentmap and user age is below the particular digital minor age.
        $isminor = \core_auth\digital_consent::is_minor($userage2, $usercountry2);
        $this->assertTrue($isminor);
        // Test country does not exists in agedigitalconsentmap and user age is above the particular digital minor age.
        $isminor = \core_auth\digital_consent::is_minor($userage3, $usercountry2);
        $this->assertFalse($isminor);
    }

    public function test_parse_age_digital_consent_map_valid_format(): void {

        // Value of agedigitalconsentmap has a valid format.
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, 14',
            'BE, 13'
        ]);

        $ageconsentmapparsed = \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);

        $this->assertEquals([
            '*' => 16,
            'AT' => 14,
            'BE' => 13
        ], $ageconsentmapparsed
        );
    }

    public function test_parse_age_digital_consent_map_invalid_format_missing_spaces(): void {

        // Value of agedigitalconsentmap has an invalid format (missing space separator between values).
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT14',
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvalidcomma', 'error', 'AT14'));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }

    public function test_parse_age_digital_consent_map_invalid_format_missing_default_value(): void {

        // Value of agedigitalconsentmap has an invalid format (missing default value).
        $agedigitalconsentmap = implode(PHP_EOL, [
            'BE, 16',
            'AT, 14'
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvaliddefault', 'error'));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }

    public function test_parse_age_digital_consent_map_invalid_format_invalid_country(): void {

        // Value of agedigitalconsentmap has an invalid format (invalid value for country).
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'TEST, 14'
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvalidcountry', 'error', 'TEST'));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }

    public function test_parse_age_digital_consent_map_invalid_format_invalid_age_string(): void {

        // Value of agedigitalconsentmap has an invalid format (string value for age).
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, ten'
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvalidage', 'error', 'ten'));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }

    public function test_parse_age_digital_consent_map_invalid_format_missing_age(): void {

        // Value of agedigitalconsentmap has an invalid format (missing value for age).
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            'AT, '
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvalidage', 'error', ''));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }

    public function test_parse_age_digital_consent_map_invalid_format_missing_country(): void {

        // Value of agedigitalconsentmap has an invalid format (missing value for country).
        $agedigitalconsentmap = implode(PHP_EOL, [
            '*, 16',
            ', 12'
        ]);

        $this->expectException('moodle_exception');
        $this->expectExceptionMessage(get_string('agedigitalconsentmapinvalidcountry', 'error', ''));

        \core_auth\digital_consent::parse_age_digital_consent_map($agedigitalconsentmap);
    }
}
