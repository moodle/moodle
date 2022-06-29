@core @core_admin
Feature: Manage external services tokens
  In order to manage external service usage
  As an admin
  I need to be able to create, filter and delete tokens

  Background:
    Given the following "users" exist:
      | username  | password  | firstname     | lastname    |
      | user1     | user1     | Firstname1    | Lastname1   |
      | user2     | user2     | Firstname2    | Lastname2   |
      | user3     | user3     | Firstname3    | Lastname3   |
      | user4     | user4     | Firstname4    | Lastname4   |
    And I change window size to "small"

  @javascript
  Scenario: Add a token to user identified by name and then delete that token
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Server > Web services > Manage tokens" in site administration
    And I press "Create token"
    And I set the field "User" to "Firstname1 Lastname1"
    And I set the field "Service" to "Moodle mobile web service"
    And I set the field "IP restriction" to "127.0.0.1"
    When I press "Save changes"
    Then I should see "Moodle mobile web service" in the "Firstname1 Lastname1" "table_row"
    And I should see "127.0.0.1" in the "Firstname1 Lastname1" "table_row"
    And I click on "Delete" "link" in the "Firstname1 Lastname1" "table_row"
    And I should see "Do you really want to delete this web service token for Firstname1 Lastname1 on the service Moodle mobile web service?"
    And I press "Delete"
    And "Firstname1 Lastname1" "table_row" should not exist

  @javascript @skip_chrome_zerosize
  Scenario: Tokens can be filtered by user and by service
    Given the following "core_webservice > Service" exists:
      | name      | Site information              |
      | shortname | siteinfo                      |
      | enabled   | 1                             |
    And the following "core_webservice > Service function" exists:
      | service   | siteinfo                      |
      | functions | core_webservice_get_site_info |
    And the following "core_webservice > Tokens" exist:
      | user      | service                       |
      | user2     | siteinfo                      |
      | user3     | moodle_mobile_app             |
      | user4     | siteinfo                      |
    When I log in as "admin"
    And I navigate to "Server > Web services > Manage tokens" in site administration

    # All created tokens are shown by default.
    And "Firstname1 Lastname1" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"

    # Filter tokens by user (note we can select the user by the identity field here).
    When I click on "Tokens filter" "link"
    And I set the field "User" to "user2@example.com"
    And I press "Show only matching tokens"
    Then "Firstname3 Lastname3" "table_row" should not exist
    And "Firstname4 Lastname4" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"

    # Reset the filter.
    And I press "Show all tokens"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"

    # Filter tokens by service.
    And I click on "Tokens filter" "link"
    And I set the field "Service" to "Site information"
    And I press "Show only matching tokens"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"
    And "Firstname3 Lastname3" "table_row" should not exist
