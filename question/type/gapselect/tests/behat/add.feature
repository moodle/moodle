@qtype @qtype_gapselect
Feature: Test creating a Select missing words question
  As a teacher
  In order to test my students
  I need to be able to create Select missing words questions

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
  Scenario: Create a Select missing words question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Select missing words" question filling the form with:
      | Question name             | Select missing words 001   |
      | Question text             | The [[1]] [[2]] on the [[3]]. |
      | General feedback          | The cat sat on the mat.       |
      | id_shuffleanswers         | 1                             |
      | id_choices_0_answer       | cat                           |
      | id_choices_1_answer       | sat                           |
      | id_choices_2_answer       | mat                           |
      | id_choices_3_answer       | dog                           |
      | id_choices_4_answer       | table                         |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Select missing words 001"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "item_qtype_gapselect" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | id_shuffleanswers | 1 |

  Scenario: Edit a Select missing words question with 2 choice and should not have empty choice.
    Given I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Select missing words" question filling the form with:
      | Question name            | Select missing words 002    |
      | Question text            | The [[1]] [[2]] on the mat. |
      | General feedback         | The cat sat on the mat.     |
      | id_shuffleanswers        | 1                           |
      | id_choices_0_answer      | cat                         |
      | id_choices_1_answer      | sat                         |
      | id_choices_2_answer      | dog                         |
      | id_choices_2_choicegroup | 2                           |
      | id_choices_3_answer      | stand                       |
      | id_choices_3_choicegroup | 2                           |
      | Hint 1                   | First hint                  |
      | Hint 2                   | Second hint                 |
    When I choose "Edit question" action for "Select missing words 002" in the question bank
    And the following fields match these values:
      | Question name            | Select missing words 002    |
      | Question text            | The [[1]] [[2]] on the mat. |
      | General feedback         | The cat sat on the mat.     |
      | id_shuffleanswers        | 1                           |
      | id_choices_0_answer      | cat                         |
      | id_choices_1_answer      | sat                         |
      | id_choices_2_answer      | dog                         |
      | id_choices_2_choicegroup | 2                           |
      | id_choices_3_answer      | stand                       |
      | id_choices_3_choicegroup | 2                           |
      | Hint 1                   | First hint                  |
      | Hint 2                   | Second hint                 |
    Then I should not see "Choice [[5]]"
