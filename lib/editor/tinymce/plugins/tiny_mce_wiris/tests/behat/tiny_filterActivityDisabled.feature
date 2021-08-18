@editor @tinymce @tinymce_tiny_mce_wiris
Feature: Check MathType disabled if filter disabled at activity forum level
In order to check if MathType will be disabled if filter is disabled at activity level
I need to disable filter at activity page level

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
  Scenario: Disable MathType at page level
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE HTML editor |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for Atto on Moodle |
    And I press "Toggle" in "Page content" field in TinyMCE editor
    And I press "MathType" in "Page content" field in TinyMCE editor
    And I set MathType formula to '<math><msqrt><mn>2</mn><mi>&#x3c0;</mi></msqrt></math>'
    And I press accept button in MathType Editor
    And I press "Save and display"
    And I navigate to "Filters" in current page administration
    And I turn MathType filter off
    And I press "Save changes"
    And I follow "Test MathType for Atto on Moodle"
    And I navigate to "Edit settings" in current page administration
    Then "MathType" "button" should not exist
