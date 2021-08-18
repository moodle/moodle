@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Check MathType disabled if filter disabled at activity forum level
In order to check if MathType plugin is disabled if filter is disabled
As an admin
I need not to be able to use MathType if filter is disabled

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
  Scenario: Disable MathType at activity level
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for Atto on Moodle |
      | Page content | 1 |
    Then "MathType" "button" should exist
    And I press "Save and display"
    And I navigate to "Filters" in current page administration
    And I turn MathType filter off
    And I press "Save changes"
    And I follow "Test MathType for Atto on Moodle"
    And I navigate to "Edit settings" in current page administration
    Then "MathType" "button" should not exist

  @javascript
  Scenario: Disable MathType at course level
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
