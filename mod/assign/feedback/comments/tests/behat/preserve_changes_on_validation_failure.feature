@mod @mod_assign @assignfeedback @assignfeedback_comments
Feature: Check that any changes to assignment feedback comments are not lost
  if the grading form validation fails due to an invalid grade.
  In order to ensure that the feedback changes are not lost
  As a teacher
  I need to grade a student and ensure that all feedback changes are preserved

  @javascript
  Scenario: Update the grade and feedback for an assignment
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name                 | course | assignfeedback_comments_enabled |
      | assign   | Test assignment name | C1     | 1                               |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    When I set the following fields to these values:
      | Grade out of 100  | 101                    |
      | Feedback comments | Feedback from teacher. |
    And I press "Save changes"
    Then I should see "Grade must be less than or equal to 100."
    And the following fields match these values:
      | Feedback comments | Feedback from teacher. |
