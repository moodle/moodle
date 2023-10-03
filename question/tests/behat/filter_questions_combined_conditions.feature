@core @core_question
Feature: The questions in the question bank can be filtered by combine various conditions
  In order to find the questions I need
  As a teacher
  I want to filter the questions by various conditions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name            |
      | Course       | C1        | Test questions 1|
      | Course       | C1        | Test questions 2|
    And the following "questions" exist:
      | questioncategory | qtype     | name            | user     | questiontext    |
      | Test questions 1 | essay     | question 1 name | teacher1 | Question 1 text |
      | Test questions 1 | essay     | question 2 name | teacher1 | Question 2 text |
      | Test questions 2 | essay     | question 3 name | teacher1 | Question 3 text |
      | Test questions 2 | essay     | question 4 name | teacher1 | Question 4 text |
    And the following "core_question > Tags" exist:
      | question        | tag |
      | question 1 name | foo |
      | question 3 name | foo |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"

  @javascript
  Scenario: The questions can be filtered by matching all conditions
    When I apply question bank filter "Category" with value "Test questions 1"
    And I apply question bank filter "Tag" with value "foo"
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
    And I should not see "question 3 name" in the "categoryquestions" "table"
    And I should not see "question 4 name" in the "categoryquestions" "table"

  @javascript
  Scenario: Filters persist when the page is reloaded
    Given the following "questions" exist:
      | questioncategory | qtype     | name                 | user     | questiontext | status |
      | Test questions 1 | essay     | hidden question name | teacher1 | Hidden text  | hidden |
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
