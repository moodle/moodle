@tiny @tiny_wiris @wiris_mathtype @tinymce6_insert_formula @tinymce6_focus @mtmoodle-75
Feature: Insert MathType formula via keyboard
  In order to check if MathType formula can be written and accepted via keyboard
  As an admin
  I need to create a MathType formula

  Background:
    Given the following config values are set as admin:
      | config        | value                        | plugin      |
      | customtoolbar | tiny_mce_wiris_formulaEditor | editor_tiny |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "mathjaxloader" filter is "off"
    And the "urltolink" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_tinymce6 @5.x @5.x_tinymce6
  Scenario: MTMOODLE-75 - User inserts MathType via keyboard
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test WirisFormula for Atto on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "3" seconds
    And I press the tab key
    And I press the enter key
    And I press "Save and display"
    Then I wait until Wirisformula formula exists
    Then a Wirisformula containing 'square root' should exist
    And Wirisformula should has height 48 with error of 2

  @javascript @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-75 - User inserts MathType via keyboard
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test WirisFormula for Atto on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "3" seconds
    And I press the tab key
    And I press the enter key
    And I press "Save and display"
    Then I wait until Wirisformula formula exists
    Then a Wirisformula containing 'square root' should exist
    And Wirisformula should has height 48 with error of 2