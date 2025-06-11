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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\persistents\notification;
use block_quickmail\notifier\models\notification_model_helper;
use block_quickmail\notifier\models\reminder\course_non_participation_model;

class block_quickmail_reminder_notification_course_non_participation_model_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications,
        sets_up_notification_models;

    public function test_notification_model_helper_supports_model() {
        // Model key is available.
        $types = notification_model_helper::get_available_model_keys_by_type('reminder');
        $this->assertContains('course_non_participation', $types);

        // Gets short model class name from key.
        $shortmodelclassname = notification_model_helper::get_model_class_name('course_non_participation');
        $this->assertEquals('course_non_participation_model', $shortmodelclassname);

        // Gets full model class name from type and key.
        $fullmodelclassname = notification_model_helper::get_full_model_class_name('reminder', 'course_non_participation');
        $this->assertEquals(course_non_participation_model::class, $fullmodelclassname);

        // Gets object type from type and key.
        $type = notification_model_helper::get_object_type_for_model('reminder', 'course_non_participation');
        $this->assertEquals('course', $type);

        // Reports if object is required.
        $result = notification_model_helper::model_requires_object('reminder', 'course_non_participation');
        $this->assertFalse($result);

        // Reports if conditions are required.
        $result = notification_model_helper::model_requires_conditions('reminder', 'course_non_participation');
        $this->assertTrue($result);

        // Test gets available condition keys.
        $keys = notification_model_helper::get_condition_keys_for_model('reminder', 'course_non_participation');
        $this->assertIsArray($keys);
        $this->assertCount(2, $keys);

        // Test gets required condition keys.
        $conditionkeys = notification::get_required_conditions_for_type('reminder', 'course-non-participation');
        $this->assertCount(2, $conditionkeys);
        $this->assertContains('time_amount', $conditionkeys);
        $this->assertContains('time_unit', $conditionkeys);
    }

}
