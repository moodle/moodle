@core @core_question
Feature: The questions in the question bank can be filtered by combine various conditions
  In order to find the questions I need
  As a teacher
  I want to filter the questions by various conditions

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
      | activity | name    | intro           | course | idnumber |
      | qbank    | Qbank 1 | Question bank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name             |
      | Activity module | qbank1    | Test questions 1 |
      | Activity module | qbank1    | Test questions 2 |
      | Activity module | qbank1    | Test questions 3 |
    And the following "questions" exist:
      | questioncategory | qtype | name            | user     | questiontext    |
      | Test questions 1 | essay | question 1 name | teacher1 | Question 1 text |
      | Test questions 1 | essay | question 2 name | teacher1 | Question 2 text |
      | Test questions 2 | essay | question 3 name | teacher1 | Question 3 text |
      | Test questions 2 | essay | question 4 name | teacher1 | Question 4 text |
    And the following "core_question > Tags" exist:
      | question        | tag |
      | question 1 name | foo |
      | question 3 name | foo |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "teacher1"

  @javascript
  Scenario: The questions can be filtered by matching all conditions
    When I apply question bank filter "Category" with value "Test questions 1"
    And I change window size to "large"
    And I apply question bank filter "Tag" with value "foo"
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
    And I should not see "question 3 name" in the "categoryquestions" "table"
    And I should not see "question 4 name" in the "categoryquestions" "table"

  @javascript
  Scenario: Filters persist when the page is reloaded
    Given the following "questions" exist:
      | questioncategory | qtype | name                 | user     | questiontext | status |
      | Test questions 1 | essay | hidden question name | teacher1 | Hidden text  | hidden |
    And the following "core_question > Tags" exist:
      | question             | tag |
      | hidden question name | foo |
    And I apply question bank filter "Category" with value "Test questions 1"
    And I apply question bank filter "Tag" with value "foo"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I should see "question 1 name" in the "categoryquestions" "table"
    And I should see "hidden question name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
    And I should not see "question 3 name" in the "categoryquestions" "table"
    And I should not see "question 4 name" in the "categoryquestions" "table"
    When I reload the page
    Then I should see "Test questions 1 (2)" in the "Filter 1" "fieldset"
    And the field "Show hidden questions" matches value "Yes"
    And I should see "foo" in the "Filter 3" "fieldset"
    And I should see "question 1 name" in the "categoryquestions" "table"
    And I should see "hidden question name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
    And I should not see "question 3 name" in the "categoryquestions" "table"
    And I should not see "question 4 name" in the "categoryquestions" "table"

  @javascript
  Scenario: Filtered category should be kept when we create new question
    Given I apply question bank filter "Category" with value "Test questions 3"
    And I should not see "question 1 name"
    And I should not see "question 2 name"
    And I click on "Create a new question" "button"
    And I set the field "True/False" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | Category | Test questions 3 |
    And I set the following fields to these values:
      | Category      | Test questions 2  |
      | Question name | Question 3        |
      | Question text | T/F question text |
    When I press "id_submitbutton"
    Then I should see "Question 3"
    And I should see "question 3 name"
