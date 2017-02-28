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
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Student comments an assignment submission
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1 |
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
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

  Scenario: Teacher can comment on an offline assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignsubmission_onlinetext_enabled | assignmentsubmission_file_enabled | assignfeedback_comments_enabled |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 0 | 0 | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    When I set the following fields to these values:
      | Grade out of 100 | 50 |
      | Feedback comments | I'm the teacher feedback |
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    Then I should see "50.00" in the "Student 1" "table_row"
    And I should see "I'm the teacher feedback" in the "Student 1" "table_row"

  Scenario: Teacher can comment on assignments with a zero grade
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignsubmission_onlinetext_enabled | assignmentsubmission_file_enabled | assignfeedback_comments_enabled |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 0 | 0 | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 0 |
    And I press "Save changes"
    And I should see "The changes to the grade and feedback were saved"
    And I press "Ok"
    And I set the following fields to these values:
      | Feedback comments | I'm the teacher feedback |
    And I press "Save changes"
    Then I should see "The changes to the grade and feedback were saved"
