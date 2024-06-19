<?php
// This file is part of Moodle - https://moodle.org/
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

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_fullname.php');
require_once(__DIR__ . '/fixtures/test_target_shortname.php');

/**
 * Unit tests for the analytics stats.
 *
 * @package     core_analytics
 * @category    test
 * @copyright 2019 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stats_test extends \advanced_testcase {

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();

        $this->setAdminUser();
    }

    /**
     * Test the {@link \core_analytics\stats::enabled_models()} implementation.
     */
    public function test_enabled_models(): void {

        $this->resetAfterTest(true);

        // By default, sites have {@link \core_course\analytics\target\no_teaching} and
        // {@link \core_user\analytics\target\upcoming_activities_due} enabled.
        $this->assertEquals(4, \core_analytics\stats::enabled_models());

        $model = \core_analytics\model::create(
            \core_analytics\manager::get_target('\core_course\analytics\target\course_dropout'),
            [
                \core_analytics\manager::get_indicator('\core\analytics\indicator\any_write_action'),
            ]
        );

        // Purely adding a new model does not make it included in the stats.
        $this->assertEquals(4, \core_analytics\stats::enabled_models());

        // New models must be enabled to have them counted.
        $model->enable('\core\analytics\time_splitting\quarters');
        $this->assertEquals(5, \core_analytics\stats::enabled_models());
    }

    /**
     * Test the {@link \core_analytics\stats::predictions()} implementation.
     */
    public function test_predictions(): void {

        $this->resetAfterTest(true);

        $model = \core_analytics\model::create(
            \core_analytics\manager::get_target('test_target_shortname'),
            [
                \core_analytics\manager::get_indicator('test_indicator_fullname'),
            ]
        );

        $model->enable('\core\analytics\time_splitting\no_splitting');

        // Train the model.
        $this->getDataGenerator()->create_course(['shortname' => 'a', 'fullname' => 'a', 'visible' => 1]);
        $this->getDataGenerator()->create_course(['shortname' => 'b', 'fullname' => 'b', 'visible' => 1]);
        $model->train();

        // No predictions yet.
        $this->assertEquals(0, \core_analytics\stats::predictions());

        // Get one new prediction.
        $this->getDataGenerator()->create_course(['shortname' => 'aa', 'fullname' => 'aa', 'visible' => 0]);
        $result = $model->predict();

        $this->assertEquals(1, count($result->predictions));
        $this->assertEquals(1, \core_analytics\stats::predictions());

        // Nothing changes if there is no new prediction.
        $result = $model->predict();
        $this->assertFalse(isset($result->predictions));
        $this->assertEquals(1, \core_analytics\stats::predictions());

        // Get two more predictions, we have three in total now.
        $this->getDataGenerator()->create_course(['shortname' => 'bb', 'fullname' => 'bb', 'visible' => 0]);
        $this->getDataGenerator()->create_course(['shortname' => 'cc', 'fullname' => 'cc', 'visible' => 0]);

        $result = $model->predict();
        $this->assertEquals(2, count($result->predictions));
        $this->assertEquals(3, \core_analytics\stats::predictions());
    }

    /**
     * Test the {@link \core_analytics\stats::actions()} and {@link \core_analytics\stats::actions_not_useful()} implementation.
     */
    public function test_actions(): void {
        global $DB;
        $this->resetAfterTest(true);

        $model = \core_analytics\model::create(
            \core_analytics\manager::get_target('test_target_shortname'),
            [
                \core_analytics\manager::get_indicator('test_indicator_fullname'),
            ]
        );

        $model->enable('\core\analytics\time_splitting\no_splitting');

        // Train the model.
        $this->getDataGenerator()->create_course(['shortname' => 'a', 'fullname' => 'a', 'visible' => 1]);
        $this->getDataGenerator()->create_course(['shortname' => 'b', 'fullname' => 'b', 'visible' => 1]);
        $model->train();

        // Generate two predictions.
        $this->getDataGenerator()->create_course(['shortname' => 'aa', 'fullname' => 'aa', 'visible' => 0]);
        $this->getDataGenerator()->create_course(['shortname' => 'bb', 'fullname' => 'bb', 'visible' => 0]);
        $model->predict();

        list($p1, $p2) = array_values($DB->get_records('analytics_predictions'));

        $p1 = new \core_analytics\prediction($p1, []);
        $p2 = new \core_analytics\prediction($p2, []);

        // No actions executed at the start.
        $this->assertEquals(0, \core_analytics\stats::actions());
        $this->assertEquals(0, \core_analytics\stats::actions_not_useful());

        // The user has acknowledged the first prediction.
        $p1->action_executed(\core_analytics\prediction::ACTION_FIXED, $model->get_target());
        $this->assertEquals(1, \core_analytics\stats::actions());
        $this->assertEquals(0, \core_analytics\stats::actions_not_useful());

        // The user has marked the other prediction as not useful.
        $p2->action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $model->get_target());
        $this->assertEquals(2, \core_analytics\stats::actions());
        $this->assertEquals(1, \core_analytics\stats::actions_not_useful());
    }
}
