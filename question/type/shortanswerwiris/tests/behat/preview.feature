@qtype @qtype_wq @qtype_shortanswerwiris
Feature: A student can answer a Wiris Short Answer question type
  In order to answer the question
  As a student
  I need to fill in the short answer field

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
      | questioncategory | qtype            | name               | template             |
      | Default for C1   | shortanswerwiris | shortanswerwiris-001 | scienceshortanswer |

  Scenario: A student executes a shortanswer wiris question
    When I am on the "shortanswerwiris-001" "core_question > preview" page logged in as teacher
    And I press "Submit and finish"
    Then I should see "The correct answer is"

  Scenario: Shortanswer readonly check
    When I am on the "shortanswerwiris-001" "core_question > preview" page logged in as teacher
    And I click on "Fill in correct responses" "button"
    And I click on "Submit and finish" "button"
