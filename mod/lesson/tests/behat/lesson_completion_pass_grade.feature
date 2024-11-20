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
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                   | lesson        |
      | course                     | C1            |
      | idnumber                   | mh1           |
      | name                       | Music history |
      | gradepass                  | 50            |
      | completion                 | 2             |
      | completionusegrade         | 1             |
      | completionpassgrade        | 1             |
    And the following "mod_lesson > page" exist:
      | lesson        | qtype   | title              | content        |
      | Music history | numeric | Numerical question | What is 1 + 2? |
    And the following "mod_lesson > answers" exist:
      | page               | answer          | jumpto        | score |
      | Numerical question | 3               | End of lesson | 1     |
      | Numerical question | @#wronganswer#@ | Next page     | 0     |

  Scenario: View automatic completion items as a teacher
    When I am on the "Music history" "lesson activity" page logged in as teacher1
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "lesson activity" page logged in as student1
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    When I am on the "Music history" "lesson activity" page
    And I set the field "Your answer" to "3"
    And I press "Submit"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
    And I am on the "Music history" "lesson activity" page logged in as student2
    And I set the field "Your answer" to "0"
    And I press "Submit"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"
    And I am on the "Course 1" course page logged in as teacher1
    And "Vinnie Student1" user has completed "Music history" activity
    And "Vinnie Student2" user has completed "Music history" activity
    And "Vinnie Student3" user has not completed "Music history" activity
