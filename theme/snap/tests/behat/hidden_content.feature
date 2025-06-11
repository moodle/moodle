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
# Test to check correct hidden content when importing a course into another
#
# @package    theme_snap
# @copyright  2020 Rafael Becerra <rafael.becerrarodriguez@openlms.net>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When importing a course into another, the hidden content should be applied correctly.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | section | initsections |
      | Course 1 | C1        | 0        | topics | 3       |      1       |
      | Course 2 | C2        | 0        | topics | 3       |      1       |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | page       | TestP 1      | Test page description       | C1     | page1     | 1       |

  @javascript
  Scenario Outline: When importing a course that has a section hidden and a visible activity into another,
    should appear into the new course with the section visible and the activity hidden.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I follow "Hide"
    And "li.draft.snap-visible-section span.text small.published-status" "css_element" should exist
    And I am on the course main page for "C2"
    And I click on "#admin-menu-trigger" "css_element"
    And I change window size to "large"
    And I expand "Course reuse" node
    And I follow "Import"
    And I click on "tbody tr .c0 input#import-course-2" "css_element"
    And I press "Continue"
    And I press "Next"
    And I press "Next"
    And I press "Perform import"
    And I press "Continue"
    And I follow "Section 1"
    And I should see "Not published to students"
    And the "class" attribute of "li.snap-asset.activity.page.modtype_page" "css_element" should contain "draft"
    Examples:
      | Option     |
      | 0          |
      | 1          |
