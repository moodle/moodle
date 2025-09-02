@qbank @qbank_viewcreator @javascript
Feature: Filter questions by modifier name
  As a teacher
  In order to organise my questions
  I want to filter the list of questions by modifier name

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | firstnamephonetic | lastnamephonetic | middlename | alternatename |
      | aa       | Aaron     | Aaronson | Aron              | Aronsun          | Andrew     | Andy          |
      | bb       | Bob       | Bobson   |                   |                  |            |               |
      | cc       | Clare     | Clareson |                   |                  |            |               |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "course enrolments" exist:
      | course | user | role           |
      | C1     | aa   | editingteacher |
      | C1     | bb   | editingteacher |
      | C1     | cc   | editingteacher |
    And the following "questions" exist:
      | qtype     | questioncategory | name            |
      | truefalse | Test questions   | First question  |
      | truefalse | Test questions   | Second question |
      | truefalse | Test questions   | Third question  |
    And the following "core_question > updated questions" exist:
      | questioncategory | question        | name            | modifiedbyuser |
      | Test questions   | First question  | First question  | aa             |
      | Test questions   | Second question | Second question | bb             |
      | Test questions   | Third question  | Third question  | cc             |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "First question"
    And I should see "Second question"
    And I should see "Third question"

  Scenario: Filter by a single word
    When I apply question bank filter "Modified by" with value "Aaron"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"

  Scenario: Filter by any word
    When I apply question bank filter "Modified by" with value "Aaron, Clare"
    Then I should see "First question"
    And I should not see "Second question"
    And I should see "Third question"

  Scenario: Filter by all words
    When I add question bank filter "Modified by"
    And I set the field "Modified by" to "son, Aar"
    And I set the field "Match" in the "Filter 3" "fieldset" to "All"
    And I press "Apply filters"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"

  Scenario: Filter by additional name fields
    When I apply question bank filter "Modified by" with value "Aron"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"
    When I apply question bank filter "Modified by" with value "sun"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"
    When I apply question bank filter "Modified by" with value "drew"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"
    When I apply question bank filter "Modified by" with value "Andy"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"

  Scenario: Exclude names by filter
    When I add question bank filter "Modified by"
    And I set the field "Modified by" to "Aron, Clare"
    And I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I press "Apply filters"
    Then I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
