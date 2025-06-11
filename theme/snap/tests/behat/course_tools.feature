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
# Tests for availability of course tools section.
#
# @package   theme_snap
# @copyright Copyright (c) 2016 Open LMS
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, a course tools section is available.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student2  | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           | enrol  |
      | student1 | C1     | student        | manual |
      | teacher1 | C1     | editingteacher | manual |

  @javascript
  Scenario: Course tools link does not show for unsupported formats.
    Given the course format for "C1" is set to "social"
    When I log in as "student1"
    And I am on the course main page for "C1"
    Then "a[href=\"#coursetools\"]" "css_element" should not exist

  @javascript
  Scenario Outline: Course tools link functions for supported formats.
    Given the course format for "C1" is set to "<format>"
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability            | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "#snap-course-tools" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#snap-student-dashboard" "css_element" should exist
    And ".snap-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".snap-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | format | completionenabled | gradebookaccessible | seecompletion | seegrade   |
      | topics | Enabled           | Allow               | should        | should     |
      | topics | Disabled          | Prohibit            | should not    | should not |
      | topics | Enabled           | Prohibit            | should        | should not |
      | topics | Disabled          | Allow               | should not    | should     |
      | weeks  | Enabled           | Allow               | should        | should     |
      | weeks  | Disabled          | Prohibit            | should not    | should not |
      | weeks  | Enabled           | Prohibit            | should        | should not |
      | weeks  | Disabled          | Allow               | should not    | should     |
      | tiles  | Enabled           | Allow               | should        | should     |
      | tiles  | Disabled          | Prohibit            | should not    | should not |
      | tiles  | Enabled           | Prohibit            | should        | should not |
      | tiles  | Disabled          | Allow               | should not    | should     |

  @javascript
  Scenario Outline: Course tools show automatically for single activity format.
    Given the course format for "C1" is set to "singleactivity" with the following settings:
      | name      | activitytype |
      | value     | forum        |
    And the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section |
      | forum    | C1     | forum1   | Test forum      | Test forum      | 1       |
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    # Note we have to call this step twice because for some reason it doesn't automatically go to the module page the
    # first time - that's a core issue though.
    And I am on the course main page for "C1"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#snap-student-dashboard" "css_element" should exist
    And ".snap-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".snap-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | completionenabled | gradebookaccessible | seecompletion | seegrade   |
      | Enabled           | Allow               | should        | should     |
      | Disabled          | Prohibit            | should not    | should not |

  @javascript
  Scenario Outline: Course tools show automatically for single activity format set to hsuforum of types general / single.
    Given I am using Open LMS
    And the course format for "C1" is set to "singleactivity" with the following settings:
      | name      | activitytype |
      | value     | hsuforum        |
    And the following "activities" exist:
      | activity    | course | idnumber | name          | intro           | section | type   |
      | hsuforum    | C1     | forum1   | Test hsuforum | Test hsuforum   | 1       | <type> |
    And completion tracking is "<completionenabled>" for course "C1"
    And I set the following system permissions of "Student" role:
      | capability                | permission            |
      | gradereport/overview:view | <gradebookaccessible> |
    When I log in as "student1"
    And I am on the course main page for "C1"
    # Note we have to call this step twice because for some reason it doesn't automatically go to the module page the
    # first time - that's a core issue though.
    And I am on the course main page for "C1"
    Then I should not see "<formatdisplay>" in the ".format-singleactivity .page-header-headings > h1" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And "#snap-student-dashboard" "css_element" should exist
    And ".snap-student-dashboard-progress" "css_element" <seecompletion> exist
    And ".snap-student-dashboard-grade" "css_element" <seegrade> exist
    Examples:
      | type    | completionenabled | gradebookaccessible | seecompletion | seegrade   | formatdisplay |
      | general | Enabled           | Allow               | should        | should     | Course 1      |
      | general | Disabled          | Prohibit            | should not    | should not | Course 1      |
      | single  | Enabled           | Allow               | should        | should     | Course 1      |
      | single  | Disabled          | Prohibit            | should not    | should not | Course 1      |

  @javascript
  Scenario: Course tools includes ally course lti report.
    Given I am using Open LMS
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And "#coursetools a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should exist
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    And "#coursetools a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should not exist

  @javascript
  Scenario: Course tools show enrol and unenrol links.
    Given I log in as "admin"
    And I am on the course main page for "C1"
    When I add "Self enrolment" enrolment method in "C1" with:
      | Custom instance name | Test student enrolment |
      | Enrolment key        | moodle_rules           |
    And I log out
    # Self enrol using student2.
    And I log in as "student2"
    And I am on the course main page for "C1"
    And I set the following fields to these values:
      | Enrolment key | moodle_rules |
    And I press "Enrol me"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    Then I should see "Unenrol me"
    And I log out
    # Manual enrolled student1, should not be able to unenrol.
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    Then I should not see "Unenrol me"
    And I log out
    # Add cap to student1 so they can unenrol.
    And I log in as "admin"
    And I set the following system permissions of "Student" role:
      | capability               | permission |
      | enrol/manual:unenrolself | Allow      |
    And I log out
    # Manual enrol student, should NOW be able to unenrol.
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    Then I should see "Unenrol me"
    And I log out
    # Admins can navigate to course tools and enrol themselves.
    And I log in as "admin"
    And I am on the course main page for "C1"
    And I click on "a[href=\"#coursetools\"]" "css_element"
    Then I should see "Enrol me"

  @javascript
  Scenario: Course Dashboard should be visible after clicking in the Course Dashboard link after clicking in a course section.
    Given the course format for "C1" is set to "topics"
    When I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "#snap-course-tools" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And I click on "a[href=\"#section-1\"].chapter-title" "css_element"
    And I click on "#snap-course-tools" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"

  @javascript
  Scenario: User should be redirected to the same section in the course in Snap when changing the edit mode.
    Given the course format for "C1" is set to "topics"
    When I log in as "admin"
    And I am on the course main page for "C1"
    And I click on "#snap-course-tools" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And I switch edit mode in Snap
    And I wait until the page is ready
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And I click on "a[href='#section-1'].chapter-title" "css_element"
    And I switch edit mode in Snap
    And I wait until the page is ready
    And I should not see "Course Dashboard" in the "#coursetools" "css_element"
    Then the course format for "C1" is set to "weeks"
    When I log in as "admin"
    And I am on the course main page for "C1"
    And I click on "#snap-course-tools" "css_element"
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And I switch edit mode in Snap
    And I wait until the page is ready
    Then I should see "Course Dashboard" in the "#coursetools" "css_element"
    And I click on "a[href='#section-1'].chapter-title" "css_element"
    And I switch edit mode in Snap
    And I wait until the page is ready
    Then I should not see "Course Dashboard" in the "#coursetools" "css_element"