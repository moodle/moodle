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
# along with Moodle. If not, see <http://www.gnu.org/licenses/>.
#
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @autor      Oscar Nadjar
# @copyright  Copyright (c) 2020 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
Feature: When the Moodle theme is set to Snap, on moving assets some links shouldn't be reached.

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Add custom content | 1 |
    And I log out
    And the following "courses" exist:
      | fullname | shortname | category | format | enablecompletion | initsections |
      | Course 1 | C1        | 0        | topics | 1                |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course               | idnumber | name             | intro                         | section | assignsubmission_onlinetext_enabled | completion | completionview |
      | assign   | C1                   | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   | 1          | 0              |
      | assign   | C1                   | assign2  | Test assignment2 | Test assignment description 2 | 1       | 1                                   | 1          | 0              |
      | assign   | Acceptance test site | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   | 0          | 0              |
      | assign   | Acceptance test site | assign2  | Test assignment2 | Test assignment description 2 | 1       | 1                                   | 0          | 0              |

  @javascript
  Scenario: In read mode, on front page, when admin try move an activity, anchor link tabindex attribute should exists and value is -1.
    Given I log in as "admin"
    And I am on site homepage
    And I click on "button.snap-edit-asset-more[title='More Options \"Test assignment2\"']" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-asset-move[aria-label='Move \"Test assignment2\"']" "css_element"
    Then I should see "Moving \"Test assignment2\""
    And the "tabindex" attribute of ".snap-asset .mod-link" "css_element" should contain "-1"
    And the "tabindex" attribute of ".snap-asset .snap-completion-meta a" "css_element" should contain "-1"
    And I click on "li.snap-activity:not(.asset-moving) a.snap-move-note" "css_element"
    Then "//li[contains(@class, 'snap-activity')]//p[contains(text(), \"Test assignment1\")]" "xpath_element" should appear after the "//li[contains(@class, 'snap-activity')]//p[contains(text(), \"Test assignment2\")]" "xpath_element"

  @javascript
  Scenario: In read mode, on course, when teacher try move an activity, anchor links tabindex attribute should exists and value is -1 and button disabled.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And I click on "button.snap-edit-asset-more[title='More Options \"Test assignment2\"']" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-asset-move[aria-label='Move \"Test assignment2\"']" "css_element"
    Then I should see "Moving \"Test assignment2\""
    And the "tabindex" attribute of ".snap-asset .mod-link" "css_element" should contain "-1"
    And the "tabindex" attribute of ".snap-asset .snap-completion-meta a" "css_element" should contain "-1"
    And I click on "li.snap-activity:not(.asset-moving) a.snap-move-note" "css_element"
    Then "//li[contains(@class, 'snap-activity')]//p[contains(text(), \"Test assignment1\")]" "xpath_element" should appear after the "//li[contains(@class, 'snap-activity')]//p[contains(text(), \"Test assignment2\")]" "xpath_element"
