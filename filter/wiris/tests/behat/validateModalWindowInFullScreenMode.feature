@filter @filter_wiris @wiris_mathtype @filter_settings @window_settings @mtmoodle-24
Feature: Filter Settings - Window Settings - Full Screen mode
  In order to check if the editor's full-screen modal is displayed
  As an admin
  I need to open an editor

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
    And I log in as "admin"

  @javascript @4.x @4.x_filter
  Scenario: MTMOODLE-24 - Validate modal window is maximized when full screen mode is selected
    # set render type to "client"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "client"
    And I check full-screen mode on
    And I press "Save changes"
    # set text editor to "HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType full-screen modal |
    # Maximize editor
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    Then I check editor is in full-screen mode

  @javascript @3.x @3.x_filter @4.0 @4.0_filter
  Scenario: MTMOODLE-24 - Validate modal window is maximized when full screen mode is selected
    # set render type to "client"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And the MathType filter render type is set to "client"
    And I check full-screen mode on
    And I press "Save changes"
    # set text editor to "HTML"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | Atto HTML editor |
    And I press "Save changes"
    # create new page in existing course
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType full-screen modal |
    # Maximize editor
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    Then I check editor is in full-screen mode