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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 0 |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
      | Hide grader identity from students | 0 |
    And I log out
    # Upload to the test assignment
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    And I log out
    # Grade the submission and leave feedback
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should not see "Graded" in the "Student 1" "table_row"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Feedback comments" to "Catch for us the foxes."
    And I press "Save changes"
    And I press "OK"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "Graded" in the "Student 1" "table_row"
    And I log out

  @javascript
  Scenario: Hidden grading is disabled.
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"
    And I log out

  @javascript
  Scenario: Hidden grading is enabled.
    # Enable the hidden grader option
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I follow "Expand all"
    And I set the field "Hide grader identity from students" to "1"
    And I press "Save and return to course"
    And I log out
    # Check the student doesn't see the grader's identity
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should not see "Graded by"
    And I log out

  @javascript
  Scenario: Hidden grading is enabled, but students have the 'view' capability.
    Given the following "permission overrides" exist:
      | capability | permission | role | contextlevel | reference |
      | mod/assign:showhiddengrader | Allow | student | Course | C1 |
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "Catch for us the foxes."
    And I should see "Teacher" in the "Graded by" "table_row"
    And I log out
