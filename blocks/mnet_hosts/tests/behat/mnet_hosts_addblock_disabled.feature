@block @block_mnet_hosts @javascript
Feature: Add the network servers block when main feature is disabled
  In order to add the Network servers block to my course
  As a teacher
  It should be added only if the MNet authentication is enabled.

  Scenario: The network servers block is displayed even when mnet authentication is disabled
    Given I log in as "admin"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Enable" "icon" in the "MNet authentication" "table_row"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Network servers" block
    When I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Disable" "icon" in the "MNet authentication" "table_row"
    And I am on site homepage
    And I turn editing mode on
    Then I should see "Network servers"

  Scenario: The network servers block can be removed even when mnet authentication is disabled
    Given I log in as "admin"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Enable" "icon" in the "MNet authentication" "table_row"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Network servers" block
    And I turn editing mode on
    And I open the "Network servers" blocks action menu
    And I click on "Delete Network servers block" "link" in the "Network servers" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And I should see "Network servers"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Disable" "icon" in the "MNet authentication" "table_row"
    And I am on site homepage
    And I turn editing mode on
    And I open the "Network servers" blocks action menu
    And I click on "Delete Network servers block" "link" in the "Network servers" "block"
    And "Delete block?" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    Then I should not see "Network servers"
