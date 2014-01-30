@mod @mod_assign
Feature: In an assignment, teachers can edit a students submission inline
  In order to easily mark students assignments
  As a teacher
  I need to have a students submission text copied to the grading online form.

  @javascript
  Scenario: Submit a text online and edit the submission
    Given the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
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
      | assignsubmission_file_enabled | 0 |
      | assignfeedback_comments_enabled | 1 |
      | assignfeedback_comments_commentinline | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 1" "link" in the "Student 1" "table_row"
    And I press "Save changes"
    And I press "Continue"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "I'm the student first submission" in the "Feedback comments" "table_row"
