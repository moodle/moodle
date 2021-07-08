@mod @mod_assign @javascript
Feature: In an assignment, students can comment in their submissions
  In order to refine assignment submissions
  As a student
  I need to add comments about submissions

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 1 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  Scenario: Student comments an assignment submission
    Given the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  |
      | assign    | C1      | Test assignment name  | 1                                    |
    And I am on the "Test assignment name" Activity page logged in as student1
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I click on ".comment-link" "css_element"
    And I set the field "content" to "First student comment"
    And I follow "Save comment"
    Then I should see "First student comment"
    And the field "content" matches value "Add a comment..."
    And I click on "Delete comment posted by Student 1" "link"
    # Wait for the animation to finish.
    And I wait "2" seconds
    And I set the field "content" to "Second student comment"
    And I follow "Save comment"
    And I should see "Second student comment"
    And I should not see "First student comment"
    And I follow "Test assignment name"
    And I click on ".comment-link" "css_element"
    And I should see "Second student comment"
    And I should not see "First student comment"

  Scenario: Teacher updated the comment box and clicked the save changes to reflect the comment
    Given the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  |
      | assign    | C1      | Test assignment name  | 1                                    |
    And I am on the "Test assignment name" Activity page logged in as student1
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I click on ".comment-link" "css_element"
    When I set the field "content" to "Teacher feedback first comment"
    And I press "Save changes"
    And I should see "Comments (1)" in the ".comment-link" "css_element"
    And I click on ".comment-link" "css_element"
    Then I should see "Teacher feedback first comment" in the ".comment-list" "css_element"

  Scenario: Teacher updated the comment box and clicked on save and show next to reflect the comment
    Given the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  |
      | assign    | C1      | Test assignment name  | 1                                    |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I click on ".comment-link" "css_element"
    When I set the field "content" to "Teacher feedback first comment"
    # click the save and show next twice as we have only 2 students
    # so the second time you click we reach the same student who made
    # the change
    And I press "Save and show next"
    And I press "Save and show next"
    And I click on ".comment-link" "css_element"
    Then I should see "Teacher feedback first comment" in the ".comment-list" "css_element"

  Scenario: Teacher can comment on an offline assignment
    Given the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  | assignmentsubmission_file_enabled  | assignfeedback_comments_enabled  |
      | assign    | C1      | Test assignment name  | 0                                    | 0                                  | 1                                |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    When I set the following fields to these values:
      | Grade out of 100   | 50                        |
      | Feedback comments  | I'm the teacher feedback  |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    Then I should see "50.00" in the "Student 1" "table_row"
    And I should see "I'm the teacher feedback" in the "Student 1" "table_row"

  Scenario: Teacher can comment on assignments with a zero grade
    Given the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  | assignmentsubmission_file_enabled  | assignfeedback_comments_enabled  |
      | assign    | C1      | Test assignment name  | 0                                    | 0                                  | 1                                |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 0 |
    And I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I set the following fields to these values:
      | Feedback comments | I'm the teacher feedback |
    And I press "Save changes"
    Then I should see "The changes to the grade and feedback were saved"
