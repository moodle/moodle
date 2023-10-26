@qtype @qtype_gapselect @_switch_window
Feature: Test all the basic functionality of this question type
  In order to evaluate students responses, As a teacher I need to
  create and preview gapselect (Select missing words) questions.

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
  Scenario: Create, edit then preview a gapselect question.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher

    # Create a new question.
    And I add a "Select missing words" question filling the form with:
      | Question name            | Select missing words 001      |
      | Question text            | The [[1]] [[2]] on the [[3]]. |
      | General feedback         | The cat sat on the mat.       |
      | id_choices_0_answer      | cat                           |
      | id_choices_1_answer      | sat                           |
      | id_choices_1_choicegroup | 2                             |
      | id_choices_2_answer      | mat                           |
      | id_choices_3_answer      | dog                           |
      | id_choices_4_answer      | table                         |
      | id_choices_5_answer      | ran                           |
      | id_choices_5_choicegroup | 2                             |
      | Hint 1                   | First hint                    |
      | Hint 2                   | Second hint                   |
    Then I should see "Select missing words 001"

    # Preview it.
    And I choose "Preview" action for "Select missing words 001" in the question bank

    # Gaps (drop-down menus) do not have labels. ids and names are generated
    # dynamically and therefore not reliable, i.e. this is an accessibility bug
    # which need to be fixed at some stage. Meanwhile, I use the ids and increment
    # them as appropriate (e.g.: q3_1_p1 become q4_1_p1, etc.).

    # Set display and behaviour options
    And I set the following fields to these values:
      | How questions behave | Interactive with multiple tries |
      | Marked out of        | 3                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown                           |
      | Right answer         | Shown                           |
    And I press "Start again with these options"

    # Answer question correctly
    And I set space "1" to "cat" in the select missing words question
    And I set space "2" to "sat" in the select missing words question
    And I set space "3" to "mat" in the select missing words question
    And I press "Check"
    And I should see "Your answer is correct"
    And I should see "The cat sat on the mat"
    And I should see "The correct answer is: The [cat] [sat] on the [mat]."
    And I press "Start again"

    # Answer question partially correct twice and then correct
    And I set space "1" to "cat" in the select missing words question
    And I set space "2" to "sat" in the select missing words question
    And I set space "3" to "dog" in the select missing words question
    And I press "Check"
    And I should see "Your answer is partially correct"
    And I should see "First hint"

    And I press "Try again"
    And I set space "3" to "table" in the select missing words question
    And I press "Check"
    And I should see "Your answer is partially correct"
    And I should see "Second hint"

    And I press "Try again"
    And I set space "3" to "mat" in the select missing words question
    And I press "Check"
    And I should see "Your answer is correct"
    And I should see "The cat sat on the mat"
    And I should see "The correct answer is: The [cat] [sat] on the [mat]."

    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "Start again with these options"

    # Answer question correctly
    And I press "Check"
    And I should see "Please put an answer in each box."
    And I set space "1" to "cat" in the select missing words question
    And I set space "2" to "sat" in the select missing words question
    And I set space "3" to "mat" in the select missing words question
    And I press "Check"
    Then I should see "Your answer is correct"
    And I should see "The cat sat on the mat"
    And I should see "The correct answer is: The [cat] [sat] on the [mat]."
    And I press "Start again"

    # Answer question partially correct
    And I set space "1" to "dog" in the select missing words question
    And I set space "2" to "sat" in the select missing words question
    And I set space "3" to "cat" in the select missing words question
    And I press "Check"
    And I should see "Your answer is partially correct"
    And I should see "You have correctly selected 1."
    And I should see "The cat sat on the mat"
    And I should see "The correct answer is: The [cat] [sat] on the [mat]."
    And I press "Start again"

    # Answer question incorrectly
    And I set space "1" to "mat" in the select missing words question
    And I set space "2" to "ran" in the select missing words question
    And I set space "3" to "table" in the select missing words question
    And I press "Check"
    And I should see "Your answer is incorrect"
    And I should see "The cat sat on the mat"
    And I should see "The correct answer is: The [cat] [sat] on the [mat]."
    And I press "Close preview"

    # Backup the course and restore it.
    And I log out
    And I log in as "admin"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I am on the "Course 2 copy 1" "core_question > course question bank" page
    And I should see "Select missing words 001"

    # Edit the copy and verify the form field contents.
    And I choose "Edit question" action for "Select missing words 001" in the question bank
    And the following fields match these values:
      | Question name            | Select missing words 001      |
      | Question text            | The [[1]] [[2]] on the [[3]]. |
      | General feedback         | The cat sat on the mat.       |
      | id_choices_0_answer      | cat                           |
      | id_choices_1_answer      | sat                           |
      | id_choices_1_choicegroup | 2                             |
      | id_choices_2_answer      | mat                           |
      | id_choices_3_answer      | dog                           |
      | id_choices_4_answer      | table                         |
      | id_choices_5_answer      | ran                           |
      | id_choices_5_choicegroup | 2                             |
      | Hint 1                   | First hint                    |
      | Hint 2                   | Second hint                   |
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    And I should see "Edited question name"
