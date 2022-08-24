@mod @mod_assign
Feature: In an assignment, teachers can edit feedback for a students previous submission attempt
  In order to correct feedback for a previous submission attempt
  As a teacher
  I need to be able to edit the feedback for a students previous submission attempt.

  # This scenario has Atto-specific steps. See MDL-75913 for further details.
  @javascript @editor_atto
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
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignfeedback_comments_enabled     | 1                       |
      | submissiondrafts                    | 0                       |
      | attemptreopenmethod                 | manual                  |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                        |
      | Test assignment name  | student2  | I'm the student first submission  |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 2" "table_row"
    And I set the following fields to these values:
      | Grade | 49 |
      | Feedback comments | I'm the teacher first feedback |
      | Allow another attempt | Yes |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should see "I'm the teacher first feedback" in the "Feedback comments" "table_row"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 2" "table_row"
    And I click on "View a different attempt" "link"
    And I click on "Attempt 1" "radio" in the "View a different attempt" "dialogue"
    And I click on "View" "button"
    And I set the following fields to these values:
      | Grade | 50 |
      | Feedback comments | I'm the teacher second feedback |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    Then I should see "I'm the teacher second feedback" in the "Feedback comments" "table_row"
    And I should see "50.00"
    And I click on ".mod-assign-history-link" "css_element"
    And I should not see "I'm the teacher second feedback" in the "Feedback comments" "table_row"
