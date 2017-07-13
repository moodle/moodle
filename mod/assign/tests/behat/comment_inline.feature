@mod @mod_assign
Feature: In an assignment, teachers can edit a students submission inline
  In order to easily mark students assignments
  As a teacher
  I need to have a students submission text copied to the grading online form.

  @javascript @_file_upload
  Scenario: Submit a text online and edit the submission
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | assignfeedback_comments_enabled | 1 |
      | assignfeedback_file_enabled | 1 |
      | assignfeedback_comments_commentinline | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50 |
      | Feedback comments | I'm the teacher feedback |
    And I upload "lib/tests/fixtures/empty.txt" file to "Feedback files" filemanager
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    Then I should see "50.00" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Graded" in the "Student 1" "table_row"
    And I should see "I'm the student first submission" in the "Student 1" "table_row"
    And I should see "I'm the teacher feedback" in the "Student 1" "table_row"
    And I should see "empty.txt" in the "Student 1" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "Graded" in the "Grading status" "table_row"
    And I should see "I'm the student first submission" in the "Online text" "table_row"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"
    And I should see "empty.txt" in the "Feedback files" "table_row"
