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
# Tests for course resource and activity editing features.
#
# @package    theme_snap
# @copyright  2015 Guy Thomas
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_course_asset
Feature: When the moodle theme is set to Snap, teachers edit assets without entering edit mode.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | showcompletionconditions | enablecompletion | initsections |
      | Course 1 | C1        | 0        | topics | 1                        | 1                |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher2  | 1        | teacher2@example.com |
      | student1 | Student   | 1        | student1@example.com |

    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | student1 | C1     | student        |

  @javascript
  Scenario Outline: Student cannot access edit actions.
    Given the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "student1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then ".snap-activity[data-type='Assignment']" "css_element" should exist
    And "div.dropdown snap-edit-more-dropdown" "css_element" should not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, non-editing teacher can see teacher's actions.
    Given the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher2"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And ".snap-activity[data-type='Assignment']" "css_element" should exist
    And "div.dropdown snap-edit-more-dropdown" "css_element" should not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher hides then shows activity.
    Given the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And ".snap-activity[data-type='Assignment']" "css_element" should exist
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-activity[data-type='Assignment'] a.js_snap_hide" "css_element"
    Then I wait until ".snap-activity[data-type='Assignment'].draft" "css_element" exists
    And I click on ".snap-activity.assign .snap-asset-actions" "css_element"
    And I click on ".snap-activity[data-type='Assignment']  a.js_snap_show" "css_element"
    Then I wait until ".snap-activity[data-type='Assignment'].draft" "css_element" does not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher hides then shows resource.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    And I upload file "test_text_file.txt" to section 1
    Then ".snap-resource[data-type='txt']" "css_element" should exist
    And ".snap-resource[data-type='txt'].draft" "css_element" should not exist
    And I click on ".snap-resource[data-type='txt'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-resource[data-type='txt'] a.js_snap_hide" "css_element"
    Then I wait until ".snap-resource[data-type='txt'].draft" "css_element" exists
    # This is to test that the change persists.
    And I reload the page
    And ".snap-resource[data-type='txt'].draft" "css_element" should exist
    And I click on ".snap-resource[data-type='txt'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-resource[data-type='txt'] a.js_snap_show" "css_element"
    Then I wait until ".snap-resource[data-type='txt'].draft" "css_element" does not exist
    # This is to test that the change persists.
    And I reload the page
    And ".snap-resource[data-type='txt'].draft" "css_element" should not exist
    Then I wait until ".snap-activity[data-type='Assignment'].draft" "css_element" does not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher duplicates activity.
    Given the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1                                   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And ".snap-activity[data-type='Assignment']" "css_element" should exist
    And ".snap-activity[data-type='Assignment'] + .snap-activity[data-type='Assignment']" "css_element" should not exist
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-activity[data-type='Assignment'] a.js_snap_duplicate" "css_element"
    Then I wait until ".snap-activity[data-type='Assignment'] + .snap-activity[data-type='Assignment']" "css_element" exists
    # This is to test that the duplication persists.
    And I reload the page
    And ".snap-activity[data-type='Assignment'] + .snap-activity[data-type='Assignment']" "css_element" should exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: In read mode, teacher duplicates resource.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    When I upload file "test_text_file.txt" to section 1
    Then ".snap-resource[data-type='txt']" "css_element" should exist
    And ".snap-resource[data-type='txt'] + .snap-resource[data-type='txt']" "css_element" should not exist
    And I click on ".snap-resource[data-type='txt'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-resource[data-type='txt'] a.js_snap_duplicate" "css_element"
    Then I wait until ".snap-resource[data-type='txt'] + .snap-resource[data-type='txt']" "css_element" exists
        # This is to test that the duplication persists.
    And I reload the page
    Then ".snap-resource[data-type='txt'] + .snap-resource[data-type='txt']" "css_element" should exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario: In read mode, teacher can copy activity to sharing cart.
    Given the following "activities" exist:
      | activity | course | idnumber | name            | intro           | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment | Test assignment | 1       | 1|
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | sharing_cart      | Course       | C1        | course-view-*   | side-pre      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    Then I should see "Copy to Sharing Cart"
    And I click on ".snap-activity[data-type='Assignment'] a.editing_backup" "css_element"
    Then I should see "Are you sure you want to copy this"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element" should not exist
    Then I should not see "Copy to Sharing Cart"

  @javascript
  Scenario: In the frontpage, an admin duplicates an activity.
    Given the following "activities" exist:
      | activity | course               | section | name        | intro                  | idnumber |
      | assign   | Acceptance test site | 1       | Assignment1 | Assignment description | assign1  |
    Then I log in as "admin"
    And I am on site homepage
    When I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-activity[data-type='Assignment'] a.js_snap_duplicate" "css_element"
    Then I wait until ".snap-activity[data-type='Assignment'] + .snap-activity[data-type='Assignment']" "css_element" exists

  @javascript
  Scenario: In the frontpage, an admin can edit completions conditions
    Given the following "activities" exist:
      | activity | name              | course | idnumber | gradepass | completion | completionusegrade |
      | quiz     | Activity sample 1 | C1     | quiz1    | 5.00      | 2          | 1                  |
    When I am on the "C1" "Course" page logged in as "admin"
    And I click on "More Options" "button"
    Then I click on "Edit conditions Activity sample 1" "button"
    And ".snap-form-required > fieldset" "css_element" should not be visible
    But ".snap-form-advanced > fieldset#id_activitycompletionheader" "css_element" should be visible

  @javascript
  Scenario: In the frontpage, an admin should not see the Edit conditions option if Enable completion is disabled in the site
    Given I disable site completion tracking
    And the following "activities" exist:
      | activity | name              | course | idnumber | gradepass | completion | completionusegrade |
      | quiz     | Activity sample 1 | C1     | quiz1    | 5.00      | 2          | 1                  |
    When I am on the "C1" "Course" page logged in as "admin"
    And I click on "More Options" "button"
    Then I should not see "Edit conditions" in the "#snap-asset-menu" "css_element"
