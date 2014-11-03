@ou @ou_vle @qtype @qtype_ddwtos
Feature: Test all the basic functionality of this question type
  In order to evaluate students responses, As a teacher I need to
  create and preview ddwtos (Drag and drop into text) questions.

    # Due to complexity and since the JavaScript code needs to be converted at some stage,
    # we are not going to test attempting this qtype. However, we will do all other
    # functionality, such as creating the question preview it and seeing the
    # correct information on the preview string as well as backing-up and restoring
    # the course containing this qtype.

    # Another way to test attempting this qtype while previewing it, is to write a
    # customised step for tabbing through place-holders and another customised
    # step for making use of arrow keys in order to go through the list of choices.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Create, edit then preview a gapselect question.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

    # Create a new question.
    And I add a "Drag and drop into text" question filling the form with:
      | Question name             | Drag and drop into text 001   |
      | Question text             | The [[1]] [[2]] on the [[3]]. |
      | General feedback          | The cat sat on the mat.       |
      | id_choices_0_answer       | cat                           |
      | id_choices_1_answer       | sat                           |
      | id_choices_2_answer       | mat                           |
      | id_choices_3_answer       | dog                           |
      | id_choices_4_answer       | table                         |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Drag and drop into text 001"

    # Preview it.
    When I click on "Preview" "link" in the "Drag and drop into text 001" "table_row"
    And I switch to "questionpreview" window
    Then I should see "Preview question: Drag and drop into text 001"
    And I switch to the main window

    # Backup the course and restore it.
    When I log out
    And I log in as "admin"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    Then I should see "Course 2"
    When I navigate to "Question bank" node in "Course administration"
    Then I should see "Drag and drop into text 001"

    # Edit the copy and verify the form field contents.
    When I click on "Edit" "link" in the "Drag and drop into text 001" "table_row"
    Then the following fields match these values:
      | Question name             | Drag and drop into text 001   |
      | Question text             | The [[1]] [[2]] on the [[3]]. |
      | General feedback          | The cat sat on the mat.       |
      | id_choices_0_answer       | cat                           |
      | id_choices_1_answer       | sat                           |
      | id_choices_2_answer       | mat                           |
      | id_choices_3_answer       | dog                           |
      | id_choices_4_answer       | table                         |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
