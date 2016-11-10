@mod @mod_assign
Feature: Group assignment submissions
  In order to allow students to work collaboratively on an assignment
  As a teacher
  I need to group submissions in groups

  @javascript
  Scenario: Switch between group modes
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student0 | Student | 0 | student0@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student0 | C1 | student |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Students submit in groups | Yes |
      | Group mode | No groups |
    And I follow "Test assignment name"
    When I follow "View all submissions"
    Then "//tr[contains(., 'Student 0')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exist
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and return to course"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 0 (student0@example.com)" user to "Group 1" group members
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exist
    And I should not see "Student 2"
    And I set the field "Separate groups" to "All participants"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exist

  @javascript
  Scenario: Confirm that the grading status changes for each group member
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | Students submit in groups | Yes |
      | Group mode | No groups |
      | Require group to make submission | No |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's first submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should not contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should not contain "Submitted for grading"
    And I log out
    And I log in as "student3"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's first submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should contain "Submitted for grading"

  Scenario: Confirm groups and submission counts are correct
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
      | student5 | Student | 5 | student5@example.com |
      | student6 | Student | 6 | student6@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student5 | C1 | student |
      | student6 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
      | Group 3 | C1 | G3 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
      | student3 | G2 |
      | student4 | G2 |
      | student5 | G3 |
      | student6 | G3 |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G2    |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | Students submit in groups | Yes |
      | Grouping for student groups | Grouping 1 |
      | Group mode | Separate groups |
      | Require group to make submission | No |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's 1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student3"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's 3 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student5"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student's 5 submission |
    And I press "Save changes"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "3" in the "Groups" "table_row"
    And I should see "3" in the "Submitted" "table_row"
    When I set the field "Separate groups" to "Group 1"
    And I press "Go"
    Then I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    And I set the field "Separate groups" to "Group 2"
    And I press "Go"
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    And I set the field "Separate groups" to "Group 3"
    And I press "Go"
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
