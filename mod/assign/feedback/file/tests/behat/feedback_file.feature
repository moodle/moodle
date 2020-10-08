@mod @mod_assign @assignfeedback @assignfeedback_file @_file_upload
Feature: In an assignment, teacher can submit feedback files during grading
  In order to provide a feedback file
  As a teacher
  I need to submit a feedback file.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name                  | Test assignment name |
      | Description                      | Submit your PDF file |
      | Maximum number of uploaded files | 2                    |
      | Students submit in groups        | Yes                  |
    And I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I follow "Expand all"
    And I set the field "assignfeedback_file_enabled" to "1"
    And I press "Save and display"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "mod/assign/feedback/file/tests/fixtures/submission.txt" file to "File submissions" filemanager
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I should see "submission.txt"
    And I should see "Not graded"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I click on "Grade" "link" in the ".submissionlinks" "css_element"
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager

  @javascript
  Scenario: A teacher can provide a feedback file when grading an assignment.
    Given I set the field "applytoall" to "0"
    And I press "Save changes"
    And I click on "Course 1" "link" in the "[data-region=assignment-info]" "css_element"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "feedback.txt"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should not see "feedback.txt"

  @javascript
  Scenario: A teacher can provide a feedback file when grading an assignment and all students in the group will receive the file.
    Given I press "Save changes"
    And I click on "Course 1" "link" in the "[data-region=assignment-info]" "css_element"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "feedback.txt"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "feedback.txt"
