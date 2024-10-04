@mod @mod_quiz @quiz @quiz_overview
Feature: View attempt states
  In order to see how students are progressing through the quiz
  As a teacher
  I need to see different attempt states on the overview report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext         |
      | Test questions   | description | Intro | Welcome to this quiz |
      | Test questions   | truefalse   | TF1   | First question       |
      | Test questions   | truefalse   | TF2   | Second question      |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | Intro    | 1    |         |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |

  Scenario: View attempts in different states
    Given quiz "Quiz 1" has pre-created attempts
    And user "student1" has started an attempt at quiz "Quiz 1"
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      |   2  | True     |
      |   3  | False    |
    When I am on the "Quiz 1" "mod_quiz > Grades report" page logged in as "teacher 1"
    Then the following should exist in the "attempts" table:
      | Email address        | Status      | Started                      | Completed                    | Grade/100.00 |
      | student1@example.com | In progress | ## now ##%d %B %Y %I:%M %p## | -                            | -            |
      | student2@example.com | Finished    | ## now ##%d %B %Y %I:%M %p## | ## now ##%d %B %Y %I:%M %p## | 25.00        |
      | student3@example.com | Not started | -                            | -                            | -            |
