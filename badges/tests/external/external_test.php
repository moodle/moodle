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
 * Badges external functions tests.
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

namespace core_badges\external;

use core_badges_external;
use core_badges\tests\external_helper;
use core_external\external_api;
use core_external\external_settings;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Badges external functions tests
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
final class external_test extends externallib_advanced_testcase {
    use external_helper;

    /**
     * Test get user badges.
     * These is a basic test since the badges_get_my_user_badges used by the external function already has unit tests.
     *
     * @covers \core_badges_external::get_user_badges
     */
    public function test_get_my_user_badges(): void {
        $data = $this->prepare_test_data();

        $this->setUser($data['student']);
        $result = core_badges_external::get_user_badges();
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(2, $result['badges']);
        $this->assert_issued_badge($data['coursebadge'], $result['badges'][0], true, false);
        $this->assert_issued_badge($data['sitebadge'], $result['badges'][1], true, false);

        // Pagination and filtering.
        $result = core_badges_external::get_user_badges(0, 0, 0, 1, '', true);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(1, $result['badges']);
        $this->assert_issued_badge($data['coursebadge'], $result['badges'][0], true, false);
    }

    /**
     * Test get user badges.
     *
     * @covers \core_badges_external::get_user_badges
     */
    public function test_get_other_user_badges(): void {
        $data = $this->prepare_test_data();

        // User with "moodle/badges:configuredetails" capability.
        $this->setAdminUser();
        $result = core_badges_external::get_user_badges($data['student']->id);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(2, $result['badges']);
        $this->assert_issued_badge($data['coursebadge'], $result['badges'][0], false, true);
        $this->assert_issued_badge($data['sitebadge'], $result['badges'][1], false, true);

        // User without "moodle/badges:configuredetails" capability.
        $this->setUser($this->getDataGenerator()->create_user());
        $result = core_badges_external::get_user_badges($data['student']->id);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);
        $this->assertCount(2, $result['badges']);
        $this->assert_issued_badge($data['coursebadge'], $result['badges'][0], false, false);
        $this->assert_issued_badge($data['sitebadge'], $result['badges'][1], false, false);
    }

    /**
     * Test get_user_badges where issuername contains text to be filtered
     *
     * @covers \core_badges_external::get_user_badges
     */
    public function test_get_user_badges_filter_issuername(): void {
        global $DB;

        $data = $this->prepare_test_data();

        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        external_settings::get_instance()->set_filter(true);

        // Update issuer name of test badge.
        $issuername = '<span class="multilang" lang="en">Issuer (en)</span><span class="multilang" lang="es">Issuer (es)</span>';
        $DB->set_field('badge', 'issuername', $issuername, ['name' => 'Test badge site']);

        // Retrieve student badges.
        $result = core_badges_external::get_user_badges($data['student']->id);
        $result = external_api::clean_returnvalue(core_badges_external::get_user_badges_returns(), $result);

        // Site badge will be last, because it has the earlier issued date.
        $badge = end($result['badges']);
        $this->assertEquals('Issuer (en)', $badge['issuername']);
    }
}
