@core @core_admin
Feature: An administrator can manage Media plugins
    In order to alter the user experience
    As an admin
    I can manage media plugins

  @javascript
  Scenario: An administrator can control the enabled state of media plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Media players > Manage media players" in site administration
    When I click on "Disable YouTube" "link"
    Then I should see "YouTube disabled."
    And "Disable YouTube" "link" should not exist
    But "Enable YouTube" "link" should exist
    When I click on "Enable YouTube" "link"
    Then I should see "YouTube enabled."
    And "Enable YouTube" "link" should not exist
    But "Disable YouTube" "link" should exist

  Scenario: An administrator can control the enabled state of media plugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Media players > Manage media players" in site administration
    When I click on "Disable YouTube" "link"
    Then I should see "YouTube disabled."
    And "Disable YouTube" "link" should not exist
    But "Enable YouTube" "link" should exist
    When I click on "Enable YouTube" "link"
    Then I should see "YouTube enabled."
    And "Enable YouTube" "link" should not exist
    But "Disable YouTube" "link" should exist
