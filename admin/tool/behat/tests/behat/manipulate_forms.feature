@tool_behat @core_form
Feature: Forms manipulation
  In order to interact with Moodle
  As a user
  I need to set forms values

  @javascript
  Scenario: Basic forms manipulation
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    When I fill in "First name" with "Field value"
    And I select "Use standard web forms" from "When editing text"
    And I check "Unmask"
    Then the "First name" field should match "Field value" value
    And the "When editing text" select box should contain "Use standard web forms"
    And the "Unmask" checkbox should be checked
    And I uncheck "Unmask"
    And the "Unmask" checkbox should not be checked
    And I press "Update profile"
