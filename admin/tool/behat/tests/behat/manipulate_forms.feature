@tool @tool_behat
Feature: Forms manipulation
  In order to interact with Moodle
  As a user
  I need to set forms values

  @javascript
  Scenario: Basic forms manipulation
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    When I set the field "First name" to "Field value"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Unmask" to "1"
    Then the field "First name" matches value "Field value"
    And the "Text editor" select box should contain "Plain text area"
    And the field "Unmask" matches value "1"
    And I set the field "Unmask" to ""
    And the field "Unmask" matches value ""
    And I press "Update profile"

  @javascript
  Scenario: Expand all fieldsets and advanced elements
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Quiz" to section "1"
    When I expand all fieldsets
    Then I should see "Close the quiz"
    And I should see "Group mode"
    And I should see "Grouping"
    And I should not see "Show more..." in the "region-main" "region"
    And I should see "Show less..."
