@core @core_question
Feature: The questions in the question bank can be selected in various ways
  In selected to do something for questions
  As a teacher
  I want to choose them to move, delete it.

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
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name              | user     | questiontext    |
      | Test questions   | essay     | A question 1 name | admin    | Question 1 text |
      | Test questions   | essay     | B question 2 name | teacher1 | Question 2 text |
      | Test questions   | numerical | C question 3 name | teacher1 | Question 3 text |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "teacher1"

  @javascript
  Scenario: The question text can be chosen all in the list of questions
    Given the field "Select all" matches value ""
    When I click on "Select all" "checkbox"
    And the field "A question 1 name" matches value "1"
    And the field "B question 2 name" matches value "1"
    And the field "C question 3 name" matches value "1"
    Then I click on "Deselect all" "checkbox"
    And the field "A question 1 name" matches value ""
    And the field "B question 2 name" matches value ""
    And the field "C question 3 name" matches value ""

  @javascript
  Scenario: The question text can be chosen in the list of questions
    Given the field "Select all" matches value ""
    When I click on "A question 1 name" "checkbox"
    Then the field "Select all" matches value ""
    And I click on "B question 2 name" "checkbox"
    And I click on "C question 3 name" "checkbox"
    And the field "Deselect all" matches value "1"

  @javascript
  Scenario: The action button can be disabled when the question not be chosen in the list of questions
    Given the field "Select all" matches value ""
    When I click on "With selected" "button"
    And I should not see "Delete"
    And I should not see "Move to..."
    And I click on "Select all" "checkbox"
    And I click on "With selected" "button"
    Then I should see question bulk action "move"
    And I should see question bulk action "deleteselected"
