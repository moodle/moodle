@mod @mod_quiz
Feature: Set a quiz to be interactive with multiple tries
  In order to attempt an interactive quiz multiple times
  As a teacher
  I should be able to set how questions behave to interactive with multiple tries

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    | template    | hint1  | hint2  | shuffleanswers |
      | Test questions   | multichoice | MC1  | First question  | one_of_four | Hint 1 | Hint 2 | 0              |
      | Test questions   | multichoice | MC2  | Second question | one_of_four | Hint 1 | Hint 2 | 0              |
    And the following "activities" exist:
      | activity | name   | course | preferredbehaviour     | specificfeedbackduring |
      | quiz     | Quiz 1 | C1     | interactive            | 1                      |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | MC1      | 1    |
      | MC2      | 2    |

  @javascript
  Scenario: Attempt an interactive quiz with multiple tries
    Given I am on the "Quiz 1" "quiz activity" page logged in as student1
    And I press "Attempt quiz"
    # Answer the question incorrectly.
    And I click on "Two" "qtype_multichoice > Answer" in the "Question 1" "question"
    When I press "Check"
    # Confirm that correct feedback is displayed.
    Then I should see "That is not right at all."
    And I should see "Hint 1."
    # Confirm answer cannot be changed after checking your answer.
    And I should not see "Clear my choice"
    And "Check" "button" should not be visible
    # Attempt question again.
    And I press "Try again"
    And I should see "Clear my choice"
    # Answer the question correctly.
    And I click on "One" "qtype_multichoice > Answer" in the "Question 1" "question"
    And I press "Check"
    # Confirm correct feedback is displayed.
    And I should see "Well done!"
    And I should see "The correct answer is: One"
    And I should not see "Hint 1."
    And "Try again" "button" should not be visible
    And I press "Next page"
    # Answer question incorrectly.
    And I click on "Two" "qtype_multichoice > Answer" in the "Question 2" "question"
    And I press "Check"
    # Confirm Hint 1 is displayed.
    And I should see "Hint 1."
    And I press "Try again"
    # Answer question incorrectly again.
    And I click on "Three" "qtype_multichoice > Answer" in the "Question 2" "question"
    And I press "Check"
    # Confirm Hint 2 is displayed.
    And I should see "Hint 2."
    And I press "Try again"
    # Answer question incorrectly again.
    And I click on "Four" "qtype_multichoice > Answer" in the "Question 2" "question"
    And I press "Check"
    # Confirm you can no longer re-attempt the question.
    And I should not see "Try again"
