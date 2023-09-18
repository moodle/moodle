@block @block_tag_youtube
Feature: Adding and configuring YouTube block
    In order to have the YouTube block used
    As a admin
    I need to add the YouTube block to the tags site page

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    And I click on "Enable YouTube" "icon" in the "YouTube" "table_row"

  @javascript
  Scenario: Category options are not available (except default) in the block settings if the YouTube API key is not set.
    Given the following config values are set as admin:
      | apikey |  | block_tag_youtube |
    And I follow "Dashboard"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks |  | theme_boost |
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I add the "YouTube" block
    When I configure the "YouTube" block
    Then I should see "Category"
    And I should see "Failed to obtain the list of categories."
    And I should see "The YouTube API key is not set. Contact your administrator."
    And the "Category" select box should contain "Any category"
    And the "Category" select box should not contain "Films & Animation"
    And the "Category" select box should not contain "Entertainment"
    And the "Category" select box should not contain "Education"

  @javascript
  Scenario: Category options are not available (except default) in the block settings when invalid YouTube API key is set.
    Given the following config values are set as admin:
      | apikey | invalidapikeyvalue | block_tag_youtube |
    And I follow "Dashboard"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks |  | theme_boost |
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"
    And I add the "YouTube" block
    When I configure the "YouTube" block
    Then I should see "Category"
    And I should see "Failed to obtain the list of categories."
    And the "Category" select box should contain "Any category"
    And the "Category" select box should not contain "Comedy"
    And the "Category" select box should not contain "Autos & Vehicles"
    And the "Category" select box should not contain "News & Politics"
