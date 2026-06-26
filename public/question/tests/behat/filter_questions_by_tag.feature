@core @core_question
Feature: The questions in the question bank can be filtered by tags
    In order to find the questions I need
    As a teacher
    I want to filter the questions by tags

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name    | intro           | course | idnumber |
      | qbank    | Qbank 2 | Question bank 2 | C1     | qbank2   |
      | qbank    | Qbank 1 | Question bank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name            | user     | questiontext    | tags |
      | Test questions   | essay | question 1 name | admin    | Question 1 text | foo  |
      | Test questions   | essay | question 2 name | teacher1 | Question 2 text | bar  |
    And I am on the "qbank1" "core_question > question bank" page logged in as "teacher1"

  @javascript
  Scenario: The questions can be filtered by tag
    When I apply question bank filter "Category" with value "Test questions"
    And I apply question bank filter "Tag" with value "foo"
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"

  @javascript
  Scenario: Empty condition should not result in exception
    When I apply question bank filter "Category" with value "Test questions"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Test questions"
    Then I click on "Add condition" "button"
    And I set the field "type" in the "Filter 2" "fieldset" to "Tag"
    And I click on "Apply filters" "button"
