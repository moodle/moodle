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
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @copyright  2015 Guy Thomas
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_course
Feature: When the moodle theme is set to Snap, teachers can toggle the visibility of course sections in read mode and
  edit mode.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
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
  Scenario Outline: In read mode, teacher hides section.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 2"
    Then "#section-2" "css_element" should exist
    And "#chapters h3:nth-of-type(3) li.snap-visible-section" "css_element" should exist
    And "#section-2.hidden" "css_element" should not exist
    And I click on "#section-2 .snap-visibility.snap-hide" "css_element"
    And I wait until "#section-2 .snap-visibility.snap-show" "css_element" exists
    Then "#section-2.hidden" "css_element" should exist
    And "#chapters h3:nth-of-type(3) li.snap-visible-section" "css_element" should exist
    # Make sure that the navigation either side of section 2 has the dimmed class - i.e. to reflect section 2's hidden status.
    And I follow "Section 3"
    And I follow "Section 2"
    And the previous navigation for section "3" shows as hidden
    And I follow "Section 1"
    And I follow "Section 2"
    And the next navigation for section "1" shows as hidden
    # Note, the Not published to students message is in the 3rd element of the TOC because element 1 is section 0.
    And I should see "Not published to students" in the "#chapters h3:nth-of-type(3)" "css_element"
    # Let's make the section visible again
    Given I click on "#section-2 .snap-visibility.snap-show" "css_element"
    And I wait until "#section-2 .snap-visibility.snap-hide" "css_element" exists
    Then "#section-2.hidden" "css_element" should not exist
    And I should not see "Not published to students" in the "#chapters h3:nth-of-type(3)" "css_element"
    # Make sure that the navigation either side of section 2 does not have the dimmed class - i.e. to reflect section 2's visible status.
    And the previous navigation for section "3" shows as visible
    And the next navigation for section "1" shows as visible
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher hides section and show an activity.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I click on "#section-1 .snap-visibility.snap-hide" "css_element"
    And I wait until "#section-1 .snap-visibility.snap-show" "css_element" exists
    And I reload the page
    And I add a assign activity to course "C1" section "1" and I fill the form with:
      | Assignment name | Assignment One          |
      | Description     | Submit your online text |
      | visible         | 1                       |
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I should see "Available but not shown on course page"
    And I click on "#section-1 .snap-visibility.snap-show" "css_element"
    And I wait until "#section-1 .snap-visibility.snap-hide" "css_element" exists
    Then I should not see "Available but not shown on course page"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher hides section and hide an activity.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I am on the course main page for "C1"
    And I add a assign activity to course "C1" section "1" and I fill the form with:
      | Assignment name | Assignment One          |
      | Description     | Submit your online text |
      | visible         | 0                       |
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then I should see "Not published to students"
    And I click on "#section-1 .snap-visibility.snap-hide" "css_element"
    And I wait until "#section-1 .snap-visibility.snap-show" "css_element" exists
    Then ".snap-asset.draft .snap-draft-tag" "css_element" should not be visible
    And I click on "#section-1 .snap-visibility.snap-show" "css_element"
    And I wait until "#section-1 .snap-visibility.snap-hide" "css_element" exists
    Then ".snap-asset.draft .snap-draft-tag" "css_element" should be visible
    Then I should see "Not published to students"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario: Teacher loses teacher capability whilst course open and receives the correct error message when trying to
  hide section.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And the editing teacher role is removed from course "C1" for "teacher1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And I click on "#section-1 .snap-visibility.snap-hide" "css_element"
    Then I should see "Failed to hide/show section"

  @javascript
  Scenario: Teacher loses teacher capability whilst course open and when rendering using the fragment api the edition
  button should not appear.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | 1 | theme_snap |
    And I log out
    Then I log in as "teacher1"
    And I am on the course main page for "C1"
    And the editing teacher role is removed from course "C1" for "teacher1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#section-1 .snap-visibility.snap-hide" "css_element" should not exist

  @javascript
  Scenario Outline: In read mode, student cannot hide section.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "student1"
    And I am on the course main page for "C1"
    And I follow "Section 2"
    Then "#section-2 .snap-visibility" "css_element" should not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |
