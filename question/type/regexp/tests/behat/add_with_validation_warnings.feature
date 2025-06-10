@qtype @qtype_regexp
Feature: Test creating a Regexp question with validation warnings
  As a teacher
  In order to test my students
  I need to be able to create a Regexp question with successful validation

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

  @javascript
  Scenario: Create a Regexp question with validation warnings
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Regular expression short answer" question filling the form with:
      | Question name        | regularshortanswer-001                    |
      | Question text        | How much does a $800 computer cost with a reduction of $1? |
      | Default mark         | 1                                         |
      | Case sensitivity     | Yes, case must match                      |
      | id_answer_0          | $799                                      |
      | id_fraction_0        | 100%                                      |
      | id_feedback_0        | OK.                                       |
      | id_answer_1          | approximately $800                        |
      | id_fraction_1        | 90%                                       |
      | id_feedback_1        | Almost.                                   |
      | id_answer_2          | (\$798\|798 dollars))                     |
      | id_fraction_2        | 40%                                       |
      | id_feedback_2        | Not quite.                                |
    Then I should see "ERROR! In Answers with a grade > 0 these unescaped metacharacters are not allowed: . ^ $ * + { } \"
    And I should see "ERROR! Check your parentheses or square brackets!"
    Then I set the field with xpath "//input[contains(@id, 'id_answer_1')]" to "approximately \$800"
    Then I set the field with xpath "//input[contains(@id, 'id_answer_2')]" to "(\$798|798 dollars)"
    And I press "id_updatebutton"
    And I click on "Show/Hide alternate answers" "link"
    And I click on "id_showalternate" "button"
    And I should see "Answer 1 (100%)"
    And I should see "$799"
    And I should see "Answer 2 (90%)"
    And I should see "approximately \$800"
    And I should see "approximately $800"
    And I should see "Answer 3 (40%)"
    And I should see "(\$798|798 dollars)"
    And I should see "$798"
    And I should see "798 dollars"
    And I press "id_submitbutton"
    Then I should see "regularshortanswer-001"
