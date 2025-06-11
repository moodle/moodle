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
# Tests for conditional resources.
#
# @package    theme_snap
# @author     2015 Guy Thomas
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, conditional restrictions work as normal.

  Background:

    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | enablecompletion | initsections |
      | Course 1 | C1        | 0        | 1         | 1                |      1       |
    And the following "activities" exist:
      | activity | course | idnumber | name                        | intro                     | section | assignsubmission_onlinetext_enabled | completion | completionview |
      | assign   | C1     | assign1  | S1 Restricted               | Restricted by date past   | 1       | 1                                   | 1          | 0              |
      | assign   | C1     | assign2  | S2 Restricted               | Restricted by date future | 1       | 1                                   | 1          | 0              |
      | assign   | C1     | assign3  | S3 Restricted               | Restricted by completion  | 2       | 1                                   | 1          | 0              |
      | assign   | C1     | assign5  | S3 Completion - view        | View completion active    | 3       | 1                                   | 1          | 1              |
      | assign   | C1     | assign6  | S4 Activity                 | View completion active    | 4       | 1                                   | 1          | 1              |
      | assign   | C1     | assign6  | S6 Activ'ity                 | View completion active    | 4       | 1                                   | 1          | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "groups" exist:
      | course | name    | idnumber |
      | C1     | Grou'p1 | Group1   |

  @javascript
  Scenario Outline: Conditionally restricted section notices show for students only when restrictions not met but always show for teachers.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
      | resourcedisplay     | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I go to course section 1
    And I restrict course asset "S1 Restricted" by completion of "S2 Restricted"
    And I am on the course main page for "C1"
    And I go to course section 1
    And I click on "li.modtype_assign a.snap-conditional-tag" "css_element"
    And I should see "Not available unless: The activity S2 Restricted is marked complete"
    And I restrict course section 1 by date to "yesterday"
    And I am on the course main page for "C1"
    And I restrict course section 2 by date to "tomorrow"
    And I should see "Conditional" in TOC item 1
    And I should see "Conditional" in TOC item 2
    And I should not see "Conditional" in TOC item 3
    And I am on the course main page for "C1"
    And I go to course section 1
    And I should see available from date of "yesterday" in section 1
    And I go to course section 2
    And I should see available from date of "tomorrow" in section 2
    And I go to course section 4
    And I click on "#section-4 .edit-summary" "css_element"
    And I set the section name to "Section 4"
    And I apply asset completion restriction "S3 Completion - view" to section
    And I am on the course main page for "C1"
    And I go to course section 4
    And I should see availability info "Not available unless: The activity S3 Completion - view is marked complete" in "section" "4"
    And I go to course section 3
    And I click on "//li[contains(@class, 'modtype_assign')]//a/p[contains(text(), 'S3 Completion - view')]" "xpath_element"
    And I am on the course main page for "C1"
    And I go to course section 4
    And I should see availability info "Not available unless: The activity S3 Completion - view is marked complete" in "section" "4"
    And I log out
    # Check the restrictions as student.
    And I log in as "student1"
    And I am on the course main page for "C1"
    And I should not see "Conditional" in TOC item 1
    And I should see "Conditional" in TOC item 2
    And I should not see "Conditional" in TOC item 3
    And I go to course section 1
    And I should not see available from date of "yesterday" in section 1
    And I click on "li.modtype_assign a.snap-conditional-tag" "css_element"
    And I should see "Not available unless: The activity S2 Restricted is marked complete"
    And I go to course section 2
    And I should see available from date of "tomorrow" in section 2
    And "#section-2 li.snap-activity" "css_element" should not exist
    And I go to course section 4
    And I should see availability info "Not available unless: The activity S3 Completion - view is marked complete" in "section" "4"
    And I go to course section 3
    And I click on "//li[contains(@class, 'modtype_assign')]//a/p[contains(text(), 'S3 Completion - view')]" "xpath_element"
    And I am on the course main page for "C1"
    And I go to course section 4
    And I should not see availability info "Not available unless: The activity S3 Completion - view is marked complete" in "section" "4"
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: Activities that has an apostrophe in the title should be displayed correctly in the restriction popup menu.
    # Scenario if the group has an apostrophe in the title.
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
      | resourcedisplay     | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I go to course section 1
    And I restrict course asset "S1 Restricted" by belong to the group "Grou'p1"
    And I am on the course main page for "C1"
    And I go to course section 1
    And I click on "//a[@class='snap-conditional-tag']" "xpath_element"
    And I should see "Not available unless: You belong to Grou'p1"
    And I log out
    # Scenario if an assignment has an apostrophe in the title.
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I go to course section 2
    And I restrict course asset "S3 Restricted" by completion of "S6 Activ'ity"
    And I am on the course main page for "C1"
    And I go to course section 2
    And I click on "//li[@id='section-2']//a[@class='snap-conditional-tag']" "xpath_element"
    And I should see "Not available unless: The activity S6 Activ'ity is marked complete"
    Examples:
      | Option     |
      | 0          |
      | 1          |
