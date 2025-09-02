@qtype @qtype_missingtype
Feature: Questions with invalid types should be clear and any actions which won't work should be disabled
  As a teacher
  In order to manage my questions
  I want to be able to clearly see which are invalid

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "questions" exist:
      | questioncategory    | qtype     | name         | user      | questiontext    |
      | Default for Qbank 1 | essay     | Question 1   | teacher1  | A text          |
      | Default for Qbank 1 | essay     | Question 2   | teacher1  | B text          |
    And question "Question 2" is changed to simulate being of an uninstalled type

  Scenario: Questions of invalid types should be highlighted and labelled as invalid
    Given I am on the "Qbank 1" "core_question > question bank" page logged in as "teacher1"
    Then I should see "Invalid question type: invalidqtype" in the table row containing "Question 2"
    And "//tr[contains(., 'Question 2')][contains(@class, 'table-danger')]" "xpath_element" should exist
