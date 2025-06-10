@qtype @qtype_wq @qtype_truefalsewiris
Feature: A student can answer a Wiris Truefalse question type
  In order to answer the question
  As a student
  I need to select the correct answer
  Background:
    Given the "wiris" filter is "on"
    Given the "mathjaxloader" filter is "disabled"
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Default for C1 |
    And the following "questions" exist:
      | questioncategory | qtype          | name            | template         |
      | Default for C1   | truefalsewiris | Truefalse wiris | sciencetruefalse |

  @javascript
  Scenario: A student executes a truefalsewiris
    When I am on the "Truefalse wiris" "core_question > preview" page logged in as teacher
    And I press "Submit and finish"
    Then I should see "The correct answer is"
