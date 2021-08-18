@editor @tinymce @tinymce_tiny_mce_wiris
Feature: Check MathType disabled if filter disabled at course level
In order to check if MathType will be disabled if filter is disabled at couse level
I need to disable filter at activity course level

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
  Scenario: Disable MathType at course level
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the following fields to these values:
      | Text editor | TinyMCE HTML editor |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    Then "MathType" "button" should exist
    And I am on "Course 1" course homepage
    And I navigate to "Filters" in current page administration
    And I turn MathType filter off
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I add a "Page" to section "0"
    Then "MathType" "button" should not exist
