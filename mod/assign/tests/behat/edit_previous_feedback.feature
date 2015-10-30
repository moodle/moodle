@mod @mod_assign
Feature: In an assignment, teachers can edit feedback for a students previous submission attempt
  In order to correct feedback for a previous submission attempt
  As a teacher
  I need to be able to edit the feedback for a students previous submission attempt.

  @javascript
  Scenario: Edit feedback for a students previous attempt.
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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignfeedback_comments_enabled | 1 |
      | Attempts reopened | Manually |
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 2" "link" in the "Student 2" "table_row"
    And I set the following fields to these values:
      | Grade | 49 |
      | Feedback comments | I'm the teacher first feedback |
      | Allow another attempt | Yes |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I click on ".mod-assign-history-link" "css_element"
    And I should see "I'm the teacher first feedback" in the "Feedback comments" "table_row"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 2" "link" in the "Student 2" "table_row"
    And I click on ".mod-assign-history-link" "css_element"
    And I follow "Edit the grade and feedback for attempt number 1"
    And I set the following fields to these values:
      | Grade | 50 |
      | Feedback comments | I'm the teacher second feedback |
    And I press "Save changes"
    And I log out
    Then I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I click on ".mod-assign-history-link" "css_element"
    And I should see "I'm the teacher second feedback" in the "Feedback comments" "table_row"
    And I should see "50.00"
