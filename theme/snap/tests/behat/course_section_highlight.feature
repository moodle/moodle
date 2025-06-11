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
# Tests for toggle course section highlighting in non edit mode in snap.
#
# @package    theme_snap
# @copyright  2016 Guy Thomas
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, teachers can toggle the currently highlighted course sections.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsecitons |
      | Course 1 | C1        | 0        | topics |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario Outline: In read mode, teacher toggles section as Highlighted and student sees appropriate status.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    Then "#section-1" "css_element" should exist
    And "#chapters h3:nth-of-type(2) li.snap-visible-section" "css_element" should exist
    And I click on "#extra-actions-dropdown-1" "css_element"
    And I click on "#section-1 .snap-highlight" "css_element"
    And I wait until "#section-1 .snap-highlight" "css_element" exists
    And I click on "#extra-actions-dropdown-1" "css_element"
    And I should see "Unhighlight"
    And I click on "#course-toc .chapters h3:nth-of-type(3)" "css_element"
    Then "#section-2" "css_element" should exist
    And "#chapters h3:nth-of-type(3) li.snap-visible-section" "css_element" should exist
    And I click on "#extra-actions-dropdown-2" "css_element"
    And I click on "#section-2 .snap-highlight" "css_element"
    And I wait until "#section-2 .snap-highlight" "css_element" exists
    # Note: nth-of-type(3) corresponds to the second section in the TOC.
    And I should see "Highlighted" in the "#chapters h3:nth-of-type(3)" "css_element"
    And "#chapters h3:nth-of-type(3) li.snap-visible-section" "css_element" should exist
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    And I click on "#extra-actions-dropdown-1" "css_element"
    And I should see "Highlight"
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    Then I should see "Highlighted" in the "#chapters h3:nth-of-type(3)" "css_element"
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on "#course-toc .chapters h3:nth-of-type(3)" "css_element"
    And I click on "#extra-actions-dropdown-2" "css_element"
    Given I click on "#section-2 .snap-highlight" "css_element"
    And I wait until "#section-2 .snap-highlight" "css_element" exists
    Then I should not see "Highlighted" in the "#chapters h3:nth-of-type(3)" "css_element"
    And "#chapters h3:nth-of-type(3) li.snap-visible-section" "css_element" should exist
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    Then I should not see "Highlighted" in the "#chapters h3:nth-of-type(3)" "css_element"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  # This scenario is necessary to make sure the correct error message comes back when an AJAX request fails but it is
  # not related to a session time out / the user being logged out.
  Scenario: Teacher loses teacher capability whilst course open and receives the correct error message when trying to
  highlight section.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And the editing teacher role is removed from course "C1" for "teacher1"
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    Then "#section-1" "css_element" should exist
    And I click on "#extra-actions-dropdown-1" "css_element"
    And I click on "#section-1 .snap-highlight" "css_element"
    Then ".modal.show .modal-dialog" "css_element" should exist
    And I should see "Failed to highlight section" in the ".modal-dialog" "css_element"
    Then I log out
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | 1 | theme_snap |
    And I log out
    And I log in as "teacher1"
    Given I am on the course main page for "C1"
    And I click on "#course-toc .chapters h3:nth-of-type(2)" "css_element"
    Then "#section-1" "css_element" should exist
    And "#extra-actions-dropdown-1" "css_element" should not exist
    And "#section-1 .snap-highlight" "css_element" should not exist

  @javascript
  Scenario Outline: Student cannot mark section highlighted.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "#course-toc .chapters h3:nth-of-type(3)" "css_element"
    Then "#section-2 .snap-highlight" "css_element" should not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  Scenario: For Weeks Format, the "current week" should be highlighted in the course.
    Given the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | startdate |
      | Course 2 | C2        | weeks  | 0             | 5           | ##yesterday## |
    Given I log in as "admin"
    And I am on the course main page for "C2"
    And I click on "#course-toc .chapters h3:nth-of-type(2) a" "css_element"
    Then "#section-1" "css_element" should exist
    Then I should see "Current week" in the "#section-1" "css_element"
