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
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts\user_preferences;

use basic_testcase;
use mod_adaptivequiz\local\report\users_attempts\filter\filter_options;

/**
 * @covers \mod_adaptivequiz\local\report\users_attempts\user_preferences\filter_user_preferences
 */
class filter_user_preferences_test extends basic_testcase {

    public function test_it_acquires_correct_default_values_when_unexpected_parameters_provided(): void {
        $filter = filter_user_preferences::from_array([]);

        $this->assertEquals(filter_options::users_option_default(), $filter->users());
        $this->assertEquals(filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT,
            $filter->include_inactive_enrolments());

        $filter = filter_user_preferences::from_array(['users' => 42, 'includeinactiveenrolments' => 5]);

        $this->assertEquals(filter_options::users_option_default(), $filter->users());
        $this->assertEquals(filter_options::INCLUDE_INACTIVE_ENROLMENTS_DEFAULT,
            $filter->include_inactive_enrolments());
    }

    public function test_it_can_be_converted_to_array(): void {
        $filterasarray = ['users' => filter_options::users_option_default(), 'includeinactiveenrolments' => 1];
        $filter = filter_user_preferences::from_array($filterasarray);

        $this->assertEquals($filterasarray, $filter->as_array());
    }
}
