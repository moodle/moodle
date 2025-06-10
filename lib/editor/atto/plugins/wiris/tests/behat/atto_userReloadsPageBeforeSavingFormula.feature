@atto @atto_wiris @wiris_mathtype @atto_insert_formula @mtmoodle-93
Feature: Check that formula is rendered when atto's draft is restored
In order to not loose data
As an admin
I need to restore draft content containing MathType formulas

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | toolbar | math = wiris | editor_atto |
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

  @javascript @4.x @4.x_atto
  Scenario: MTMOODLE-93 - Insert a formula and reload the page
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I select seconds in autosave frequency option
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" using the activity chooser
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I wait "5" seconds
    And I reload the page
    And I wait "5" seconds
    Then I wait until Wirisformula formula exists
    Then Wirisformula should exist

  @javascript @3.x @3.x_atto @4.0 @4.0_atto
  Scenario: MTMOODLE-93 - Insert a formula and reload the page
    And I navigate to "Plugins > Text editors > Atto toolbar settings" in site administration
    And I select seconds in autosave frequency option
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0" 
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I wait until MathType editor is displayed
    And I wait "2" seconds
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I wait "1" seconds
    And I press accept button in MathType Editor
    And I wait "5" seconds
    And I reload the page
    And I wait "5" seconds
    Then I wait until Wirisformula formula exists
    Then Wirisformula should exist

