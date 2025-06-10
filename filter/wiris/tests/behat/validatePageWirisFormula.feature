@filter @filter_wiris @wiris_mathtype @moodle_activities @page_render @mtmoodle-8
Feature: Render in moodle pages
  In order to check the pages rendering
  As an admin
  I need to change the configuration

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
    And the MathType filter render type is set to "php"
    And I log in as "admin"

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-8 - Check MathType renders a wiris formula in moodle pages
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for wiris formula render in pages |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists in forum
    Then a Wirisformula containing "1 plus 1" should exist
    # Check renders for Student role
    And I follow "Switch role to..." in the user menu
    And I press "Student"
    And I wait "1" seconds
    Then a Wirisformula containing "1 plus 1" should exist

 @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-8 - Check MathType renders a wiris formula in moodle pages
    # set text editor to "atto HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for wiris formula render in pages |
    # insert Wirisformula
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I set MathType formula to '<math xmlns="http://www.w3.org/1998/Math/MathML"><mn>1</mn><mo>+</mo><mn>1</mn></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I wait "1" seconds
    # check that Wirisformula exists in forum
    Then a Wirisformula containing "1 plus 1" should exist
    # Check renders for Student role
    And I follow "Switch role to..." in the user menu
    And I press "Student"
    And I wait "1" seconds
    Then a Wirisformula containing "1 plus 1" should exist