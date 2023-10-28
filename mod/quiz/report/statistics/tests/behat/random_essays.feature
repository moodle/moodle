@mod @mod_quiz @quiz @quiz_statistics
Feature: Robustness of the statistics calculations with random essays
  In order not to see errors
  As a teacher
  I need the statistics to work even if the quiz uses a random selection of essays

  Scenario: Statistics can be calculated even after a question type has been uninstalled
    Given the following "users" exist:
      | username |
      | teacher  |
      | student  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
      | student | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype  | template                | name                    | questiontext |
      | Test questions   | essay  | plain                   | Test question 1         |              |
      | Test questions   | essay  | plain                   | Test question 2         |              |
      | Test questions   | random |                         | Random (Test questions) | 0            |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question                | page |
      | Random (Test questions) | 1    |
    And user "student" has attempted "Quiz 1" with responses:
      | slot | response                   |
      |   1  | Here is my wonderful essay |

    When I am on the "Quiz 1" "mod_quiz > Statistics report" page logged in as teacher
    Then I should see "No attempts have been made at this quiz, or all attempts have questions that need manual grading."
