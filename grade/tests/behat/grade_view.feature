@core @core_grades
Feature: We can enter in grades and view reports from the gradebook
  In order to check the expected results are displayed
  As a teacher
  I need to assign grades and check that they display correctly in the gradebook.
  I need to enable grade weightings and check that they are displayed correctly.

  Background:
    Given the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I fill the moodle form with:
      | Online text | This is a submission |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment name"
    And I press "Update"

  @javascript
  Scenario: Grade a grade item and ensure the results display correctly in the gradebook
    When I select "User report" from "Grade report"
    And the "Grade report" select box should contain "Grader report"
    And the "Grade report" select box should contain "Outcomes report"
    And the "Grade report" select box should contain "User report"
    And the "Select all or one user" select box should contain "All users (1)"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I should see "80.00" in the "Test assignment name" "table_row"
    And I select "Overview report" from "Grade report"
    And I should see "80.00" in the "overview-grade" "table"

  @javascript
  Scenario: We can add a weighting to a grade item and it is displayed properly in the user report
    When I select "Full view" from "Grade report"
    And I select "Weighted mean of grades" from "Aggregation"
    And I fill the moodle form with:
      | Extra credit value for Test assignment name | 0.72 |
    And I press "Save changes"
    And I select "User report" from "Grade report"
    And I follow "Course grade settings"
    And I fill the moodle form with:
      | Show weightings | Show |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    Then I should see "0.72" in the "Test assignment name" "table_row"
    And I should not see "0.72%" in the "Test assignment name" "table_row"
