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
# Tests for cover image uploading.
#
# @package    theme_snap
# @copyright  Copyright (c) 2016 Blackboard Ltd.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap, ajax failures due to log outs / expired sessions are reported correctly
  as session issues.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher  | Test      | teacher  | teacher@example.com  |
    And the following config values are set as admin:
      | advancedfeedsenable | 0 | theme_snap |

  @javascript
  Scenario: Teacher get's login status warning when trying to manage sections if logged out.
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
      | Course 1 | C1        | 0        | topics |      1       |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    # Test logout msg when changing section visibility
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Section 2"
    Then "#section-2" "css_element" should exist
    And I log out via a separate window
    When I click on "#section-2 .snap-visibility.snap-hide" "css_element"
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when highlighting section
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Section 2"
    Then "#section-2" "css_element" should exist
    And I log out via a separate window
    And I highlight section 2
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when moving section
    And I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Section 2"
    And I follow "Move \"Section 2\""
    Then I should see "Moving \"Untitled Section\"" in the "#snap-footer-alert" "css_element"
    And I follow "Section 4"
    And I log out via a separate window
    When I follow "Place section \"Untitled Section\" before section \"Section 4\""
    Then I should see "You are logged out"

  @javascript
  Scenario: Teacher get's login status warning when trying to manage assets if logged out.
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
      | Course 1 | C1        | 0        | topics |      1       |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    # Test logout msg when changing asset visibility
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Section 1"
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I log out via a separate window
    And I click on ".snap-activity[data-type='Assignment'] a.js_snap_hide" "css_element"
    And I wait until the page is ready
    # Test logout msg when attempting to duplicate asset
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    When I follow "Section 1"
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I log out via a separate window
    And I wait until the page is ready
    When I click on ".snap-activity[data-type='Assignment'] a.js_snap_duplicate" "css_element"
    Then "body#page-login-index" "css_element" should exist
    # Test logout msg when attempting to move asset
    Given I log in as "teacher"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And I click on ".snap-activity.modtype_assign .snap-edit-asset-more" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-asset-move" "css_element"
    Then I should see "Moving \"Test assignment\""
    And I log out via a separate window
    When I click on "li#section-1 li.snap-drop.asset-drop div.asset-wrapper" "css_element"
    Then I should see "You are logged out"
