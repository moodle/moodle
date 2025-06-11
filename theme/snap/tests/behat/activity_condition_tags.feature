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
# Tests for visibility of activity restriction tags.
#
# @package    theme_snap
# @copyright Copyright (c) 2018 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap, activity restriction tags are shown.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
      | Course 1 | C1        | 0        | topics |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "groups" exist:
      | course | name   | idnumber |
      | C1     | Group1 | Group1   |
    And the following "group members" exist:
      | group  | user     |
      | Group1 | student1 |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro             | assignsubmission_onlinetext_enabled | assignfeedback_comments_enabled | section | duedate         |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment 1 | 1                                   | 1                               | 1       | ##tomorrow##    |
      | assign   | C1     | assign2  | Test assignment2 | Test assignment 2 | 1                                   | 1                               | 1       | ##next week##   |

  @javascript
  Scenario Outline: User sees the grade restriction.
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_snap |
    And I log out
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset-more[title='More Options \"Test assignment1\"']" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset[aria-label='Edit activity Test assignment1']" "css_element"
    And I wait until the page is ready
    And I click on "//fieldset[@id=\"id_availabilityconditionsheader\"]" "xpath_element"
    And I click on "//button[text()=\"Add restriction...\"]" "xpath_element"
    And I click on "//button[@id=\"availability_addrestriction_grade\"]" "xpath_element"
    And I set the field with xpath "//span[@class=\"pe-3\"][text()=\"Grade\"]//following-sibling::span//select" to "Test assignment2"
    Then I click on "//input[@id=\"id_submitbutton2\"]" "xpath_element"
    And I wait until the page is ready
    And I follow "Section 1"
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    Then I should see "You have a grade in Test assignment2"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: User sees all restrictions when matching all restrictions.
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_snap |
    And I log out
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset-more[title='More Options \"Test assignment1\"']" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset[aria-label='Edit activity Test assignment1']" "css_element"
    And I wait until the page is ready
    And I click on "//fieldset[@id=\"id_availabilityconditionsheader\"]" "xpath_element"
    And I click on "//button[text()=\"Add restriction...\"]" "xpath_element"
    And I click on "//button[@id=\"availability_addrestriction_grade\"]" "xpath_element"
    And I set the field with xpath "//span[@class=\"pe-3\"][text()=\"Grade\"]//following-sibling::span//select" to "Test assignment2"
    Then I click on "//button[text()=\"Add restriction...\"]" "xpath_element"
    And I click on "//button[@id=\"availability_addrestriction_group\"]" "xpath_element"
    And I set the field with xpath "//span[@class=\"pe-3\"][text()=\"Group\"]//following-sibling::span//select" to "Group1"
    And I set the field with xpath "//span[@class=\"accesshide\"][text()=\"Required restrictions \"]//following-sibling::select" to "all"
    Then I click on "//input[@id=\"id_submitbutton2\"]" "xpath_element"
    And I wait until the page is ready
    And I follow "Section 1"
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    Then I should see "You have a grade in Test assignment2"
    Then I should see "You belong to Group1"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: User sees all restrictions when matching any restrictions.
    Given I log in as "admin"
    And the following config values are set as admin:
      | resourcedisplay | <Option> | theme_snap |
    And I log out
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset-more[title='More Options \"Test assignment1\"']" "css_element"
    And I click on ".snap-activity.modtype_assign .snap-edit-asset[aria-label='Edit activity Test assignment1']" "css_element"
    And I wait until the page is ready
    And I click on "//fieldset[@id=\"id_availabilityconditionsheader\"]" "xpath_element"
    And I click on "//button[text()=\"Add restriction...\"]" "xpath_element"
    And I click on "//button[@id=\"availability_addrestriction_grade\"]" "xpath_element"
    And I set the field with xpath "//span[@class=\"pe-3\"][text()=\"Grade\"]//following-sibling::span//select" to "Test assignment2"
    Then I click on "//button[text()=\"Add restriction...\"]" "xpath_element"
    And I click on "//button[@id=\"availability_addrestriction_group\"]" "xpath_element"
    And I set the field with xpath "//span[@class=\"pe-3\"][text()=\"Group\"]//following-sibling::span//select" to "Group1"
    And I set the field with xpath "//span[@class=\"accesshide\"][text()=\"Required restrictions \"]//following-sibling::select" to "any"
    Then I click on "//input[@id=\"id_submitbutton2\"]" "xpath_element"
    And I wait until the page is ready
    And I follow "Section 1"
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    Then I should see "You have a grade in Test assignment2"
    Then I should see "You belong to Group1"
    Examples:
      | Option     |
      | 0          |
      | 1          |
