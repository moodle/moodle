@tiny @tiny_wiris @wiris_mathtype @tinymce6_edit_formula @mtmoodle-36
Feature: Edit MathType formula with tinymce6 editor via selection
In order to check if MathType formula can be edited via selection
As an admin
I need to create a MathType formula

Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | customtoolbar | tiny_mce_wiris_formulaEditor | editor_tiny |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "mathjaxloader" filter is "off"
    And the "urltolink" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_tinymce6 
  Scenario: MTMOODLE-36 - Edit MathType formula via selection
    # Course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for TinyMCE6 on Moodle chemistry formulas |
    # Insert formula.
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    # Assert that selection
    ## Go to the editor's iframe to click the wirisformula
    And I switch to iframe with locator "id_page_ifr"
    And I wait until Wirisformula formula exists
    And I click on WirisFormula with alt equals to "1 plus 1"
    ## Return to the default frame to click on the MathType editor button
    And I return to default frame
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mi>y</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    # Check final formula
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing '1 plus 1' should not exist
    And a Wirisformula containing 'square root of y' should exist

  @javascript @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-36 - Edit MathType formula via selection
    # Course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for TinyMCE6 on Moodle chemistry formulas |
    # Insert formula.
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    ## Go to the editor's iframe to click the wirisformula
    And I switch to iframe with locator "id_page_ifr"
    And I wait until Wirisformula formula exists
    And I click on WirisFormula with alt equals to "1 plus 1"
    ## Return to the default frame to click on the MathType editor button
    And I return to default frame
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><msqrt><mi>y</mi></msqrt></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    # Check final formula
    And I wait until Wirisformula formula exists
    Then a Wirisformula containing '1 plus 1' should not exist
    And a Wirisformula containing 'square root of y' should exist
