@core @core_grades
Feature: We can enter in grades and view reports from the gradebook
  In order to check the expected results are displayed
  As a teacher
  I need to assign grades and check that they display correctly in the gradebook.
  I need to enable grade weightings and check that they are displayed correctly.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name 1 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name 2 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name 1"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for assignment 1 |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I follow "Course 1"
    And I follow "Test assignment name 2"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for assignment 2 |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment name 1"
    And I give the grade "90.00" to the user "Student 1" for the grade item "Test assignment name 2"
    And I press "Update"

  @javascript
  Scenario: Grade a grade item and ensure the results display correctly in the gradebook
    When I set the field "Grade report" to "User report"
    And the "Grade report" select box should contain "Grader report"
    And the "Grade report" select box should contain "Outcomes report"
    And the "Grade report" select box should contain "User report"
    And the "Select all or one user" select box should contain "All users (1)"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    Then the following should exist in the "user-grade" table:
      | Grade item | Grade | Range | Percentage |
      | Test assignment name 1 | 80.00 | 0–100 | 80.00 % |
      | Test assignment name 2 | 90.00 | 0–100 | 90.00 % |
      | Course total | 85.00 | 0–100 | 85.00 % |
    And the following should not exist in the "user-grade" table:
      | Grade item | Grade | Range | Percentage |
      | Course total | 90.00 | 0–110 | 90.00 % |
    And I set the field "Grade report" to "Overview report"
    And "C1" row "Grade" column of "overview-grade" table should contain "85.00"
    And "C1" row "Grade" column of "overview-grade" table should not contain "90.00"

  @javascript
  Scenario: We can add a weighting to a grade item and it is displayed properly in the user report
    When I set the field "Grade report" to "Full view"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I set the following fields to these values:
      | Extra credit value for Test assignment name | 0.72 |
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I follow "Course grade settings"
    And I set the following fields to these values:
      | Show weightings | Show |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    Then the following should exist in the "user-grade" table:
      | Grade item | Weight | Grade | Range | Percentage |
      | Test assignment name 1 | 0.72 | 80.00 | 0–100 | 80.00 % |
      | Test assignment name 2 | 1.00 | 90.00 | 0–100 | 90.00 % |
      | Course total | - | 85.81 | 0–100 | 85.81 % |
    And the following should not exist in the "user-grade" table:
      | Grade item | Weight | Percentage |
      | Test assignment name 1 | 0.72% | 0.72% |
      | Test assignment name 2 | 1.00% | 1.00% |
      | Course total | 1.00% | 1.00% |
