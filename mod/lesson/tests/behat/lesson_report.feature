@mod @mod_lesson
Feature: In a lesson activity, teachers can review student attempts
  To review student attempts in a lesson
  As a Teacher
  I need to view the reports.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name             | course | idnumber    | retake |
      | lesson     | Test lesson name | C1     | lesson1     | 1      |

  Scenario: View student attempts in a lesson containing both content and question pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | content   | Third page name       | Third page contents       |
      | Test lesson name | truefalse | True/false question 1 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 2 | Kermit is a frog          |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response | jumpto        | score |
      | First page name       | Next page     |          | Next page     | 0     |
      | Second page name      | Previous page |          | Previous page | 0     |
      | Second page name      | Next page     |          | Next page     | 0     |
      | Third page name       | Previous page |          | Previous page | 0     |
      | Third page name       | Next page     |          | Next page     | 0     |
      | True/false question 1 | True          | Correct  | Next page     | 1     |
      | True/false question 1 | False         | Wrong    | This page     | 0     |
      | True/false question 2 | True          | Correct  | Next page     | 1     |
      | True/false question 2 | False         | Wrong    | This page     | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    Then I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I should see "Student 1"
    And I should see "100%"
    And I should see "High score"
    And I should see "Average score"
    And I should see "Low score"

  Scenario: View student attempts in a lesson containing only content pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title            | content              |
      | Test lesson name | content   | First page name  | First page contents  |
      | Test lesson name | content   | Second page name | Second page contents |
      | Test lesson name | content   | Third page name  | Third page contents  |
      | Test lesson name | content   | Fourth page name | Fourth page contents |
    And the following "mod_lesson > answers" exist:
      | page             | answer        | jumpto        |
      | First page name  | Next page     | Next page     |
      | Second page name | Previous page | Previous page |
      | Second page name | Next page     | Next page     |
      | Third page name  | Previous page | Previous page |
      | Third page name  | Next page     | Next page     |
      | Fourth page name | Previous page | Previous page |
      | Fourth page name | End of lesson | End of lesson |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Fourth page contents"
    And I press "End of lesson"
    Then I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I should see "Student 1"
    And I should not see "High score"
    And I should not see "Average score"
    And I should not see "Low score"
