@mod @mod_lesson @core_grades @core_form
Feature: Using the lesson activities which support point scale
  validate if we can change the maximum grade when users are graded
  As a teacher
  I need to know whether I can not edit value of Maximum grade input field

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | course      | C1                     |
      | activity    | lesson                 |
      | name        | Test lesson name       |
      | idnumber    | lesson1                |

  @javascript
  Scenario: Lesson rescale grade should not be possible when users are graded
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 2? |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | End of lesson |
      | id_enableotheranswers | 1 |
      | id_jumpto_6 | Next page |
    And I press "Save page"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I set the field "Your answer" to "5"
    And I press "Submit"
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    Then the "Maximum grade" "field" should be disabled
