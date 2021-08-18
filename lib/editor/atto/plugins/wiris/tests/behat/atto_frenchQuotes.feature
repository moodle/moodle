@editor @editor_atto @atto @atto_wiris @_bug_phantomjs
Feature: Checking french quotes to prevent dissapear and post
In order to check if french quotes can be displayed correctly
I need to post with french quotes

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
  Scenario: Checking french quotes to prevent dissapear and post
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Page" to section "0"
    And I set the following fields to these values:
      | Name         | Test MathType for Atto on Moodle |
      | Page content | &laquo;Bonjour&raquo; |
    And I press "Save and display"
    Then "«Bonjour»" "text" should exist
    And I navigate to "Edit settings" in current page administration
    Then Wirisformula should not exist
