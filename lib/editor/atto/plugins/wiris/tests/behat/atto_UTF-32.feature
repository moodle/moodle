@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Test I double struck (UTF-32)
In order to create formulas with UTF-32 characters
As an admin
I need to see a formula with a UTF-32 character

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
  Scenario: Insert double struck using UTF-32
    And I navigate to "Plugins" in site administration
    And I follow "Atto toolbar settings"
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
    And I set MathType formula to '<math><mi mathvariant="normal">&#x1D540;</mi></math>'
    And I press accept button in MathType Editor
    And I press "HTML" in "Page content" field in Atto editor
    And I press "HTML pressed" in "Page content" field in Atto editor
    And I press "Save and display"
    Then a Wirisformula containing html entity '&#x1D540;' should exist
    And Wirisformula should has height 19 with error of 2
