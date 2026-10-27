@qtype @qtype_calculated
Feature: Test editing a Calculated question
  As a teacher
  In order to update calculated questions for assessments
  I need to edit them and ensure the editing interface is fully accessible

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype      | name                   | template |
      | Test questions   | calculated | Calculated for editing | sum      |

  @javascript @accessibility
  Scenario: Edit a Calculated question and check accessibility
    When I am on the "Calculated for editing" "core_question > edit" page logged in as teacher
    Then the "#region-main" "css_element" should meet accessibility standards
