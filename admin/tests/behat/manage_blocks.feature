@core @core_admin
Feature: An administrator can manage Block plugins
    In order to alter the user experience
    As an admin
    I can manage block plugins

  @javascript
  Scenario: An administrator can control the enabled state of block plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I toggle the "Disable Latest badges" admin switch "off"
    And I should see "Latest badges disabled."
    And I reload the page
    Then I should see "Enable Latest badges"
    And I toggle the "Enable Latest badges" admin switch "on"
    And I should see "Latest badges enabled."
    And I reload the page
    Then I should see "Disable Latest badges"

  @javascript
  Scenario: An administrator can control the protected state of block plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Protect instances of Latest badges" "link"
    Then I should see "Latest badges block instances are protected."
    And "Protect instances of Latest badges" "link" should not exist
    But "Unprotect instances of Latest badges" "link" should exist
    And "Protect instances of Activities" "link" should exist
    When I click on "Unprotect instances of Latest badges" "link"
    Then I should see "Latest badges block instances are unprotected."
    And "Unprotect instances of Activities" "link" should not exist
    But "Protect instances of Latest badges" "link" should exist
    And "Protect instances of Activities" "link" should exist

  Scenario: An administrator can control the protected state of block plugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Protect instances of Latest badges" "link"
    Then I should see "Latest badges block instances are protected."
    And "Protect instances of Latest badges" "link" should not exist
    But "Unprotect instances of Latest badges" "link" should exist
    And "Protect instances of Activities" "link" should exist
    When I click on "Unprotect instances of Latest badges" "link"
    Then I should see "Latest badges block instances are unprotected."
    And "Unprotect instances of Activities" "link" should not exist
    But "Protect instances of Latest badges" "link" should exist
    And "Protect instances of Activities" "link" should exist
