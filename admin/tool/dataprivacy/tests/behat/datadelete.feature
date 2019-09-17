@tool @tool_dataprivacy
Feature: Data delete from the privacy API
  In order to delete data for users and meet legal requirements
  As an admin, user, or parent
  I need to be able to request a user and their data data be deleted

  Background:
    Given the following "users" exist:
      | username       | firstname       | lastname |
      | victim         | Victim User     | 1        |
      | parent         | Long-suffering  | Parent   |
      | privacyofficer | Privacy Officer | One      |
    And the following "roles" exist:
      | shortname | name  | archetype |
      | tired     | Tired |           |
    And the following "permission overrides" exist:
      | capability                                           | permission | role    | contextlevel | reference |
      | tool/dataprivacy:makedatarequestsforchildren         | Allow      | tired   | System       |           |
      | tool/dataprivacy:makedatadeletionrequestsforchildren | Allow      | tired   | System       |           |
      | tool/dataprivacy:managedatarequests                  | Allow      | manager | System       |           |
    And the following "role assigns" exist:
      | user   | role  | contextlevel | reference |
      | parent | tired | User         | victim    |
    And the following "system role assigns" exist:
      | user           | role    | contextlevel |
      | privacyofficer | manager | User         |
    And the following config values are set as admin:
      | contactdataprotectionofficer | 1  | tool_dataprivacy |
    And the following data privacy "categories" exist:
      | name          |
      | Site category |
    And the following data privacy "purposes" exist:
      | name         | retentionperiod |
      | Site purpose | P10Y           |
    And the following config values are set as admin:
      | contactdataprotectionofficer | 1  | tool_dataprivacy |
      | privacyrequestexpiry         | 55 | tool_dataprivacy |
      | dporoles                     | 1  | tool_dataprivacy |
    And I set the site category and purpose to "Site category" and "Site purpose"

  @javascript
  Scenario: As admin, delete a user and their data
    Given I log in as "victim"
    And I should see "Victim User 1"
    And I log out

    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I set the field "User" to "Victim User 1"
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    Then I should see "Victim User 1"
    And I should see "Awaiting approval" in the "Victim User 1" "table_row"
    And I open the action menu in "Victim User 1" "table_row"
    And I follow "Approve request"
    And I press "Approve request"
    And I should see "Approved" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Deleted" in the "Victim User 1" "table_row"

    And I log out
    And I log in as "victim"
    And I should see "Invalid login"

  @javascript
  Scenario: As a student, request deletion of account and data
    Given I log in as "victim"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    Then I should see "Delete all of my personal data"
    And I should see "Awaiting approval" in the "Delete all of my personal data" "table_row"

    And I log out
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I open the action menu in "Victim User 1" "table_row"
    And I follow "Approve request"
    And I press "Approve request"

    And I log out
    And I log in as "victim"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I should see "Approved" in the "Delete all of my personal data" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "Your session has timed out"
    And I log in as "victim"
    And I should see "Invalid login"

    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I should see "Deleted"

  @javascript
  Scenario: As a parent, request account and data deletion for my child
    Given I log in as "parent"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    And I set the field "User" to "Victim User 1"
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    Then I should see "Victim User 1"
    And I should see "Awaiting approval" in the "Victim User 1" "table_row"

    And I log out
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I open the action menu in "Victim User 1" "table_row"
    And I follow "Approve request"
    And I press "Approve request"

    And I log out
    And I log in as "parent"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I should see "Approved" in the "Victim User 1" "table_row"
    And I run all adhoc tasks
    And I reload the page
    And I should see "You don't have any personal data requests"

  @javascript
  Scenario: As a Privacy Officer, I cannot create data deletion request unless I have permission.
    Given I log in as "privacyofficer"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I open the autocomplete suggestions list
    And I click on "Victim User 1" item in the autocomplete list
    Then I should see "Export all of my personal data"
    And "Type" "select" should not be visible
    And the following "permission overrides" exist:
      | capability                                 | permission | role    | contextlevel | reference |
      | tool/dataprivacy:requestdeleteforotheruser | Allow      | manager | System       |           |
    And I reload the page
    And I open the autocomplete suggestions list
    And I click on "Victim User 1" item in the autocomplete list
    And "Type" "select" should be visible

  @javascript
  Scenario: As a student, I cannot create data deletion request unless I have permission.
    Given I log in as "victim"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    Then "Type" "select" should exist
    And the following "permission overrides" exist:
      | capability                     | permission | role | contextlevel | reference |
      | tool/dataprivacy:requestdelete | Prevent    | user | System       |           |
    And I reload the page
    And I should see "Export all of my personal data"
    And "Type" "select" should not exist

  @javascript
  Scenario: As a parent, I cannot create data deletion request unless I have permission.
    Given I log in as "parent"
    And the following "permission overrides" exist:
      | capability                                           | permission | role  | contextlevel | reference |
      | tool/dataprivacy:makedatadeletionrequestsforchildren | Prevent    | tired | System       | victim    |
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    And I follow "New request"
    And I open the autocomplete suggestions list
    And I click on "Victim User 1" item in the autocomplete list
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    And I should see "You don't have permission to create deletion request for this user."
    And the following "permission overrides" exist:
      | capability                                           | permission | role  | contextlevel | reference |
      | tool/dataprivacy:makedatadeletionrequestsforchildren | Allow      | tired | System       | victim    |
      | tool/dataprivacy:requestdelete                       | Prevent    | user  | System       |           |
    And I open the autocomplete suggestions list
    And I click on "Long-suffering Parent" item in the autocomplete list
    And I press "Save changes"
    And I should see "You don't have permission to create deletion request for yourself."

  @javascript
  Scenario: As a student, link to create data deletion should not be shown if I don't have permission.
    Given the following "permission overrides" exist:
      | capability                     | permission | role | contextlevel | reference |
      | tool/dataprivacy:requestdelete | Prohibit   | user | System       |           |
    When I log in as "victim"
    And I follow "Profile" in the user menu
    Then I should not see "Delete my account"

  @javascript
  Scenario: As a primary admin, the link to create a data deletion request should not be shown.
    Given I log in as "admin"
    When I follow "Profile" in the user menu
    Then I should not see "Delete my account"

  @javascript
  Scenario: As a Privacy Officer, I cannot Approve to Deny deletion data request without permission.
    Given the following "permission overrides" exist:
      | capability                                 | permission | role    | contextlevel | reference |
      | tool/dataprivacy:requestdeleteforotheruser | Allow      | manager | System       |           |
    When I log in as "privacyofficer"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I open the autocomplete suggestions list
    And I click on "Victim User 1" item in the autocomplete list
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    And the following "permission overrides" exist:
      | capability                                 | permission | role    | contextlevel | reference |
      | tool/dataprivacy:requestdeleteforotheruser | Prohibit   | manager | System       |           |
    And I reload the page
    Then ".selectrequests" "css_element" should not exist
    And I open the action menu in "region-main" "region"
    And I should not see "Approve request"
    And I should not see "Deny request"
    And I choose "View the request" in the open action menu
    And "Approve" "button" should not exist
    And "Deny" "button" should not exist

  @javascript
  Scenario: As a Privacy Officer, I cannot re-submit deletion data request without permission.
    Given the following "permission overrides" exist:
      | capability                                 | permission | role    | contextlevel | reference |
      | tool/dataprivacy:requestdeleteforotheruser | Allow      | manager | System       |           |
    When I log in as "privacyofficer"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I follow "New request"
    And I open the autocomplete suggestions list
    And I click on "Victim User 1" item in the autocomplete list
    And I set the field "Type" to "Delete all of my personal data"
    And I press "Save changes"
    And I open the action menu in "region-main" "region"
    And I follow "Deny request"
    And I press "Deny request"
    And the following "permission overrides" exist:
      | capability                                 | permission | role    | contextlevel | reference |
      | tool/dataprivacy:requestdeleteforotheruser | Prohibit   | manager | System       |           |
    And I reload the page
    And I open the action menu in "region-main" "region"
    Then I should not see "Resubmit as new request"
