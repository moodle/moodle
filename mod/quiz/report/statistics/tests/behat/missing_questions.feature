@mod @mod_quiz @quiz @quiz_statistics
Feature: Robustness of the statistics calculations with missing qusetions
  In order to be able to install and uninstall plugins
  As a teacher
  I need the statistics to work even if a question type has been uninstalled

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
      | questioncategory | qtype     | name            |
      | Test questions   | truefalse | Test question 1 |
      | Test questions   | truefalse | Test question 2 |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question        | page |
      | Test question 1 | 1    |
      | Test question 2 | 1    |
    And user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
    And question "Test question 1" is changed to simulate being of an uninstalled type
    And question "Test question 2" no longer exists in the database

    When I am on the "Quiz 1" "mod_quiz > Statistics report" page logged in as teacher

    Then I should see "Quiz structure analysis"
    And "1" row "Question name" column of "questionstatistics" table should contain "Missing question"
    And "1" row "Attempts" column of "questionstatistics" table should contain "1"
    And "1" row "Intended weight" column of "questionstatistics" table should contain "50.00%"
    And "2" row "Question name" column of "questionstatistics" table should contain "Missing question"
    And "2" row "Attempts" column of "questionstatistics" table should contain "1"
    And "2" row "Intended weight" column of "questionstatistics" table should contain "50.00%"
