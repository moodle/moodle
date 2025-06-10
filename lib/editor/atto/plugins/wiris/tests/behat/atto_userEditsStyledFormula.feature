@atto @atto_wiris @wiris_mathtype @atto_edit_formula @atto_formula_type @mtmoodle-58
Feature: Edit MathType styled formula
  In order to check if styled MathType formula can be edited correctly
  As an admin
  I need to create a styled MathType formula

  Background:
    Given the following config values are set as admin:
      | config  | value        | plugin      |
      | toolbar | math = wiris | editor_atto |
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

  @javascript @4.x @4.x_atto
  Scenario: MTMOODLE-58 - User edits MathType styled
    # Course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle chemistry formulas |
    # Insert formula.
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math style="font-family:Arial" xmlns="http://www.w3.org/1998/Math/MathML"><mstyle mathsize="72px"><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn></mstyle></math>'
    And I wait "2" seconds
    And I press accept button in MathType Editor
    # Assert that selection
    And I wait until Wirisformula formula exists
    And I dBclick on WirisFormula with alt equals to "bold italic 1"
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math style="font-family:Arial" xmlns="http://www.w3.org/1998/Math/MathML"><mstyle mathsize="72px"><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn><mo mathvariant="bold-italic" mathcolor="#FF0000">+</mo><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn></mstyle></math>'
    And I wait "2" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    # Check final formula
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'bold italic 1 bold italic plus bold italic 1' should exist

  @javascript @4.0 @4.0_atto
  Scenario: MTMOODLE-58 - User edits MathType styled
    # Course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle chemistry formulas |
    # Insert formula.
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math style="font-family:Arial" xmlns="http://www.w3.org/1998/Math/MathML"><mstyle mathsize="72px"><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn></mstyle></math>'
    And I wait "2" seconds
    And I press accept button in MathType Editor
    # Assert that selection
    And I wait until Wirisformula formula exists
    And I dBclick on WirisFormula with alt equals to "bold italic 1"
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math style="font-family:Arial" xmlns="http://www.w3.org/1998/Math/MathML"><mstyle mathsize="72px"><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn><mo mathvariant="bold-italic" mathcolor="#FF0000">+</mo><mn mathvariant="bold-italic" mathcolor="#FF0000">1</mn></mstyle></math>'
    And I wait "2" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    # Check final formula
    And I wait until Wirisformula formula exists
    And a Wirisformula containing 'bold italic 1 bold italic plus bold italic 1' should exist