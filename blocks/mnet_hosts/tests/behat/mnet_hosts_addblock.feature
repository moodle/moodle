@block @block_mnet_hosts @javascript @addablocklink
Feature: Add the network servers block when main feature is enabled
  In order to add the Network servers block to my course
  As a teacher
  It should be added only if the MNet authentication is enabled.

  Scenario: The network servers block can be added when mnet authentication is enabled
    Given I log in as "admin"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Enable" "icon" in the "MNet authentication" "table_row"
    And I am on site homepage
    And I turn editing mode on
    When I click on "Add a block" "link"
    Then I should see "Network servers"

  Scenario: The network servers block cannot be added when mnet authentication is disabled
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I click on "Add a block" "link"
    Then I should not see "Network servers"
