@mod @mod_assign @_file_upload
Feature: Hide grader identities identity from students
  In order to keep the grader's identity a secret
  As a moodle teacher
  I need to enable Hide Grader in the assignment settings

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
    # Set up the test assignment
    And the following "activity" exists:
      | activity                           | assign                  |
      | course                             | C1                      |
      | name                               | Test assignment name    |
      | submissiondrafts                   | 0                       |
      | teamsubmission                     | 1                       |
      | asignsubmission_onlinetext_enabled | 0                       |
      | assignsubmission_file_enabled      | 1                       |
      | assignsubmission_file_maxfiles     | 2                       |
      | assignsubmission_file_maxsizebytes | 1000000                 |
      | assignfeedback_comments_enabled    | 1                       |
      | hidegrader                         | 0                       |
    And the following "mod_assign > submission" exists:
      | assign  | Test assignment name          |
      | user    | student1                      |
      | file    | lib/tests/fixtures/empty.txt  |

    # Grade the submission and leave feedback
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I should not see "Graded" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Feedback comments" to "Catch for us the foxes."
    And I press "Save changes"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And I should see "Graded" in the "Student 1" "table_row"
    And I log out

  @javascript
  Scenario: Hidden grading is disabled.
    Given I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"

  @javascript
  Scenario: Hidden grading is enabled.
    # Enable the hidden grader option
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I click on "Expand all" "link" in the "region-main" "region"
    And I set the field "Hide grader identity from students" to "1"
    And I press "Save and return to course"
    And I log out

    # Check the student doesn't see the grader's identity
    When I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should not see "Graded by"

  @javascript
  Scenario: Hidden grading is enabled, but students have the 'view' capability.
    Given the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | mod/assign:showhiddengrader | Allow | student | Course | C1 |
    When I am on the "Test assignment name" Activity page logged in as student1
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"
