@core @core_admin
Feature: Web service user settings
  In order to configure authorised users for a web service
  As an admin
  I need to use the page that lets you do that

  Background:
    # Include a custom profile field so we can check it gets displayed
    Given the following "custom profile fields" exist:
      | datatype | shortname | name           | param2 |
      | text     | frog      | Favourite frog | 100    |
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_frog |
    And the following "users" exist:
      | username | firstname | lastname | email         | profile_field_frog |
      | user1    | User      | One      | 1@example.org | Kermit             |
    And the following "core_webservice > Service" exists:
      | name            | Silly service |
      | shortname       | silly         |
      | restrictedusers | 1             |
      | enabled         | 1             |

  Scenario: Add a user to a web service
    When I log in as "admin"
    And I navigate to "Server > Web services > External services" in site administration
    And I click on "Authorised users" "link" in the "Silly service" "table_row"
    And I set the field "Not authorised users" to "User One"
    And I press "Add"
    Then I should see "User One" in the ".alloweduserlist" "css_element"
    And I should see "1@example.org" in the ".alloweduserlist" "css_element"
    And I should see "Kermit" in the ".alloweduserlist" "css_element"
