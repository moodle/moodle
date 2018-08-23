@tool @tool_dataprivacy
Feature: Data export from the privacy API
  In order to export data for users and meet legal requirements
  As an admin, user, or parent
  I need to be able to export data for a user

  Background:
    Given the following "users" exist:
      | username | firstname      | lastname |
      | victim   | Victim User    | 1        |
      | parent   | Long-suffering | Parent   |
    And the following "roles" exist:
      | shortname | name  | archetype |
      | tired     | Tired |           |
    And the following "permission overrides" exist:
      | capability                                   | permission | role  | contextlevel | reference |
      | tool/dataprivacy:makedatarequestsforchildren | Allow      | tired | System       |           |
    And the following "role assigns" exist:
      | user   | role  | contextlevel | reference |
      | parent | tired | User         | victim    |
    And the following config values are set as admin:
      | contactdataprotectionofficer | 1 | tool_dataprivacy |

  @javascript
  Scenario: As admin, export data for a user and download it
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I set the field "Requesting for" to "Victim User 1"
    And I press "Save changes"
    Then I should see "Victim User 1"
    And I should see "Pending" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Awaiting approval" in the "Victim User 1" "table_row"
    And I follow "Actions"
    And I follow "Approve request"
    And I press "Approve request"
    And I should see "Approved" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Complete" in the "Victim User 1" "table_row"
    And I follow "Actions"
    And following "Download" should download between "1" and "100000" bytes

  @javascript
  Scenario: As a student, request data export and then download it when approved
    Given I log in as "victim"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    And I press "Save changes"
    Then I should see "Export all of my personal data"
    And I should see "Pending" in the "Export all of my personal data" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Awaiting approval" in the "Export all of my personal data" "table_row"

    And I log out
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "Actions"
    And I follow "Approve request"
    And I press "Approve request"

    And I log out
    And I log in as "victim"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I should see "Approved" in the "Export all of my personal data" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Complete" in the "Export all of my personal data" "table_row"
    And I follow "Actions"
    And following "Download" should download between "1" and "100000" bytes

  @javascript
  Scenario: As a parent, request data export for my child because I don't trust the little blighter
    Given I log in as "parent"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    And I set the field "Requesting for" to "Victim User 1"
    And I press "Save changes"
    Then I should see "Victim User 1"
    And I should see "Pending" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Awaiting approval" in the "Victim User 1" "table_row"

    And I log out
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "Actions"
    And I follow "Approve request"
    And I press "Approve request"

    And I log out
    And I log in as "parent"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I should see "Approved" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Complete" in the "Victim User 1" "table_row"
    And I follow "Actions"
    And following "Download" should download between "1" and "100000" bytes
