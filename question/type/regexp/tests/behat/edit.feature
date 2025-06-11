@qtype @qtype_regexp
Feature: Test editing a Regexp question
  As a teacher
  In order to be able to update my Regexp questions
  I need to edit them

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
      | questioncategory | qtype  | name                   | template   |
      | Test questions   | regexp | regexp-001 for editing | frenchflag |

  @javascript @_switch_window
  Scenario: Edit a Regexp question
    When I am on the "regexp-001 for editing" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question text | What are the colours of the French flag? |
      | Question name | |
    And I press "id_submitbutton"
    And I should see "You must supply a value here."
    And I set the following fields to these values:
      | Question name | Edited regexp-001 name |
    And I press "id_submitbutton"
    Then I should see "Edited regexp-001 name"
    And I choose "Edit question" action for "Edited regexp-001" in the question bank
    And I set the following fields to these values:
      | id_answer_2          | --.*blue.*     |
      | id_fraction_2        | None             |
      | id_feedback_2        | Missing blue!. |
    And I press "id_submitbutton"
    And I should see "Edited regexp-001 name"
    And I choose "Preview" action for "Edited regexp-001" in the question bank
    And I should see "What are the colours of the French flag?"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Save preview options and start again"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "white"
    And I press "Check"
    And I should see "Missing blue!"
    And I should see "General feedback:"
    And I should see "The best correct answer is:"
