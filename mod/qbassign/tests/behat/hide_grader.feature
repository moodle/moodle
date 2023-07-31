@mod @mod_qbassign @_file_upload
Feature: Hide grader identities identity from students
  In order to keep the grader's identity a secret
  As a moodle teacher
  I need to enable Hide Grader in the qbassignment settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    # Set up the test qbassignment
    And the following "activity" exists:
      | activity                           | qbassign                  |
      | course                             | C1                      |
      | name                               | Test qbassignment name    |
      | submissiondrafts                   | 0                       |
      | teamsubmission                     | 1                       |
      | asignsubmission_onlinetex_enabled | 0                       |
      | qbassignsubmission_file_enabled      | 1                       |
      | qbassignsubmission_file_maxfiles     | 2                       |
      | qbassignsubmission_file_maxsizebytes | 1000000                 |
      | qbassignfeedback_comments_enabled    | 1                       |
      | hidegrader                         | 0                       |
    And the following "mod_qbassign > submission" exists:
      | qbassign  | Test qbassignment name          |
      | user    | student1                      |
      | file    | lib/tests/fixtures/empty.txt  |

    # Grade the submission and leave feedback
    And I am on the "Test qbassignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I should not see "Graded" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Feedback comments" to "Catch for us the foxes."
    And I press "Save changes"
    And I follow "Test qbassignment name"
    And I follow "View all submissions"
    And I should see "Graded" in the "Student 1" "table_row"
    And I log out

  @javascript
  Scenario: Hidden grading is disabled.
    Given I am on the "Test qbassignment name" Activity page logged in as student1
    Then I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"

  @javascript
  Scenario: Hidden grading is enabled.
    # Enable the hidden grader option
    Given I am on the "Test qbassignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    And I set the field "Hide grader identity from students" to "1"
    And I press "Save and return to course"
    And I log out

    # Check the student doesn't see the grader's identity
    When I am on the "Test qbassignment name" Activity page logged in as student1
    Then I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should not see "Graded by"

  @javascript
  Scenario: Hidden grading is enabled, but students have the 'view' capability.
    Given the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | mod/qbassign:showhiddengrader | Allow | student | Course | C1 |
    When I am on the "Test qbassignment name" Activity page logged in as student1
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"
