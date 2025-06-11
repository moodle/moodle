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
use block_quickmail\notifier\models\event\course_entered_model;

class block_quickmail_event_notification_course_entered_model_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses,
        sets_up_notifications,
        sets_up_notification_models;

    public function test_notification_model_helper_supports_model() {
        // Model key is available.
        $types = notification_model_helper::get_available_model_keys_by_type('event');
        $this->assertContains('course_entered', $types);

        // Gets short model class name from key.
        $shortmodelclassname = notification_model_helper::get_model_class_name('course_entered');
        $this->assertEquals('course_entered_model', $shortmodelclassname);

        // Gets full model class name from type and key.
        $fullmodelclassname = notification_model_helper::get_full_model_class_name('event', 'course_entered');
        $this->assertEquals(course_entered_model::class, $fullmodelclassname);

        // Gets object type from type and key.
        $type = notification_model_helper::get_object_type_for_model('event', 'course_entered');
        $this->assertEquals('course', $type);

        // Reports if object is required.
        $result = notification_model_helper::model_requires_object('event', 'course_entered');
        $this->assertFalse($result);

        // Reports if conditions are required.
        $result = notification_model_helper::model_requires_conditions('event', 'course_entered');
        $this->assertFalse($result);

        // Test gets available condition keys.
        $keys = notification_model_helper::get_condition_keys_for_model('event', 'course_entered');
        $this->assertIsArray($keys);
        $this->assertCount(0, $keys);

        // Test gets required condition keys.
        $conditionkeys = notification::get_required_conditions_for_type('event', 'course-entered');
        $this->assertCount(0, $conditionkeys);
    }

}
