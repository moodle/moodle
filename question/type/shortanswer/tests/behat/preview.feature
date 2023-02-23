@qtype @qtype_shortanswer
Feature: Preview a Short answer question
  As a teacher
  In order to check my Short answer questions will work for students
  I need to preview them

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
      | questioncategory | qtype       | name            | template |
      | Test questions   | shortanswer | shortanswer-001 | frogtoad |

  @javascript @_switch_window
  Scenario: Preview a Short answer question with correct answer
    When I am on the "shortanswer-001" "core_question > preview" page logged in as teacher
    And I should see "Name an amphibian:"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Start again with these options"
    And I set the field with xpath "//div[@class='qtext']//input[contains(@id, '1_answer')]" to "frog"
    And I press "Check"
    Then I should see "Frog is a very good answer."
    And I should see "Generalfeedback: frog or toad would have been OK."
    And I should see "The correct answer is: frog"

  @javascript @_switch_window
  Scenario: Preview a Short answer question with almost correct answer
    When I am on the "shortanswer-001" "core_question > preview" page logged in as teacher
    And I should see "Name an amphibian:"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Start again with these options"
    And I set the field with xpath "//div[@class='qtext']//input[contains(@id, '1_answer')]" to "toad"
    And I press "Check"
    Then I should see "Toad is an OK good answer."
    And I should see "Generalfeedback: frog or toad would have been OK."
    And I should see "The correct answer is: frog"

  @javascript @_switch_window
  Scenario: Preview a Short answer question with incorrect answer
    When I am on the "shortanswer-001" "core_question > preview" page logged in as teacher
    And I should see "Name an amphibian:"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Start again with these options"
    And I set the field with xpath "//div[@class='qtext']//input[contains(@id, '1_answer')]" to "cat"
    And I press "Check"
    Then I should see "That is a bad answer."
    And I should see "Generalfeedback: frog or toad would have been OK."
    And I should see "The correct answer is: frog"
