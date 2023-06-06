@mod @mod_lesson
Feature: In a lesson activity, students can not re-attempt a question more than the allowed amount
  In order to check a lesson question can not be attempted more than the allowed amount
  As a student I need to check I cannot reattempt a question more than I should be allowed

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
    And the following "activity" exists:
      | activity                 | lesson                  |
      | course                   | C1                      |
      | idnumber                 | 0001                    |
      | name                     | Test lesson name        |
      | retake                   | 1                       |
      | minquestions             | 3                       |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | truefalse | True/false question 1 | The earth is round.       |
      | Test lesson name | truefalse | True/false question 2 | Kermit is a frog          |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | truefalse | True/false question 3 | Paper is made from trees. |
      | Test lesson name | content   | Third page name       | Third page contents       |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response | jumpto        | score |
      | First page name       | Next page     |          | Next page     | 0     |
      | Second page name      | Previous page |          | Previous page | 0     |
      | Second page name      | Next page     |          | Next page     | 0     |
      | True/false question 1 | True          | Correct  | Next page     | 1     |
      | True/false question 1 | False         | Wrong    | This page     | 0     |
      | True/false question 2 | True          | Correct  | Next page     | 1     |
      | True/false question 2 | False         | Wrong    | This page     | 0     |
      | True/false question 3 | True          | Correct  | Next page     | 1     |
      | True/false question 3 | False         | Wrong    | This page     | 0     |
      | Third page name       | Previous page |          | Previous page | 0     |
      | Third page name       | Next page     |          | Next page     | 0     |

  Scenario: Check that we can leave a quiz and when we re-enter we can not re-attempt the question again
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False| 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I am on the "Test lesson name" "lesson activity" page
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False| 1 |
    When I press "Submit"
    Then I should see "Maximum number of attempts reached - Moving to next page"

  @javascript @_bug_phantomjs
  Scenario: Check that we can not click back on the browser at the last quiz result page and re-attempt the last question to get full marks
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | True| 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True| 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press "Continue"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 2 (out of 3)"
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Third page contents"
    When I press "Next page"
    Then I should see "Number of questions answered: 1 (You should answer at least 3)"

  @javascript
  Scenario: Check that we can not click back on the browser and re-attempt a question
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | True | 1 |
    When I press "Submit"
    Then I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 3)"
