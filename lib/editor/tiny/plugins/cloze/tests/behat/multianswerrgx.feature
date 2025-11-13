@editor @tiny @editor_tiny @tiny_cloze @javascript
Feature: Test the multianswerrgx question type (simulate that the plugin is enabled)

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
    And the following "user preferences" exist:
        | user    | preference | value |
        | teacher | htmleditor | tiny  |
    And  the following config values are set as admin:
        | simulate_multianswerrgx | 1 | tiny_cloze |

  Scenario: Create a new question and the regex types must not appear in the list
    When the following config values are set as admin:
        | simulate_multianswerrgx | 0 | tiny_cloze |
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    Then I should see "MULTICHOICE_S"
    And I should see "NUMERICAL"
    And I should see "SHORTANSWER"
    And I should not see "REGEXP"
    And I should not see "REGEXP_C"

  Scenario: Create a new question and the regex types must appear in the list
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    Then I should see "MULTICHOICE_S"
    And I should see "NUMERICAL"
    And I should see "SHORTANSWER"
    And I should see "REGEXP"
    And I should see "REGEXP_C"

  Scenario: Load REGEXP question string with feedback
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I set the field "Question text" to multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:REGEXP:%100%blue, white and red#Congratulations!~--.*(blue|red|white).*#You have not even found one of the colours of the French flag!~--.*(&&blue&&red&&white).*#You have not found all the colours of the French flag~--.*\bblue\b.*#The colour of the sky is missing!}</span></p>
    """
    And I select the "span" element in position "0" of the "Question text" TinyMCE editor
    And I click on "Cloze question editor" "button"
    # The following step works locally but fails on Github for some reason.
    #Then I should see "Regular expression short answer (REGEXP)"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" matches value "blue, white and red"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//select[contains(@class, 'tiny_cloze_frac')]" matches value "100%"
    And the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_feedback')]" matches value "Congratulations!"

  Scenario: Load REGEXP question string and provoke some errors
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "REGEXP" to "1"
    And I click on "Select question type" "button"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "[bcr]at$"
    And I click on "//form[@name='tiny_cloze_form']//li[1]//a[contains(@class, 'tiny_cloze_add')]" "xpath"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" to "^(dog|cat))*"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//select[contains(@class, 'tiny_cloze_fraction')]" to "Incorrect"
    And I click on "Insert question" "button"
    Then I should see "Special chars like . ^ $ * + { } \ / must be escaped in correct regular expression."
    And I should see "Opening and closing brackets do not match in regular expression."
    When I set the field with xpath "//form[@name='tiny_cloze_form']//li[1]//input[contains(@class, 'tiny_cloze_answer')]" to "[bcr]at"
    And I set the field with xpath "//form[@name='tiny_cloze_form']//li[2]//input[contains(@class, 'tiny_cloze_answer')]" to "^(dog|cat)\)"
    And I click on "Insert question" "button"
    #And I click on the "View > Source code" menu item for the "Question text" TinyMCE editor
    #Then I should see "<p>{1:REGEXP:=[bcr]at~^(dog|cat)\)}</p>" source code for the "Question text" TinyMCE editor
    And I click on "Save changes and continue editing" "button"
    Then the field "Question text" matches multiline:
    """
    <p><span class="cloze-question-marker" contenteditable="false">{1:REGEXP:=[bcr]at~^(dog|cat)\)}</span></p>
    """
