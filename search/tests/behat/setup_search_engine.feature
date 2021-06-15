@core @core_search
Feature: Plugins > Search > Search setup contains Setup search engine only if the target section actually exists
  In order to set up the selected search engine
  As an admin
  I need to be able to click the link 'Setup search engine' but only if the target section actually exists

  Scenario: Selected search engine has an admin section
    Given the following config values are set as admin:
      | enableglobalsearch | 1        |
      | searchengine       | solr     |
    And I log in as "admin"
    When I navigate to "Plugins > Search" in site administration
    Then "Setup search engine" "link" should exist

  Scenario: Selected search engine does not have an admin section
    Given the following config values are set as admin:
      | enableglobalsearch | 1        |
      | searchengine       | simpledb |
    And I log in as "admin"
    When I navigate to "Plugins > Search" in site administration
    Then I should see "Setup search engine"
    And "Setup search engine" "link" should not exist
