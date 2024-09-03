@mod @mod_assign @core_outcome
Feature: Outcome grading
  In order to give an outcome to my student
  As a teacher
  I need to grade a submission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student0 | Student | 0 | student0@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student0 | C1 | student |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    And the following "scales" exist:
      | name       | scale                                                |
      | Test Scale | Disappointing, Excellent, Good, Very good, Excellent |
    And the following "grade outcomes" exist:
      | fullname        | shortname | scale      |
      | Outcome Test    | OT        | Test Scale |
    And I am on the "Course 1" "grades > outcomes" page logged in as admin
    And I set the field "Available standard outcomes" to "Outcome Test"
    And I click on "#add" "css_element"
    And I log out

  @javascript
  Scenario: Giving an outcome to a student
    Given I log in as "teacher1"
    And I add a assign activity to course "Course 1" section "1" and I fill the form with:
      | Assignment name                     | Test assignment name        |
      | ID number                           | Test assignment name        |
      | Description                         | Test assignment description |
      | assignsubmission_onlinetext_enabled | 1                           |
      | Outcome Test                        | 1                           |
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | My online text |
    And I press "Save changes"
    When I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I go to "Student 0" "Test assignment name" activity advanced grading page
    And I set the following fields to these values:
      | Outcome Test: | Excellent |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    When I am on the "Test assignment name" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 1" "table_row"

  @javascript
  Scenario: Giving an outcome to a group submission
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user     | group |
      | student0 | G1    |
      | student1 | G1    |
    And I log in as "teacher1"
    And I add a assign activity to course "Course 1" section "1" and I fill the form with:
      | Assignment name                     | Test assignment name        |
      | Description                         | Test assignment description |
      | ID number                           | Test assignment name        |
      | assignsubmission_onlinetext_enabled | 1                           |
      | Students submit in groups           | Yes                         |
      | Group mode                          | No groups                   |
      | Outcome Test                        | 1                           |
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | My online text |
    And I press "Save changes"
    When I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I go to "Student 0" "Test assignment name" activity advanced grading page
    And I set the following fields to these values:
      | Outcome Test: | Excellent |
      | Apply grades and feedback to entire group | Yes |
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page
    And I navigate to "Submissions" in current page administration
    Then I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should see "Outcome Test: Excellent" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 2" "table_row"
    And I click on "Grade actions" "actionmenu" in the "Student 1" "table_row"
    And I choose "Grade" in the open action menu
    And I set the following fields to these values:
      | Outcome Test: | Disappointing |
      | Apply grades and feedback to entire group | No |
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should see "Outcome Test: Disappointing" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Disappointing" in the "Student 0" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Disappointing" in the "Student 2" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 2" "table_row"
