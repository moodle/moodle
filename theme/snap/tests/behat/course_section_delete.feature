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
# Tests deleting sections in snap.
#
# @package    theme_snap
# @author     Guy Thomas
# @copyright  2016 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_course_section
Feature: When the moodle theme is set to Snap, teachers can delete sections without having to reload the page.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections|
      | Course 1 | C1        | 0        | topics |      1      |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro                         | section | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment description 1 | 1       | 1                                   |
      | assign   | C1     | assign2  | Test assignment2 | Test assignment description 2 | 2       | 1                                   |

  @javascript
  Scenario Outline: In read mode, on course, teacher can cancel / confirm delete section.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"

    And I follow "Section 1"
    And I click on "#section-1 .edit-summary" "css_element"
    And I set the section name to "Section one"
    And I press "Save changes"
    And I follow "Section 2"
    And I click on "#section-2 .edit-summary" "css_element"
    And I set the section name to "Section two"
    And I press "Save changes"

    And I follow "Section one"
    Then "#section-1" "css_element" should exist
    And I click on "#extra-actions-dropdown-1" "css_element"
    When I click on "#section-1 .snap-section-editing.actions .snap-delete" "css_element"
    Then I should see section delete dialog
    And I cancel dialog
    Then I should not see section delete dialog
    And I should see "Section one"
    And I click on "#extra-actions-dropdown-1" "css_element"
    When I click on "#section-1 .snap-section-editing.actions .snap-delete" "css_element"
    Then I should see section delete dialog
    When I press "Delete Section"
    Then I should not see "Section one" in the "li[id^='section-']" "css_element"
    And I cannot see "Test assignment1" in course asset search
    And I can see "Test assignment2" in course asset search
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: Student cannot delete section.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1 .snap-section-editing.actions .snap-delete" "css_element" should not exist
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: When deleting a section the section number should update
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <coursepartialrender> | theme_snap |
      | leftnav             | <leftnav>             | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"

    And I follow "Section 1"
    And I click on "#section-1 .edit-summary" "css_element"
    And I set the section name to "Section one"
    And I press "Save changes"
    And I follow "Section 2"
    And I click on "#section-2 .edit-summary" "css_element"
    And I set the section name to "Section two"
    And I press "Save changes"

    And I follow "Section one"
    And I wait until the page is ready
    Then "#section-1 .content .sectionname .sectionnumber" "css_element" <titlenumber> exist
    Then I should see "<beforetitle>" in the "#section-1 .content .sectionname" "css_element"
    And I click on "#extra-actions-dropdown-1" "css_element"
    When I click on "#section-1 .snap-section-editing.actions .snap-delete" "css_element"
    Then I should see section delete dialog
    When I press "Delete Section"
    And I wait until the page is ready
    Then "#section-1 .content .sectionname .sectionnumber" "css_element" <titlenumber> exist
    Then I should see "<aftertitle>" in the "#section-1 .content .sectionname" "css_element"
    Examples:
      | coursepartialrender     | leftnav | titlenumber | beforetitle   | aftertitle   |
      | 0                       | list    | should not  | Section one   | Section two    |
      | 1                       | list    | should not  | Section one   | Section two    |
      | 0                       | top     | should      | Section one   | Section two    |
      | 1                       | top     | should      | 1.Section one | 1.Section two  |

  @javascript
  Scenario: Teacher with course update permission can see delete section confirmation dialog.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | 1 | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"

    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And I click on "#extra-actions-dropdown-1" "css_element"
    When I click on "#section-1 .snap-section-editing.actions .snap-delete" "css_element"
    Then I should see section delete dialog
    Then I should see "Are you absolutely sure you want to completely delete"

