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
    And I set the field "Name" to "Webservice1"
    And I set the field "User" to "Firstname1 Lastname1"
    And I set the field "Service" to "Moodle mobile web service"
    And I set the field "IP restriction" to "127.0.0.1"
    When I press "Save changes"
    Then the following should exist in the "generaltable" table:
      | Name        | First name           | Service                   | IP restriction | Last access |
      | Webservice1 | Firstname1 Lastname1 | Moodle mobile web service | 127.0.0.1      | Never       |

    # Verify the message and the "Copy to clipboard" button.
    And I should see "Copy the token now. It won't be shown again once you leave this page."
    And "Copy to clipboard" "button" should exist

    # New token can only read once.
    And I reload the page
    And I should not see "Copy the token now. It won't be shown again once you leave this page."
    And "Copy to clipboard" "button" should not exist

    # Delete token.
    And I click on "Delete" "link" in the "Webservice1" "table_row"
    And I should see "Do you really want to delete this web service token for Firstname1 Lastname1 on the service Moodle mobile web service?"
    And I press "Delete"
    And "Webservice1" "table_row" should not exist

  @javascript @skip_chrome_zerosize
  Scenario: Tokens can be filtered by name (case-insensitive), by user and by service
    Given the following "core_webservice > Service" exists:
      | name      | Site information              |
      | shortname | siteinfo                      |
      | enabled   | 1                             |
    And the following "core_webservice > Service function" exists:
      | service   | siteinfo                      |
      | functions | core_webservice_get_site_info |
    And the following "core_webservice > Tokens" exist:
      | user      | service                       | name           |
      | user2     | siteinfo                      | WEBservice1    |
      | user3     | moodle_mobile_app             | webservicE3     |
      | user4     | siteinfo                      | New service2   |
    When I log in as "admin"
    And I navigate to "Server > Web services > Manage tokens" in site administration

    # All created tokens are shown by default.
    And "Firstname1 Lastname1" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"

    # Filter tokens by by name (case-insensitive).
    And I click on "Tokens filter" "link"
    And I set the field "Name" to "webservice"
    And I press "Show only matching tokens"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And "Firstname4 Lastname4" "table_row" should not exist

    # Reset the filter.
    And I press "Show all tokens"
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
