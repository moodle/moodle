@qbank @qbank_viewcreator
Feature: Question bank Modified by column
  In order to help keep track of the changes of the question version in the question bank
  As a teacher
  I need to know who modified the question and when the modification made in the question bank

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher1 | 1 | teacher1@example.com |
      | teacher2 | Teacher2 | 2 | teacher2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  @javascript
  Scenario: Enable/disable modified by column from the base view
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "View creator"
    When I click on "Disable" "link" in the "View creator" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    Then I should not see "Modified by"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "View creator" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    Then I should see "Modified by"

  @javascript
  Scenario: Editing a question shows the modifier of the question
    And I log in as "teacher1"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    And I should see "First question"
    And I choose "Edit question" action for "First question" in the question bank
    And I should see "Version 1"
    And I set the field "id_name" to "Renamed question v2"
    And I set the field "id_questiontext" to "edited question"
    And I press "id_submitbutton"
    And I should see "Teacher1"
    And I log in as "teacher2"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I set the field "Filter type" to "Category"
    And I set the field "Category" to "Test questions"
    And I press "Apply filters"
    And I choose "Edit question" action for "Renamed question v2" in the question bank
    Then I should see "Version 2"
    And I set the field "id_name" to "Renamed question v3"
    And I set the field "id_questiontext" to "edited question v3"
    And I press "id_submitbutton"
    And I should not see "Teacher1"
    And I should see "Teacher2"
