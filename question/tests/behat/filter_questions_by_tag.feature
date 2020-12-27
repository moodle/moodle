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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration
    And I choose "Edit question" action for "question 1 name" in the question bank
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I choose "Edit question" action for "question 2 name" in the question bank
    And I set the following fields to these values:
      | Tags | bar |
    And I press "id_submitbutton"

  @javascript
  Scenario: The questions can be filtered by tag
    When I set the field "Filter by tags..." to "foo"
    And I press the enter key
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
