@mod @mod_qbassign
Feature: In an qbassignment, teacher can require submission statements
  In order to require students to accept an qbassignment submission statement
  As a teacher
  I need to enable "Require that students accept the submission statement"

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry     | Teacher  | teacher1@example.com |
      | student1 | Sam       | Student | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity                            | qbassign      |
      | course                              | C1          |
      | name                                | Test qbassign |
      | submissiondrafts                    | 1           |
      | requiresubmissionstatement          | 1           |
      | qbassignsubmission_onlinetex_enabled | 1           |

  Scenario: Student is required to accept qbassignment submission statement
    Given I am on the "Test qbassign" "qbassign activity" page logged in as student1
    And I press "Add submission"
    And I set the field "Online text" to "My submission text."
    And I press "Save changes"
    And I should see "Draft (not submitted)" in the "Submission status" "table_row"
    When I press "Submit qbassignment"
    Then I should see "This submission is my own work, except where I have acknowledged the use of the works of other people."
    And I press "Continue"
    And I should see "Confirm submission"
    And I should see "- Required"
    And I set the field "submissionstatement" to "1"
    And I press "Continue"
    And I should see "Submitted for grading" in the "Submission status" "table_row"

  Scenario: Student is not required to accept qbassignment submission statement
    Given I am on the "Test qbassign" "qbassign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Require that students accept the submission statement | No |
    And I press "Save and display"
    And I am on the "Test qbassign" "qbassign activity" page logged in as student1
    And I press "Add submission"
    And I set the field "Online text" to "My submission text."
    And I press "Save changes"
    And I should see "Draft (not submitted)" in the "Submission status" "table_row"
    When I press "Submit qbassignment"
    Then I should not see "This submission is my own work, except where I have acknowledged the use of the works of other people."
    And I press "Continue"
    And I should see "Submitted for grading" in the "Submission status" "table_row"
