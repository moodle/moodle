@qtype @qtype_wq @qtype_multichoicewiris
Feature: Test editing a Multichoice wiris question
  As a teacher
  In order to be able to update my Multichoice wiris question
  I need to edit them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype            | name                  | template             |
      | Test questions   | multichoicewiris | multichoice-wiris-001 | four_of_five_science |

  @javascript @_switch_window
  Scenario: Edit a multichoice wiris question
    When I am on the "multichoice-wiris-001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Edited multichoice-wiris-001 name |
    And I press "id_submitbutton"
    Then I should see "Edited multichoice-wiris-001 name"
    When I choose "Edit question" action for "Edited multichoice-wiris-001" in the question bank
    And I press "Blanks for 3 more choices"
    And I set the following fields to these values:
      | id_answer_5      | 57                                        |
      | id_fraction_5    | 20%                                       |
      | id_feedback_5    | 57 is odd                                 |
      | id_answer_6      | 6                                         |
      | id_fraction_6    | None                                      |
      | id_feedback_6    | 6 is even                                 |
      | id_answer_7      | 88                                        |
      | id_fraction_7    | None                                      |
      | id_feedback_7    | 88 is even                                |
      | id_fraction_0    | 20%                                       |
      | id_fraction_1    | 20%                                       |
      | id_fraction_2    | 20%                                       |
      | id_fraction_3    | 20%                                       |
      | General feedback | The odd numbers are 57, #t1, #t2 and #t4. |
    And I press "id_submitbutton"
    Then I should see "Edited multichoice-wiris-001"
