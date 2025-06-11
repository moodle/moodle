# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests for single section to be overriden with normal behaviour in Snap.
#
# @package    theme_snap
# @copyright  Copyright (c) 2015 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap, courses in single section per page mode are forced to operate with all
  sections displayed at the same time.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | coursedisplay |
      | Course 1 | C1        | 0        | topics | 1             |
    # In the above, coursedisplay 1 means that the course will display single section at a time.
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | student1 | C1     | student        |

  @javascript
  Scenario: All users see course pages rendered in regular mode even when course single section per page mode set.

    # Test with admin user.
    Given I log in as "admin"
    Then I can see course "C1" in all sections mode
    And I log out

    # Test with editing teacher.
    And I log in as "teacher1"
    Then I can see course "C1" in all sections mode
    And I log out

    # Test widh non editing teacher.
    And I log in as "teacher2"
    Then I can see course "C1" in all sections mode
    And I log out

    # Test with student.
    And I log in as "student1"
    Then I can see course "C1" in all sections mode
    And I log out
