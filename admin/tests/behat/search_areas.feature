@core @core_admin
Feature: Use the search areas admin screen
  In order to control search indexing
  As an admin
  I need to use the search areas admin screen

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Search > Search areas" in site administration

  Scenario: Disable and enable a search area
    When I click on "Disable" "link" in the "Book - resource information" "table_row"
    Then I should see "Search area disabled" in the ".alert-success" "css_element"
    And I should see "Search area disabled" in the "Book - resource information" "table_row"

    When I click on "Enable" "link" in the "Book - resource information" "table_row"
    Then I should see "Search area enabled" in the ".alert-success" "css_element"
    And I should not see "Search area disabled" in the "Book - resource information" "table_row"

  # Note: Other scenarios are not currently easy to implement in Behat because there is no mock
  # search engine - we could add testing once Moodle has an internal database search engine.
