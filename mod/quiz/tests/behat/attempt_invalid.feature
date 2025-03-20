@mod_quiz
Feature: A quiz with invalid question types should not be able to be attempted
  As a teacher
  If my quiz has questions with invalid types
  I want my students to be unable to attempt the quiz until it is fixed

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student  | C1     | student |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name         | user   | questiontext    |
      | Test questions   | essay     | Question 1   | admin  | A text          |
      | Test questions   | essay     | Question 2   | admin  | B text          |
    And the following "activities" exist:
      | activity | name    | intro              | course | idnumber | grade | navmethod  |
      | quiz     | Quiz 1  | Quiz 1 description | C1     | quiz1    | 100   | free       |
    And quiz "Quiz 1" contains the following questions:
      | question        | page | maxmark |
      | Question 1      | 1    |         |
      | Question 2      | 1    |         |
    And question "Question 2" is changed to simulate being of an uninstalled type

  @javascript
  Scenario: Quiz with invalid questions should disable attempts
    Given I am logged in as "student"
    When I am on the "Quiz 1" "mod_quiz > View" page
    Then I should see "This quiz has questions with invalid types"
    And I should not see "Attempt quiz"
