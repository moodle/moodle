@qtype @qtype_regexp
Feature: Preview a Regexp question
  As a teacher
  In order to check my Regexp questions will work for students
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
      | questioncategory | qtype  | name       | template |
      | Test questions   | regexp | regexp-001 | cat_bat_rat |
      | Test questions   | regexp | regexp-002 | frenchflag |

  @javascript @_switch_window
  Scenario: Preview a Regexp question with an inline input blank
    When I am on the "regexp-001" "core_question > preview" page logged in as teacher
    And I should see "Name an animal whose name consists of 3 letters and the middle letter is the vowel \"a\":"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I set the field with xpath "//div[@class='qtext']//input[contains(@id, '1_answer')]" to "cat"
    And I press "Check"
    Then I should see "The best answer."

  @javascript @_switch_window
  Scenario: Preview a Regexp question with correct answer
    When I am on the "regexp-002" "core_question > preview" page logged in as teacher
    And I should see "What are the colours of the French flag?"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "it's blue, white and red"
    And I press "Check"
    Then I should see "The best answer."
    And I should see "General feedback: OK"
    And I should see "The best correct answer is:"
    And I should see "it's blue, white and red"
    And I click on "Show/Hide alternate answers" "link"
    And I should see "The other accepted answers are:"
    And I should see "80%"
    And I should see "it's blue, white, red"
    And I should see "it is blue, white, red"
    And I should see "they are blue, white, red"
    And I should see "blue, white, red"

  @javascript @_switch_window
  Scenario: Preview a Regexp question with an acceptable answer
    When I am on the "regexp-002" "core_question > preview" page logged in as teacher
    And I should see "What are the colours of the French flag?"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "they are blue, white, red"
    And I press "Check"
    Then I should see "An acceptable answer."
    And I should see "General feedback: OK"
    And I should see "The best correct answer is:"
    And I should see "it's blue, white and red"
    And I click on "Show/Hide alternate answers" "link"
    And I should see "The other accepted answers are:"
    And I should see "80%"
    And I should see "it's blue, white, red"
    And I should see "it is blue, white, red"
    And I should see "they are blue, white, red"
    And I should see "blue, white, red"

  @javascript @_switch_window
  Scenario: Preview a Regexp question with missing words in the answer
    When I am on the "regexp-002" "core_question > preview" page logged in as teacher
    And I should see "What are the colours of the French flag?"
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
      | rightanswer | 0 |
    And I press "saverestart"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "it's white and red"
    And I press "Check"
    Then I should see "Misplaced words"
    And I should see "Missing blue!"
    And I press "Start again"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "it's black and orange"
    And I press "Check"
    Then I should see "Wrong words Misplaced words"
    And I should see "You have not even found one of the colors of the French flag!"
    And I press "Start again"
    And I set the field with xpath "//div[@class='answer']//input[contains(@id, '1_answer')]" to "it's blue and orange"
    And I press "Check"
    Then I should see "Wrong words Misplaced words"
    And I should see "You have not found all the colors of the French flag!"
