@mod @mod_quiz @core_completion
Feature: Ensure saving a quiz does not modify the completion settings.
  In order to reliably use completion
  As a teacher
  I need to be able to update the quiz
  without changing the completion settings.

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
    And the following "activity" exists:
      | activity                     | quiz      |
      | course                       | C1        |
      | idnumber                     | quiz1     |
      | name                         | Test quiz |
      | section                      | 1         |
      | attempts                     | 2         |
      | gradepass                    | 5.00      |
      | completion                   | 2         |
      | completionview               | 0         |
      | completionusegrade           | 1         |
      | completionpassgrade          | 1         |
      | completionattemptsexhausted  | 1         |
    And quiz "Test quiz" contains the following questions:
      | question       | page |
      | First question | 1    |
    And user "student1" has attempted "Test quiz" with responses:
      | slot | response |
      |   1  | True     |

  Scenario: Ensure saving quiz activty does not change completion settings
    Given I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher1"
    When I navigate to "Settings" in current page administration
    Then the "completionattemptsexhausted" "field" should be disabled
    And the field "completionattemptsexhausted" matches value "1"
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    And the "completionattemptsexhausted" "field" should be disabled
    And the field "completionattemptsexhausted" matches value "1"
