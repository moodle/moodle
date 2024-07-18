@block @block_rss_client
Feature: Enable RSS client block menu on the frontpage
  In order to enable the RSS client block on the frontpage
  As an admin
  I can add RSS client block to the frontpage

  Background:
    Given I log in as "admin"
    When I navigate to "Plugins > Blocks > Manage blocks" in site administration
    Then I enable "rss_client" "block" plugin
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | rss_client     | System       | 1         | site-index      | side-pre      |

  @javascript
  Scenario: Configuring the RSS block on the frontpage
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And "Remote news feed" "block" should exist
    And I configure the "Remote news feed" block
    And I should see "There are no existing RSS feeds configured for this site. You can add one choosing 'Add new RSS feed'."

    # Test filling in an empty URL in the input.
    And I click on "Add new RSS feed" "radio"
    And I press "Save changes"
    And I should see "You must supply a value here."

    # Test filling in with a non-valid URL in the input.
    And I set the field "config_feedurl" to "https://example.com/notvalid.rss"
    And I press "Save changes"
    And I should see "Could not find or load the RSS feed."

    # Test filling in with the correct URL in the input.
    And I set the field "config_feedurl" to "https://www.nasa.gov/rss/dyn/breaking_news.rss"
    And I set the field "config_block_rss_client_show_channel_link" to "Yes"
    And I press "Save changes"
    And I should see "NASA"
    And I should see "Source site..."

    # Test the existence of the available feeds.
    When I configure the "NASA" block
    Then I should see "NASA" in the "Select the feeds to display in this block" "select"
    And I click on "Cancel" "button" in the "Configure NASA block" "dialogue"

    # Test the Manage all my feeds page.
    And I click on "Manage all my feeds" "link"
    And I should see "NASA"
