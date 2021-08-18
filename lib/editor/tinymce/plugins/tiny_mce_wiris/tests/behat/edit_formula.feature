@editor @tinymce @tinymce_tiny_mce_wiris
Feature: MathType for TinyMCE
  In order to check if formula can be created correctly in tiny
  I need to create a formula in the user profile

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | customtoolbar | tiny_mce_wiris_formulaEditor | editor_tinymce |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: Create a formulas
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE HTML editor |
    And I press "Save changes"
    And I open my profile in edit mode
    And I press "MathType"
    And I set mathtype formula to "1+2"
    And I press accept button in MathType Editor
    And I press "Update profile"
    And I follow "Profile" in the user menu
    # Checking formula image outside edit element.
    Then a Wirisformula containing '1 plus 2' should exist
