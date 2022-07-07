@mod @mod_assign @core_outcome @javascript
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
    And I log in as "admin"
    And I navigate to "Grades > Scales" in site administration
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name | Test Scale |
      | Scale | Disappointing, Excellent, Good, Very good, Excellent |
    And I press "Save changes"
    And I navigate to "Grades > Outcomes" in site administration
    And I press "Add a new outcome"
    And I set the following fields to these values:
      | Full name | Outcome Test |
      | Short name | OT |
      | Scale | Test Scale |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "Outcomes" in current page administration
    And I set the field "Available standard outcomes" to "Outcome Test"
    And I click on "#add" "css_element"
    And I log out

  Scenario: Giving an outcome to a student
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | assignsubmission_onlinetext_enabled | 1 |
      | Outcome Test | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | My online text |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 0" "table_row"
    And I set the following fields to these values:
      | Outcome Test: | Excellent |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    Then I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 1" "table_row"

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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    And I add "Student 0 (student0@example.com)" user to "Group 1" group members
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | assignsubmission_onlinetext_enabled | 1 |
      | Students submit in groups | Yes |
      | Group mode | No groups |
      | Outcome Test | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | My online text |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 0" "table_row"
    And I set the following fields to these values:
      | Outcome Test: | Excellent |
      | Apply grades and feedback to entire group | Yes |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    Then I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should see "Outcome Test: Excellent" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 2" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Outcome Test: | Disappointing |
      | Apply grades and feedback to entire group | No |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Outcome Test: Excellent" in the "Student 0" "table_row"
    And I should see "Outcome Test: Disappointing" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Disappointing" in the "Student 0" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 1" "table_row"
    And I should not see "Outcome Test: Disappointing" in the "Student 2" "table_row"
    And I should not see "Outcome Test: Excellent" in the "Student 2" "table_row"
