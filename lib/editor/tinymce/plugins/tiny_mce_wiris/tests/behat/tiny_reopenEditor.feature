@editor @tinymce @tinymce_tiny_mce_wiris
Feature: Check if editor can be reopened
In order to check if the MathType editor can be reopened
I need to open the editor
Close the editor
Open the editor again

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin  | C1     | editingteacher |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Reopen the editor
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE HTML editor |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE editor
    And I press "MathType" in "Page content" field in TinyMCE editor
    And I press cancel button in MathType Editor
    And I press "MathType" in "Page content" field in TinyMCE editor
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then a Wirisformula containing 'square root' should exist
    And Wirisformula should has height 48 with error of 2
