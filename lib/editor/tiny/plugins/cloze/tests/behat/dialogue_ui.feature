@editor @tiny @tiny_cloze @javascript
Feature: Test basic ui features of the dialogue window

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

  Scenario: Test changes of question description
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    When I set the field "SHORTANSWER" to "1"
    Then I should see "Allows a response of one or a few words that is graded by comparing against various model answers, which may contain wildcards."
    And I should see "No, case is unimportant"

    When I set the field "SHORTANSWER_C" to "1"
    Then I should see "Allows a response of one or a few words that is graded by comparing against various model answers, which may contain wildcards."
    And I should see "Yes, case must match"

    When I set the field "MULTIRESPONSE_S" to "1"
    Then I should see "Allows the selection of a single or multiple responses from a pre-defined list."
    And I should see "Vertical column of checkboxes"
    And I should see "Shuffle within questions"
    And I should see "Multiple answers allowed"

    When I set the field "MULTICHOICE_H" to "1"
    Then I should see "Allows the selection of a single or multiple responses from a pre-defined list."
    And I should see "Horizontal row of radio-buttons"
    And I should see "One answer only"

    When I click on "Select question type" "button"
    Then I should see "Multiple choice - single response (MULTICHOICE_H)"

    And I click on "Cancel" "button" in the "Cloze question editor" "dialogue"

  Scenario: Trash icon should be visible if several anser options are available and disappear if there is only one option available
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Embedded answers (Cloze)" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "multianswer-001"
    And I click on "Cloze question editor" "button"
    And I set the field "NUMERICAL" to "1"
    And I click on "Select question type" "button"
    Then ".tiny_cloze_delete img" "css_element" should not be visible

    When I click on "//form[@name='tiny_cloze_form']//li[1]//a[contains(@class, 'tiny_cloze_add')]" "xpath"
    Then ".tiny_cloze_delete img" "css_element" should be visible

    When I click on "//form[@name='tiny_cloze_form']//li[1]//a[contains(@class, 'tiny_cloze_delete')]" "xpath"
    Then ".tiny_cloze_delete img" "css_element" should not be visible

    When I click on "Cancel" "button" in the "Cloze question editor" "dialogue"
    And I click on "Cloze question editor" "button"
    And I set the field "MULTICHOICE_H" to "1"
    And I click on "Select question type" "button"
    Then ".tiny_cloze_delete img" "css_element" should be visible

    When I click on "//form[@name='tiny_cloze_form']//li[3]//a[contains(@class, 'tiny_cloze_delete')]" "xpath"
    Then ".tiny_cloze_delete img" "css_element" should be visible

    When I click on "//form[@name='tiny_cloze_form']//li[1]//a[contains(@class, 'tiny_cloze_delete')]" "xpath"
    Then ".tiny_cloze_delete img" "css_element" should not be visible
