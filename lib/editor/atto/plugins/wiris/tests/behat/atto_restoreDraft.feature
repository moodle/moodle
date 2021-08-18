@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
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
    And I log in as "admin"

  @javascript
  Scenario: Insert a formula and restore the page
    And I navigate to "Plugins" in site administration
    And I follow "Atto toolbar settings"
    And I select seconds in autosave frequency option
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle |
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I press accept button in MathType Editor
    And I wait "5" seconds
    And I reload the page
    Then Wirisformula should exist
