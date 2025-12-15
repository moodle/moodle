# This file is part of Moodle - https://moodle.org/
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
# along with Moodle. If not, see <https://www.gnu.org/licenses/>.
#
# Test to verify the rendering of the Page Activity expand
# icon on the Course page based on its configured settings.
#
# @package    theme_snap
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_mod_feedback
Feature: Edit feedback as a teacher
  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name          | course | idnumber    |
      | feedback   | Feedback Test | C1     | feedback1   |
    And I am on the "Feedback Test" "feedback activity" page logged in as teacher
    And I navigate to "Questions" in current page administration
    And I add a "Multiple choice" question to the feedback with:
      | Question               | this is a multiple choice 1 |
      | Label                  | multichoice1                |

  @javascript
  Scenario: View question editing elements and required classes for drag and drop
    Given I am on the "Feedback Test" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    Then ".moodle-actionmenu" "css_element" should exist
    And "#feedback_dragarea form" "css_element" should exist
    And ".row.feedback_itemlist" "css_element" should exist

