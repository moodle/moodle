@editor @tiny @editor_tiny @tiny_html @tiny_cloze @javascript
Feature: Test the cloze question editor string parser that a selected question string loads correctly into the dialogue.

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

  Scenario: Load MC question string with feedback
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I set the field "Question text" to multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MC:=California#OK~Arizona#Wrong}</span></p>
    """
    And I select the "span" element in position "0" of the "Question text" TinyMCE editor
    When I click on "Cloze question editor" "button"
    Then I should see "Multiple choice - single response (MULTICHOICE)"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" matches value "California"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Correct"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_feedback')]" matches value "OK"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" matches value "Arizona"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Incorrect"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_feedback')]" matches value "Wrong"

  Scenario: Load MULTIRESPONSE question with custom percentages
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I set the field "Question text" to multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTIRESPONSE:%0%cat~%-22%dog~%100%mouse}</span></p>
    """
    And I select the "span" element in position "0" of the "Question text" TinyMCE editor
    When I click on "Cloze question editor" "button"
    Then I should see "Multiple choice - multiple response (MULTIRESPONSE)"
    And the field "Default mark" matches value "1"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" matches value "cat"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_frac')]" matches value "0"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" matches value "dog"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Custom"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_frac_custom')]" matches value "-22"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[3]//input[contains(@class, 'tiny_cloze_answer')]" matches value "mouse"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_frac')]" matches value "100"

  Scenario: Load SHORTANSWER question with two correct answers
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I set the field "Question text" to multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{3:SHORTANSWER:=William~=Bill#This is the short form but correct}</span></p>
    """
    And I select the "span" element in position "0" of the "Question text" TinyMCE editor
    When I click on "Cloze question editor" "button"
    Then I should see "Short answer (SHORTANSWER)"
    And the field "Default mark" matches value "3"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" matches value "William"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Correct"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_feedback')]" matches value ""
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" matches value "Bill"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Correct"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_feedback')]" matches value "This is the short form but correct"

  Scenario: Load MULTICHOICE_V question with latex notation
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I set the field "Question text" to multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:MULTICHOICE_V:=\(\frac{1\}{2\}\)~\(\frac{2\}{3\}\)~\(\frac{3\}{4\}\)}</span></p>
    """
    And I select the "span" element in position "0" of the "Question text" TinyMCE editor
    When I click on "Cloze question editor" "button"
    Then I should see "Multiple choice - single response (MULTICHOICE_V)"
    And the field "Default mark" matches value "1"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" matches value "\(\frac{1}{2}\)"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Correct"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" matches value "\(\frac{2}{3}\)"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Incorrect"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[3]//input[contains(@class, 'tiny_cloze_answer')]" matches value "\(\frac{3}{4}\)"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[3]//select[contains(@class, 'tiny_cloze_frac')]" matches value "Incorrect"
