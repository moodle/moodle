@qtype @qtype_description
Feature: Test creating a Description question
  As a teacher
  In order to test my students
  I need to be able to create a Description question

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

  Scenario: Create a Description question with Correct answer as False
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Description" question filling the form with:
      | Question name                      | description-001                                                |
      | Question text                      | Instructions about the following questions.                    |
      | General feedback                   | Why actually the field 'General feedback' used in this qytype? |
    Then I should see "description-001"
