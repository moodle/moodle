@tiny @tiny_wiris @wiris_mathtype @tinymce6_filter_editor_settings
Feature: Check the math and chem buttons visibility on text editors
In order to check the buttons visibility in atto editor
As an admin
I need to change the settings on the filter wiris

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
    And the "urltolink" filter is "off"
    And the "mathjaxloader" filter is "off"
    And I log in as "admin"

  @javascript @mtmoodle-18 @4.x @4.x_tinymce6 @4.x_filter 
  Scenario: MTMOODLE-18 - Disable mathtype button and check that it is not visible 
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 1 |
      | Math editor    | 0 |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    Then I check "MathType" in "Page content" field "does not" exist in TinyMCE editor
    And I check "ChemType" in "Page content" field "does" exist in TinyMCE editor

  @javascript @mtmoodle-19 @4.x @4.x_tinymce6 @4.x_filter
  Scenario: MTMOODLE-19 - Disable chemtype button and check that it is not visible
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 0 |
      | Math editor    | 1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    Then I check "MathType" in "Page content" field "does" exist in TinyMCE editor
    And I check "ChemType" in "Page content" field "does not" exist in TinyMCE editor

  @javascript @mtmoodle-20 @4.x @4.x_tinymce6 @4.x_filter
  Scenario: MTMOODLE-20 - Set mathtype & chemtype buttons always active and disable MathType filter
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 1 |
      | Math editor    | 1 |
      | Editor always active   | 1 |
    And I press "Save changes"
    And the "wiris" filter is "off"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I press "Toggle" in "Page content" field in TinyMCE 6 editor
    Then I check "MathType" in "Page content" field "does" exist in TinyMCE editor
    Then I check "ChemType" in "Page content" field "does" exist in TinyMCE editor

  @javascript @mtmoodle-97 @4.x @4.x_tinymce6 @4.x_filter
  Scenario: MTMOODLE-97 - Disable mathtype & chemtype buttons and disable MathType filter
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 0 |
      | Math editor    | 0 |
      | Editor always active   | 0 |
    And I press "Save changes"
    And the "wiris" filter is "off"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    Then I check "MathType" in "Page content" field "does not" exist in TinyMCE editor
    Then I check "ChemType" in "Page content" field "does not" exist in TinyMCE editor

@javascript @mtmoodle-18 @3.x @3.x_tinymce6 @3.x_filter @4.0 @4.0_tinymce6 @4.0_filter
  Scenario: MTMOODLE-18 - Disable mathtype button and check that it is not visible 
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 1 |
      | Math editor    | 0 |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    Then I check "MathType" in "Page content" field "does not" exist in TinyMCE editor
    And I check "ChemType" in "Page content" field "does" exist in TinyMCE editor

  @javascript @mtmoodle-19 @3.x @3.x_tinymce6 @3.x_filter @4.0 @4.0_tinymce6 @4.0_filter
  Scenario: MTMOODLE-19 - Disable chemtype button and check that it is not visible
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 0 |
      | Math editor    | 1 |
    And I press "Save changes" 
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    Then I check "MathType" in "Page content" field "does" exist in TinyMCE editor
    And I check "ChemType" in "Page content" field "does not" exist in TinyMCE editor

  @javascript @mtmoodle-20 @3.x @3.x_tinymce6 @3.x_filter @4.0 @4.0_tinymce6 @4.0_filter
  Scenario: MTMOODLE-20 - Set mathtype & chemtype buttons always active and disable MathType filter
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 1 |
      | Math editor    | 1 |
      | Editor always active   | 1 |
    And I press "Save changes"
    And the "wiris" filter is "off"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    Then I check "MathType" in "Page content" field "does" exist in TinyMCE editor
    Then I check "ChemType" in "Page content" field "does" exist in TinyMCE editor

  @javascript @mtmoodle-97 @3.x @3.x_tinymce6 @3.x_filter @4.0 @4.0_tinymce6 @4.0_filter
  Scenario: MTMOODLE-97 - Disable mathtype & chemtype buttons and disable MathType filter
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE editor |
    And I press "Save changes"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Chemistry editor   | 0 |
      | Math editor    | 0 |
      | Editor always active   | 0 |
    And I press "Save changes"
    And the "wiris" filter is "off"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    Then I check "MathType" in "Page content" field "does not" exist in TinyMCE editor
    Then I check "ChemType" in "Page content" field "does not" exist in TinyMCE editor
