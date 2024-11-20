@core @core_admin @core_webservice
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
      | Name        | User                 | Service                   | IP restriction | Last access |
      | Webservice1 | Firstname1 Lastname1 | Moodle mobile web service | 127.0.0.1      | Never       |

    # Verify the message and the "Copy to clipboard" button.
    And I should see "Copy the token now. It won't be shown again once you leave this page."
    And "Copy to clipboard" "button" should exist

    # New token can only read once.
    And I reload the page
    And I should not see "Copy the token now. It won't be shown again once you leave this page."
    And "Copy to clipboard" "button" should not exist

    # Delete token.
    And I change the window size to "large"
    And I press "Delete" action in the "Webservice1" report row
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
      | user      | service                       | name           | validuntil       |
      | user2     | siteinfo                      | WEBservice1    | ## yesterday ##  |
      | user3     | moodle_mobile_app             | webservicE3    | ## +1 year ##    |
      | user4     | siteinfo                      | New service2   | ## +1 year ##    |
    When I log in as "admin"
    And I navigate to "Server > Web services > Manage tokens" in site administration

    # All created tokens are shown by default.
    And "Firstname1 Lastname1" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"

    # Filter tokens by by name.
    And I click on "Filters" "button"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator   | Contains    |
      | Name value      | webservice  |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And "Firstname4 Lastname4" "table_row" should not exist
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"

    # Filter tokens by user.
    And I set the following fields in the "User" "core_reportbuilder > Filter" to these values:
      | User operator   | Contains            |
      | User value      | Firstname2          |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then "Firstname3 Lastname3" "table_row" should not exist
    And "Firstname4 Lastname4" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"

    # Filter tokens by service.
    And I set the following fields in the "Service" "core_reportbuilder > Filter" to these values:
      | Service value     | Site information |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"
    And "Firstname3 Lastname3" "table_row" should not exist
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"

    # Filter tokens by valid date.
    And I set the following fields in the "Valid until" "core_reportbuilder > Filter" to these values:
      | Valid until operator | Last   |
      | Valid until value    | 2      |
      | Valid until unit     | day(s) |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then "Firstname3 Lastname3" "table_row" should not exist
    And "Firstname4 Lastname4" "table_row" should not exist
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"

    # Reset the filters.
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Site information" in the "Firstname2 Lastname2" "table_row"
    And I should see "Moodle mobile web service" in the "Firstname3 Lastname3" "table_row"
    And I should see "Site information" in the "Firstname4 Lastname4" "table_row"

  @javascript
  Scenario: Tokens table should display missing capabilities
    Given the following "core_webservice > Services" exist:
      | name            | shortname     | enabled |
      | Test Service 1  | testservice1  | 1       |
      | Test Service 2  | testservice2  | 1       |
    And the following "core_webservice > Service functions" exist:
      | service       | functions                           |
      | testservice1  | block_accessreview_get_module_data  |
      | testservice2  | core_block_fetch_addable_blocks     |
    And the following "core_webservice > Tokens" exist:
      | user      | service       | name        |
      | user1     | testservice1  | Token 01    |
      | user2     | testservice2  | Token 02    |
    When I log in as "admin"
    And I navigate to "Server > Web services > Manage tokens" in site administration
    # Check the missing capabilities.
    Then I should see "View the accessibility review" in the "Token 01" "table_row"
    And I should see "block/accessreview:view" in the "Token 01" "table_row"
    Then I should see "Manage blocks on a page" in the "Token 02" "table_row"
    And I should see "moodle/site:manageblocks" in the "Token 02" "table_row"
