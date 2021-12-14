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
 * Tool analytics external functions tests.
 *
 * @package    tool_analytics
 * @category   external
 * @copyright  2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */

namespace tool_analytics\external;

use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/analytics/tests/fixtures/test_indicator_max.php');
require_once($CFG->dirroot . '/analytics/tests/fixtures/test_target_course_level_shortname.php');

/**
 * Tool analytics external functions tests
 *
 * @package    tool_analytics
 * @category   external
 * @copyright  2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.8
 */
class external_test extends externallib_advanced_testcase {

    /**
     * test_potential_contexts description
     */
    public function test_potential_contexts() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Include the all context levels so the misc. category get included.
        $this->assertCount(1, \tool_analytics\external::potential_contexts());

        // The frontpage is not included.
        $this->assertCount(0, \tool_analytics\external::potential_contexts('PHPUnit'));

        $target = \core_analytics\manager::get_target('test_target_course_level_shortname');
        $indicators = ['test_indicator_max' => \core_analytics\manager::get_indicator('test_indicator_max')];
        $model = \core_analytics\model::create($target, $indicators);

        $this->assertCount(1, \tool_analytics\external::potential_contexts(null, $model->get_id()));
    }

    /**
     * test_potential_contexts description
     */
    public function test_potential_contexts_no_manager() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(\required_capability_exception::class);
        $this->assertCount(2, \tool_analytics\external::potential_contexts());
    }
}
