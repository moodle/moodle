@core @core_admin
Feature: An administrator can manage Block plugins
    In order to alter the user experience
    As an admin
    I can manage block plugins

  @javascript
  Scenario: An administrator can control the enabled state of block plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Disable the Latest badges plugin" "link"
    Then I should see "The Latest badges plugin has been disabled"
    And "Disable the Latest badges plugin" "link" should not exist
    But "Enable the Latest badges plugin" "link" should exist
    When I click on "Enable the Latest badges plugin" "link"
    Then I should see "The Latest badges plugin has been enabled"
    And "Enable the Latest badges plugin" "link" should not exist
    But "Disable the Latest badges plugin" "link" should exist

  Scenario: An administrator can control the enabled state of block plugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Disable the Latest badges plugin" "link"
    Then I should see "The Latest badges plugin has been disabled"
    And "Disable the Latest badges plugin" "link" should not exist
    But "Enable the Latest badges plugin" "link" should exist
    When I click on "Enable the Latest badges plugin" "link"
    Then I should see "The Latest badges plugin has been enabled"
    And "Enable the Latest badges plugin" "link" should not exist
    But "Disable the Latest badges plugin" "link" should exist

  @javascript
  Scenario: An administrator can control the protected state of block plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Protect instances of the Latest badges block" "link"
    Then I should see "The Latest badges block is now protected"
    And "Protect instances of the Latest badges block" "link" should not exist
    But "Unprotect instances of the Latest badges block" "link" should exist
    And "Protect instances of the Activities block" "link" should exist
    When I click on "Unprotect instances of the Latest badges block" "link"
    Then I should see "The Latest badges block is no longer protected"
    And "Unprotect instances of the Activities block" "link" should not exist
    But "Protect instances of the Latest badges block" "link" should exist
    And "Protect instances of the Activities block" "link" should exist

  Scenario: An administrator can control the protected state of block plugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    When I click on "Protect instances of the Latest badges block" "link"
    Then I should see "The Latest badges block is now protected"
    And "Protect instances of the Latest badges block" "link" should not exist
    But "Unprotect instances of the Latest badges block" "link" should exist
    And "Protect instances of the Activities block" "link" should exist
    When I click on "Unprotect instances of the Latest badges block" "link"
    Then I should see "The Latest badges block is no longer protected"
    And "Unprotect instances of the Activities block" "link" should not exist
    But "Protect instances of the Latest badges block" "link" should exist
    And "Protect instances of the Activities block" "link" should exist
