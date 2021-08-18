@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Check MathType enabled if filter disabled at course level but allow_editorplugin_active_course setting is enabled
In order to use MathType with other filters
As an admin
I need to use MathType despite the filter is disabled

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
  Scenario: Disable MathType at course level and enable allow_editorplugin_active_course setting
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Filters" in current page administration
    And I turn MathType filter off
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I add a "Page" to section "0"
    Then "MathType" "button" should not exist
    And I navigate to "Plugins" in site administration
    And I follow "MathType by WIRIS"
    And I check editor always active
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I add a "Page" to section "0"
    Then "MathType" "button" should exist
