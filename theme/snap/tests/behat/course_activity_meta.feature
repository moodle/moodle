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
# @package    theme_snap
# @copyright  Copyright (c) 2017 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, students see meta data against course activities.

  Background:
    Given I skip because "To be reviewed on INT-20659 (Only fails on Gitlab)."
    Given the following config values are set as admin:
      | enableoutcomes | 1 |
      | theme | snap |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode | theme | initsections |
      | Course 1 |    C1     |    0     |     1     |       |       1      |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  @javascript
  Scenario Outline: Student sees correct meta data against course activities
    Given the following "activities" exist:
      | activity | course | idnumber | name             | intro             | assignsubmission_onlinetext_enabled | assignfeedback_comments_enabled | section | duedate         |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment 1 | 1                                   | 1                               | 1       | ##tomorrow##    |
      | assign   | C1     | assign2  | Test assignment2 | Test assignment 2 | 1                                   | 1                               | 1       | ##next week##   |
      | assign   | C1     | assign3  | Test assignment3 | Test assignment 3 | 1                                   | 1                               | 1       | ##yesterday##   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I wait until "#section-1" "css_element" is visible
    And I should see "Test assignment1"
    And assignment entitled "Test assignment1" shows as not submitted in metadata
    And assignment entitled "Test assignment1" is not overdue in metadata
    And assignment entitled "Test assignment1" does not have feedback metadata
    And assignment entitled "Test assignment2" shows as not submitted in metadata
    And assignment entitled "Test assignment2" is not overdue in metadata
    And assignment entitled "Test assignment2" does not have feedback metadata
    And assignment entitled "Test assignment3" shows as not submitted in metadata
    And assignment entitled "Test assignment3" is overdue in metadata
    And assignment entitled "Test assignment3" does not have feedback metadata
    And I am on activity "assign" "Test assignment1" page
    And I reload the page
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And assignment entitled "Test assignment1" shows as submitted in metadata
    And assignment entitled "Test assignment2" shows as not submitted in metadata
    And assignment entitled "Test assignment3" shows as not submitted in metadata
    And deadline for assignment "Test assignment3" in course "C1" is extended to "##next week##" for "student1"
    And I reload the page
    And assignment entitled "Test assignment3" is not overdue in metadata
    And I log out
    And I log in as "teacher1"
    And I grade the assignment "Test assignment1" in course "C1" as follows:
      | username | grade | feedback                 |
      | student1 | 50    | I'm the teacher feedback |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I wait until "#section-1" "css_element" is visible
    And I should see "Test assignment1"
    And assignment entitled "Test assignment1" has feedback metadata
    And assignment entitled "Test assignment2" does not have feedback metadata
    And assignment entitled "Test assignment3" does not have feedback metadata
    And Activity "assign" "Test assignment1" is deleted
    And Activity "assign" "Test assignment2" is deleted
    And Activity "assign" "Test assignment3" is deleted
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: Student that belongs to a specific group sees correct meta data against course activities
    And the following "users" exist:
      | username | firstname | lastname | email         |
      | student3 | Student   | 3 | student3@example.com |
      | student4 | Student   | 4 | student4@example.com |
    And the following "course enrolments" exist:
      | user | course | role    |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI1   |
      | student3 | GI2   |
      | student4 | GI2   |
    And I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "li#section-1 [data-action='open-chooser']" "css_element"
    And I follow "Assignment"
    # Create assignment 1.
    And I set the following fields to these values:
      | Assignment name                  | Test assign  |
      | Description                      | Description  |
      | Online text                      | 1            |
      | Group mode                       | 1            |
      | Students submit in groups        | Yes          |
      | Require all group members submit | No           |
    And I press "Save and return to course"
    And I should see "Test assign"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I wait until "#section-1" "css_element" is visible
    And I should see "Test assign"
    And assignment entitled "Test assign" shows as not submitted in metadata
    And I am on activity "assign" "Test assign" page
    And I reload the page
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And assignment entitled "Test assign" shows as submitted in metadata
    And I log out
    # Now we login as student2 and it must appear as submitted since is in the same group with student1.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I wait until "#section-1" "css_element" is visible
    And I should see "Test assign"
    And assignment entitled "Test assign" shows as submitted in metadata
    And I log out
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario Outline: Student sees correct feedback with multiple outcomes configured
    Given I log in as "admin"
    And the following config values are set as admin:
      | coursepartialrender | <Option> | theme_snap |
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Legacy outcomes" in current page administration
    And I click on "//*[contains(text(),'Manage outcomes')]" "xpath_element"
    And I press "Add a new outcome"
    And I follow "Add a new scale"
    And I set the following fields to these values:
      | Name | 1337dom scale |
      | Scale | Noob, Nub, 1337, HaXor |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Legacy outcomes" in current page administration
    And I click on "//*[contains(text(),'Manage outcomes')]" "xpath_element"
    And I press "Add a new outcome"
    And I set the following fields to these values:
      | Full name | M8d skillZ! |
      | Short name | skillZ! |
      | Scale | 1337dom scale |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I add a assign activity to course "C1" section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | M8d skillZ! | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Test assignment name']" "xpath_element"
    And I reload the page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Test assignment name']" "xpath_element"
    And I reload the page
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Test assignment name']" "xpath_element"
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
      | M8d skillZ! | 1337 |
      | Feedback comments | I'm the teacher first feedback |
    And I press "Save changes"
    And I follow "Assignment: Test assignment name"
    And I follow "View all submissions"
    Then I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "60.0"
    And I press "Save all quick grading changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And assignment entitled "Test assignment name" has feedback metadata
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And assignment entitled "Test assignment name" does not have feedback metadata
    Examples:
      | Option     |
      | 0          |
      | 1          |

  @javascript
  Scenario: Correct pending submissions for grading in course view for Snap
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Group mode" to "Separate groups"
    And I press "Save and display"
    And I am on "Course 1" course homepage
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student3 | Student   | 3        | student3@example.com |

    And the following "course enrolments" exist:
      | user     | course | role           |
      | student2 | C1     | student        |
      | student3 | C1     | student        |

    And the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
      | student2 | GI2   |
      | teacher1 | GI1   |
      | student3 | GI1   |

    # Create assignment 1.
    And I follow "Section 1"
    And I click on "li#section-1 [data-action='open-chooser']" "css_element"
    And I follow "Assignment"
    # Create assignment 1.
    And I set the following fields to these values:
      | Assignment name           | A1   |
      | Description               | x    |
      | Online text               | 1    |
      | Group mode                | 1    |
    And I press "Save and return to course"
    And I should see "A1"
    And I log out
    # Login as student from group 1 to submit an assignment.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I should see "A1"
    And I am on activity "assign" "A1" page
    And I reload the page
    And I click on "//*[contains(text(),'Add submission')]" "xpath_element"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    #Log as student from group 2 to submit an assignment.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I should see "A1"
    And I am on activity "assign" "A1" page
    And I reload the page
    And I click on "//*[contains(text(),'Add submission')]" "xpath_element"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    # Make sure we see the entire user and submission count with the right permissions.
    Given I log in as "admin"
    Given the following "permission overrides" exist:
      | capability                  | permission   | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Allow        | editingteacher | Course       | C1        |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I should see "2 of 3 Submitted, 2 Ungraded"
    And I log out
    Given I log in as "admin"
    # Make sure we see the entire user and submission count with the right permissions.
    Then the following "permission overrides" exist:
      | capability                  | permission   | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prohibit     | editingteacher | Course       | C1        |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "1 of 2 Submitted, 1 Ungraded"

  @javascript
  Scenario: Check that the forum Due date is being shown in the Course main page
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a forum activity to course "C1" section "1" and I fill the form with:
      | Forum name  | Test forum name                |
      | Description | Test forum description         |
      | Whole forum grading > Type | Point           |
      | Due date               | ##1 January 2000 08:00## |
    #And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "li#section-1 [data-action='open-chooser']" "css_element"
    And I click on "[title='Add a new Forum']" "css_element"
    # Create assignment 1.
    And I set the following fields to these values:
      | Forum name  | Test forum name 2                |
      | Description | Test forum 2 description         |
      | Whole forum grading > Type | Point           |
      | Due date               | ##2 January 2000 08:00## |
    And I press "Save and return to course"
    Then I should see "Due 1 January 2000"
    Then I should see "Due 2 January 2000"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    Then I should see "Due 2 January 2000"
    And I log out

  @javascript
  Scenario: Show due date in assignments to group members with group overrides
    Given the following "activities" exist:
      | activity | course | idnumber | name             | intro             | assignsubmission_onlinetext_enabled | assignfeedback_comments_enabled | section | duedate         | allowsubmissionsfromdate |
      | assign   | C1     | assign1  | Test assignment 1 | Test assignment 1 | 1                                   | 1                               | 1       | ##1 January 2000 08:00##    |##1 January 2000 08:00##|
      | assign   | C1     | assign2  | Test assignment 2 | Test assignment 2 | 1                                   | 1                               | 1       | ##2 January 2000 08:00##    |##1 January 2000 08:00##|
      | assign   | C1     | assign3  | Test assignment 3 | Test assignment 3 | 1                                   | 1                               | 1       | ##3 January 2000 08:00##    |##1 January 2000 08:00##|
    And the following "groups" exist:
      | name     | course | idnumber |
      | Group1       | C1     | G1      |
      | Group2       | C1     | G2      |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1   |
      | student2 | G2   |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should see "Due 2 January 2000"
    And I should see "Due 3 January 2000"
    Then I am on activity "assign" "Test assignment 1" page
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Overrides"
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group1             |
      | Due date               | ##1 December 2000 08:00## |
    And I press "Save"
    Then I am on "Course 1" course homepage
    Then I am on activity "assign" "Test assignment 2" page
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Overrides"
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group1             |
      | Due date               | ##2 December 2000 08:00## |
    And I press "Save"
    Then I am on "Course 1" course homepage
    And I follow "Section 1"
    And I should see "Due 1 January 2000"
    And I should see "Due 2 January 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 December 2000"
    And I should see "Due 2 December 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should see "Due 2 January 2000"
    And I should see "Due 3 January 2000"
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I am on activity "assign" "Test assignment 2" page
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Settings"
    And I set the following fields to these values:
      | Due date               | disabled |
    And I press "Save and return to course"
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should not see "Due 2 January 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 December 2000"
    And I should see "Due 2 December 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should not see "Due 2 January 2000"
    And I should see "Due 3 January 2000"
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I am on activity "assign" "Test assignment 2" page
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Settings"
    And I set the following fields to these values:
      | Due date               | ##5 January 2000 08:00## |
    And I press "Save and return to course"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should see "Due 5 January 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 December 2000"
    And I should see "Due 2 December 2000"
    And I should see "Due 3 January 2000"
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "Due 1 January 2000"
    And I should see "Due 5 January 2000"
    And I should see "Due 3 January 2000"
    And I log out

  @javascript
  Scenario: Show group modes in activity cards
    Given the following "activities" exist:
      | activity   | name              | course    | idnumber     | groupmode |
      | assign     | Test Assignment 1 | C1        | assign1      | 0         |
      | forum      | Test Forum 1      | C1        | forum1       | 1         |
      | resource   | Test Resource 1   | C1        | resource1    |           |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And ".snap-groups-more img[alt='No groups']" "css_element" should not exist in the ".snap-activity.assign" "css_element"
    And ".snap-groups-more img[alt='Separate groups']" "css_element" should not exist in the ".snap-activity.forum" "css_element"
    And ".snap-groups-more" "css_element" should not exist in the ".resource" "css_element"
    And I am on "Course 1" course homepage
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And ".snap-groups-more img[alt='No groups']" "css_element" should exist in the ".snap-activity.assign" "css_element"
    And ".snap-groups-more img[alt='Separate groups']" "css_element" should exist in the ".snap-activity.forum" "css_element"
    And ".snap-groups-more" "css_element" should not exist in the ".resource" "css_element"
    And I click on ".snap-activity.assign .snap-asset-actions" "css_element"
    And ".dropdown .groups-dropdown" "css_element" should exist in the ".snap-activity.assign #snap-asset-menu" "css_element"
    And ".snap-activity.assign #groups-menu" "css_element" should not be visible
    And I click on ".snap-activity.assign .groups-dropdown" "css_element"
    And ".snap-activity.assign #groups-menu" "css_element" should be visible
    And I should see "No groups"
    And I should see "Separate groups"
    And I should see "Visible groups"
    And ".snap-activity.assign #snap-groups-menu" "css_element" should not be visible
    And I click on ".snap-activity.assign .snap-groups-more" "css_element"
    And ".snap-activity.assign #snap-groups-menu" "css_element" should be visible
    And I should see "No groups"
    And I should see "Separate groups"
    And I should see "Visible groups"
    And I click on "Visible groups" "link"
    And ".snap-groups-more img[alt='No groups']" "css_element" should not exist in the ".snap-activity.assign" "css_element"
    And ".snap-groups-more img[alt='Visible groups']" "css_element" should exist in the ".snap-activity.assign" "css_element"

	@javascript
  Scenario: Show availability modes in activity cards
    Given the following config values are set as admin:
      | allowstealth | 1 |

    Given the following "activities" exist:
      | activity   | name              | course    | idnumber     |
      | assign     | Test Assignment 1 | C1        | assign1      |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage

    # Check availability action submenu.
    And I click on ".snap-activity.assign .snap-asset-actions" "css_element"
    And I click on ".dropdown .availability-dropdown" "css_element"

    Then I should see "Show on course page"
    Then I should see "Hide on course page"

    # Check Show on course page output.
    And I click on ".snap-activity.assign #availability-menu a[data-action='cmShow']" "css_element"
    Then I should not see "Not published to students"

    # Check Hide on course page output.
    And I click on ".snap-activity.assign .snap-asset-actions" "css_element"
    And I click on ".dropdown .availability-dropdown" "css_element"
    And I click on ".snap-activity.assign #availability-menu a[data-action='cmHide']" "css_element"

    Then I should see "Not published to students"
