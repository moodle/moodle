@core @core_question
Feature: The questions in the question bank can be filtered by tags
  In order to find the questions I need
  As a teacher
  I want to filter the questions by tags

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
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | user     | questiontext    |
      | Test questions   | essay     | question 1 name | admin    | Question 1 text |
      | Test questions   | essay     | question 2 name | teacher1 | Question 2 text |
    And I am on the "question 1 name" "core_question > edit" page logged in as "teacher1"
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I am on the "question 2 name" "core_question > edit" page
    And I set the following fields to these values:
      | Tags | bar |
    And I press "id_submitbutton"

  @javascript
  Scenario: The questions can be filtered by tag
    When I apply question bank filter "Tag" with value "foo"
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"

  @javascript
  Scenario: Empty condition should not result in exception
    When I am on the "Course 1" "core_question > course question bank" page
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Test questions"
    When I click on "Add condition" "button"
    And I set the field "type" in the "Filter 2" "fieldset" to "Tag"
    And I click on "Apply filters" "button"
