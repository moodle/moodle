@mod @mod_lesson @core_completion
Feature: Pass grade activity completion in the lesson activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Vinnie    | Student2 | student2@example.com |
      | student3 | Vinnie    | Student3 | student3@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking          | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And the following "activity" exists:
      | activity                   | lesson        |
      | course                     | C1            |
      | idnumber                   | mh1           |
      | name                       | Music history |
      | section                    | 1             |
      | gradepass                  | 50            |
      | completion                 | 2             |
      | completionusegrade         | 1             |
      | completionpassgrade        | 1             |
    And I am on "Course 1" course homepage
    And I follow "Music history"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 2? |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | End of lesson |
      | id_enableotheranswers | 1 |
      | id_jumpto_6 | Next page |
    And I press "Save page"
    And I log out

  Scenario: View automatic completion items as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Music history"
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "lesson activity" page logged in as student1
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    When I am on "Course 1" course homepage
    And I follow "Music history"
    And I set the field "Your answer" to "3"
    And I press "Submit"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
    And I log out
    And I am on the "Music history" "lesson activity" page logged in as student2
    And I set the field "Your answer" to "0"
    And I press "Submit"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Vinnie Student1" user has completed "Music history" activity
    And "Vinnie Student2" user has completed "Music history" activity
    And "Vinnie Student3" user has not completed "Music history" activity
