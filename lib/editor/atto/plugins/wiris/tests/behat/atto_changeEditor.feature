@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Change between editors
In order to check if it's possible change between MathType and ChemType editors
As an admin
I need to change between editors

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
  Scenario: Change from custom editor to MathType Editor
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle chemistry formulas |
    And I press "ChemType" in "Page content" field in Atto editor
    And I press cancel button in MathType Editor
    And I press "MathType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><mfrac><mn>1</mn><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></mfrac></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then ChemType formula should not exist
    Then a Wirisformula containing 'square root' should exist
    And Wirisformula should has height 48 with error of 2
