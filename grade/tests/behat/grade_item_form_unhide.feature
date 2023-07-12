@core @core_grades
Feature: Teacher can unhide grades on the edit page allowing students to view their grades
  In order to show the grades of an activity to a student
  As a teacher
  I need to unhide an activity on the edit page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | assignfeedback_comments_enabled |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1                               |
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100  | 50                       |
      | Feedback comments | I'm the teacher feedback |
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I should see "50.00"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"

  @javascript
  Scenario: Hiding the activity using the drop-down hide link and then unhiding the activity using the edit settings form page
    Given I hide the grade item "Test assignment name" of type "gradeitem" on "setup" page
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I should not see "50.00"
    And I should not see "I'm the teacher feedback"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I click on grade item menu "Test assignment name" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And the field "Hidden" matches value "1"
    And I set the field "Hidden" to "0"
    And I press "Save changes"
    And I log out
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    Then I should see "50.00"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"

  @javascript
  Scenario: Hiding the activity using the edit settings form page and then unhiding the activity using the drop-down show link
    Given I click on grade item menu "Test assignment name" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And I set the field "Hidden" to "1"
    And I press "Save changes"
    And I log out
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I should not see "50.00"
    And I should not see "I'm the teacher feedback"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I click on grade item menu "Test assignment name" of type "gradeitem" on "setup" page
    And I choose "Show" in the open action menu
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    Then I should see "50.00"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"

  @javascript
  Scenario: Hiding the category using the drop-down hide link and then unhiding the category using the edit settings form page
    Given I hide the grade item "Course 1" of type "course" on "setup" page
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I should not see "50.00"
    And I should not see "I'm the teacher feedback"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I click on grade item menu "Course 1" of type "course" on "setup" page
    And I choose "Edit category" in the open action menu
    And the field "Hidden" matches value "1"
    And I set the field "Hidden" to "0"
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    Then I should see "50.00"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"

  @javascript
  Scenario: Hiding the category using the edit settings form page and then unhiding the category using the drop-down show link
    Given I set the following settings for grade item "Course 1" of type "course" on "setup" page:
      | Hidden | 1 |
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I should not see "50.00"
    And I should not see "I'm the teacher feedback"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I click on grade item menu "Course 1" of type "course" on "setup" page
    And I choose "Show" in the open action menu
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    Then I should see "50.00"
    And I should see "I'm the teacher feedback" in the "Feedback comments" "table_row"
