@editor @tiny @editor_tiny @tiny_html @tiny_cloze @javascript
Feature: Test the cloze question editor gui with empty fields that should be automatically eliminated.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Mark      | Allright | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    Given the following "user preferences" exist:
      | user    | preference | value |
      | teacher | htmleditor | tiny  |

  Scenario: Create a MULTICHOICE question with 2 correct answers and the empty field in the middle will be ignored
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTICHOICE" to "1"
    And I click on "Select question type" "button"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "William"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//input[contains(@class, 'tiny_cloze_answer')]" to "Bill"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_fraction')]" to "Correct"
    When I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{2:SHORTANSWER:=William~=Bill}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTICHOICE:=William~=Bill}</span></p>
    """

  Scenario: Create a MULTICRESPONSE question with 2 correct answers and the last empty field will be ignored
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTIRESPONSE" to "1"
    And I click on "Select question type" "button"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "William"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" to "Bill"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_fraction')]" to "Correct"
    When I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{2:SHORTANSWER:=William~=Bill}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTIRESPONSE:=William~=Bill}</span></p>
    """

  Scenario: Create a MULTICRESPONSE question with 2 correct answers and the last empty correct field will be ignored
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTIRESPONSE" to "1"
    And I click on "Select question type" "button"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "William"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" to "Bill"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_fraction')]" to "Correct"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_fraction')]" to "Correct"
    When I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{2:SHORTANSWER:=William~=Bill}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTIRESPONSE:=William~=Bill}</span></p>
    """
