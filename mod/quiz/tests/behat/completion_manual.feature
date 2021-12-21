@mod @mod_quiz @core_completion
Feature: Manually complete a quiz
  In order to meet manual quiz completion requirements
  As a student
  I need to be able to view and modify my quiz manual completion status

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | grade_item_advanced | hiddenuntil |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | completion |
      | quiz     | Test quiz name | C1     | quiz1    | 1          |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |

  @javascript
  Scenario: Use manual completion
    Given I am on the "Test quiz name" "quiz activity" page logged in as teacher1
    And the manual completion button for "Test quiz name" should be disabled
    And I log out
    # Student view.
    When I am on the "Test quiz name" "quiz activity" page logged in as student1
    Then the manual completion button of "Test quiz name" is displayed as "Mark as done"
    And I toggle the manual completion state of "Test quiz name"
    And the manual completion button of "Test quiz name" is displayed as "Done"
