@mod @mod_quiz
Feature: Attempt a quiz in a sequential mode
  As a student I should not be able to see the previous questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
      | teacher  | Teacher   | One      | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
      | teacher | C1     | teacher |
    And the following "activities" exist:
      | activity | name    | intro              | course | idnumber | preferredbehaviour | navmethod  |
      | quiz     | Quiz 1  | Quiz 1 description | C1     | quiz1    | immediatefeedback  | sequential |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
      | Test questions   | truefalse | TF2  | Second question |
      | Test questions   | truefalse | TF3  | Third question  |
      | Test questions   | truefalse | TF4  | Fourth question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 1               |
      | TF2      | 2    | 1               |
      | TF3      | 3    | 1               |
      | TF4      | 4    | 1               |

  @javascript
  Scenario Outline: As a student I should not be able to navigate out of sequence if sequential navigation is on.
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I should see "First question"
    When I am on the "Quiz 1 > student > Attempt 1 > <pagenumber>" "mod_quiz > Attempt view" page
    And I should see "<canseequestion>"
    Then I should not see "<cannotseequestion>"
    Examples:
      | pagenumber | canseequestion  | cannotseequestion |
      | 1          | First question  | Second question   |
      | 2          | Second question | First question    |
      | 4          | First question  | Fourth question   |

  @javascript
  Scenario: As a student I should not be able to navigate out of sequence by opening new windows on the same quiz.
    Given the following config values are set as admin:
      | config         | value | plugin |
      | autosaveperiod | 60    | quiz   |
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I should see "First question"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "Next page" "button"
    When I am on the "Quiz 1 > student > Attempt 1 > 3" "mod_quiz > Attempt view" page
    And I click on "True" "radio" in the "Third question" "question"
    And I should see "Third question"
    And I click on "Next page" "button"
    And I am on the "Quiz 1 > student > Attempt 1 > 1" "mod_quiz > Attempt view" page
    Then I should see "Fourth question"

  @javascript
  Scenario: As a student I should not be able to save my data by opening a given page out of sequence.
    Given the following config values are set as admin:
      | config         | value | plugin |
      | autosaveperiod | 1     | quiz   |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I am on the "Quiz 1 > student > Attempt 1 > 2" "mod_quiz > Attempt view" page
    And I should see "Second question"
    And I click on "True" "radio" in the "Second question" "question"
    And I wait "2" seconds
    And I am on the "Quiz 1 > student > Attempt 1 > 1" "mod_quiz > Attempt view" page
    Then I should see "Second question"

  @javascript
  Scenario: As a student I can review question I have finished in any order
    Given user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
      |   3  | False    |
      |   4  | False    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I follow "Review"
    And I am on the "Quiz 1 > student > Attempt 1 > 3" "mod_quiz > Attempt view" page
    And I should see "Third question"
    And I am on the "Quiz 1 > student > Attempt 1 > 2" "mod_quiz > Attempt view" page
    Then I should see "Second question"
