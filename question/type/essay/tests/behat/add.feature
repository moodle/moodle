@qtype @qtype_essay
Feature: Test creating an Essay question
  As a teacher
  In order to test my students
  I need to be able to create an Essay question

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  Scenario: Create an Essay question with Response format set to 'HTML editor'
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Essay" question filling the form with:
      | Question name            | essay-001                      |
      | Question text            | Write an essay with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | HTML editor                    |
    Then I should see "essay-001"

  Scenario: Create an Essay question with Response format set to 'HTML editor with the file picker'
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Essay" question filling the form with:
      | Question name            | essay-002                      |
      | Question text            | Write an essay with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | HTML editor                    |
    Then I should see "essay-002"
