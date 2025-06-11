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
# Basic test for Profile based branding.
#
# @package   theme_snap
# @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap, and Profile based branding is used, a body CSS class should be present.

  Background:
    Given the following config values are set as admin:
      | pbb_enable | 1 | theme_snap |
    And the following "users" exist:
      | username | firstname | lastname | email                | department     | institution       |
      | teacher1 | Teacher   | 1        | teacher1@example.com | Marketing Unit | Moodle University |

  @javascript
  Scenario: Logged in user sees body CSS class.
    Given I log in as "teacher1"
    # Default field is the department.
    Then "body.snap-pbb-marketing-unit" "css_element" should exist
    And I open my profile in edit mode
    And I press "Optional"
    And I set the field "Department" to "fAntástic   SàlEs      AREA"
    And I press "Update profile"
    Then "body.snap-pbb-fantastic-sales-area" "css_element" should exist
    And I log out
    # Changing the field to institution.
    And the following config values are set as admin:
      | pbb_field | user\|institution | theme_snap |
    And I log in as "teacher1"
    Then "body.snap-pbb-moodle-university" "css_element" should exist
