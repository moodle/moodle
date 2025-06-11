@editor @tiny @editor_tiny @tiny_html @tiny_cloze @javascript
Feature: Test the cloze question dialgoue with error messages when not all fields are filled correctly.

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

  Scenario: Create a MULTICHOICE_VS question with errors
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTICHOICE_VS" to "1"
    And I click on "Select question type" "button"
    When I click on "Insert question" "button"
    Then I should see "Empty answer."
    And I should not see "No correct answer found."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_fraction')]" to "Incorrect"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_fraction')]" to "Incorrect"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_fraction')]" to "Incorrect"
    And I click on "//form[@name='tiny_cloze_form']//li[2]//a[@class='tiny_cloze_delete']" "xpath"
    And I click on "Insert question" "button"

    Then I should see "Empty answer."
    And I should see "No correct answer found."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "dog"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" to "cat"
    And I click on "//form[@name='tiny_cloze_form']//li[2]//a[@class='tiny_cloze_add']" "xpath"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//input[contains(@class, 'tiny_cloze_answer')]" to "mouse"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_fraction')]" to "Correct"
    And I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{1:MULTICHOICE_VS:=dog~cat~mouse}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTICHOICE_VS:=dog~cat~mouse}</span></p>
    """

  Scenario: Create a NUMERICAL question with errors
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "NUMERICAL" to "1"
    And I click on "Select question type" "button"
    When I click on "Insert question" "button"
    Then I should see "Empty answer."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "adr"
    And I click on "Insert question" "button"
    Then I should see "Value must be numeric."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "0.45"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_tolerance')]" to "adr"
    And I click on "Insert question" "button"
    Then I should see "Value must be numeric."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_tolerance')]" to "0"
    And I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{1:NUMERICAL:=0.45:0}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:NUMERICAL:=0.45:0}</span></p>
    """

  Scenario: Create a MULTIRESPONSE question with errors
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTIRESPONSE" to "1"
    And I click on "Select question type" "button"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_fraction')]" to "Custom"
    When I click on "Insert question" "button"
    Then I should see "Empty answer."
    And I should not see "No correct answer found."
    And I should see "Invalid value for custom percent rate."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_frac_custom')]" to "-101"
    And I click on "Insert question" "button"
    Then I should see "Empty answer."
    And I should not see "No correct answer found."
    And I should see "Invalid value for custom percent rate."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_frac_custom')]" to "-100"
    And I click on "Insert question" "button"
    Then I should see "Empty answer."
    And I should not see "No correct answer found."
    And I should not see "Invalid value for custom percent rate."

    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_frac_custom')]" to "22"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_fraction')]" to "Incorrect"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_fraction')]" to "50%"
    And I click on "Insert question" "button"
    Then I should see "Empty answer."
    And I should see "No correct answer found."
    And I should not see "Invalid value for custom percent rate."
