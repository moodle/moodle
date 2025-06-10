@tiny @tiny_wiris @wiris_mathtype @tinymce6_edit_formula @tinymce6_latex_formula @mtmoodle-38
Feature: Edit LaTeX formula with MathType editor
  In order to check the edition of a formula in LaTeX
  As an admin
  I need to edit a LaTeX with MathType

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "mathjaxloader" filter is "disabled"
    And the "urltolink" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4 @4.x_tinymce6
  Scenario: MTMOODLE-38 - User edits a LaTeX formula
    And I navigate to "General > Security > Site security settings" in site administration
    And I check enable trusted content
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Description  | ejemplo                              |
      | Page content | $$\frac{x+3}{y-2}=c^2$$              |
    # Go to the editor's iframe to select the wirisformula
    And I switch to iframe with locator "id_page_ifr"
    And I click on "Page content" field in TinyMCE 6
    And I return to default frame
    And I place caret at position "2" in "Page Content" field in TinyMCE 6
    # Return to the default frame to check if MathType editor opened
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    And I switch to iframe with locator "id_page_ifr"
    Then "$$\frac1{\sqrt{2\pi}}$$" "text" should exist
    And I return to default frame
    And I press "Save and display"
    And I wait "1" seconds
    And I navigate to "Settings" in current page administration
    And I switch to iframe with locator "id_page_ifr"
    Then "$$\frac1{\sqrt{2\pi}}$$" "text" should exist

  @javascript @4.0 @4.0_tinymce6
  Scenario: MTMOODLE-38 - User edits a LaTeX formula
    And I navigate to "General > Security > Site security settings" in site administration
    And I check enable trusted content
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for TinyMCE6 on Moodle |
      | Page content | $\frac{x+3}{y-2}=c^2$              |
    And I click on "Page content" field
    And I place caret at position "2" in "Page content" field
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    And I press "MathType" in "Page content" field in TinyMCE 6 editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I wait "1" seconds
    Then "$$\frac1{\sqrt{2\pi}}$$" "text" should exist
    And I press "Save and display"
    And I wait "1" seconds
    And I navigate to "Settings" in current page administration
    Then "$$\frac1{\sqrt{2\pi}}$$" "text" should exist
