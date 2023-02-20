@core @core_admin
Feature: An administrator can manage Media plugins
    In order to alter the user experience
    As an admin
    I can manage media plugins

  @javascript
  Scenario: An administrator can control the enabled state of media plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Media players > Manage media players" in site administration
    When I click on "Disable the YouTube plugin" "link"
    Then I should see "The YouTube plugin has been disabled"
    And "Disable the YouTube plugin" "link" should not exist
    But "Enable the YouTube plugin" "link" should exist
    When I click on "Enable the YouTube plugin" "link"
    Then I should see "The YouTube plugin has been enabled"
    And "Enable the YouTube plugin" "link" should not exist
    But "Disable the YouTube plugin" "link" should exist

  Scenario: An administrator can control the enabled state of media plugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Media players > Manage media players" in site administration
    When I click on "Disable the YouTube plugin" "link"
    Then I should see "The YouTube plugin has been disabled"
    And "Disable the YouTube plugin" "link" should not exist
    But "Enable the YouTube plugin" "link" should exist
    When I click on "Enable the YouTube plugin" "link"
    Then I should see "The YouTube plugin has been enabled"
    And "Enable the YouTube plugin" "link" should not exist
    But "Disable the YouTube plugin" "link" should exist
