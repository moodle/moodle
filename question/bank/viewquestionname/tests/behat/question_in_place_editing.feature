@qbank @qbank_viewquestionname @javascript
Feature: Use the qbank view page to edit question title using in place edit feature

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  Scenario: Question title can be changed from the question bank view
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    When I set the field "Edit question name" in the "First question" "table_row" to "Edited question"
    Then I should not see "First question"
    And I should see "Edited question"

  Scenario: Teacher without permission can not change the title from question bank view
    Given the following "role capability" exists:
      | role                    | editingteacher |
      | moodle/question:editall | prevent        |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    And I should see "First question"
    And "Edit question name" "field" should not exist
