@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Use atto to post a chemistry formula
In order to check whether a chemistry formula can be displayed correctly
As an admin
I need to write a chemistry formula

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
  Scenario: Post a chemistry formula
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name | Test MathType for Atto on Moodle chemistry formulas |
    And I press "ChemType" in "Page content" field in Atto editor
    And I set MathType formula to '<math><mi mathvariant="normal">H</mi><mn>2</mn><mi mathvariant="normal">O</mi></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    Then ChemType formula should exist
    And Wirisformula should has height 19 with error of 2
