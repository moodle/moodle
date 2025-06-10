@atto @atto_wiris @wiris_mathtype @atto_insert_formula @atto_symbols_and_attributes @mtmoodle-94
Feature: Test double struck using UTF-32
  In order to create formulas with UTF-32 characters
  As an admin
  I need to see a formula with a UTF-32 character

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"
    And the "urltolink" filter is "off"
    And the "mathjaxloader" filter is "off"
    And I log in as "admin"

  @javascript @4.x @4.x_atto
  Scenario: MTMOODLE-94 - Insert double struck using UTF-32
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I wait "3" seconds
    And I set MathType formula to '<math><mi mathvariant="normal">&#x1D540;</mi></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "HTML" in "Page content" field in Atto editor
    And I press "HTML pressed" in "Page content" field in Atto editor
    And I press "Save and display"
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists
    And a Wirisformula containing html entity '§#120128;' should exist
    And Wirisformula should has height 19 with error of 2

  @javascript @3.x @3.x_atto @4.0 @4.0_atto
  Scenario: MTMOODLE-94 - Insert double struck using UTF-32
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
      """
      math = wiris
      other = html
      """
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait "1" seconds
    And I set MathType formula to '<math><mi mathvariant="normal">&#x1D540;</mi></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I press "HTML" in "Page content" field in Atto editor
    And I press "HTML pressed" in "Page content" field in Atto editor
    And I press "Save and display"
    # Then a Wirisformula containing html entity '&#x1D540;' should exist
    # And Wirisformula should has height 19 with error of 2
    And I wait "2" seconds
    Then I wait until Wirisformula formula exists
    # And a Wirisformula containing html entity '&#x1D540;' should exist
    And Wirisformula should has height 19 with error of 2