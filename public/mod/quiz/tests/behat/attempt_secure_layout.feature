@mod @mod_quiz
Feature: Attempt a quiz in secure layout
  As a student
  In order to demonstrate what I know
  I need to be able to attempt quizzes in secure layout

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | grade | navmethod | browsersecurity | showuserpicture |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    | 100   | free      | securewindow    | 2               |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
      | Test questions   | truefalse | TF2  | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |

  @javascript
  Scenario: Large user image in the quiz navigation in secure layout
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I switch to a second window
    Then "Student One" "link" should not exist in the "Quiz navigation" "block"

  Scenario: A quiz page on the secure layout shows both the course name and the quiz name
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I should see "Quiz 1"
    And I should see "Quiz 1 description"
    When I press "Attempt quiz"
    Then I should see "Course 1"
    And I should see "Quiz 1"
    But I should not see "Quiz 1 description"
