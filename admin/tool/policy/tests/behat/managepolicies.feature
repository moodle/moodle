@tool @tool_policy
Feature: Manage policies
  In order to manage policies
  As a manager
  I need to be able to create and edit site policies

  Background:
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | User      | One      | one@example.com |
      | user2    | User      | Two      | two@example.com |
      | manager  | Max       | Manager  | man@example.com |
    And the following "role assigns" exist:
      | user    | role           | contextlevel | reference |
      | manager | manager        | System       |           |

  Scenario: Create new policy and save as draft
    When I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I follow "New policy"
    And I set the following fields to these values:
      | Name        | Policy1        |
      | Version     | v1             |
      | Summary     | Policy summary |
      | Full policy | Full text      |
    And the field "Type" matches value "Site policy"
    And the field "User consent" matches value "All users"
    And the field "status" matches value "0"
    And "Draft" "field" should exist
    And "Active" "field" should exist
    And "Minor change" "field" should not exist
    And I should not see "Minor change"
    And "Save as draft" "button" should not exist
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version | Agreements |
      | Policy1 Site policy, All users | Draft         | v1      | N/A        |
    And I log out

  Scenario: Create new policy and save as active
    When I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I follow "New policy"
    And I set the following fields to these values:
      | Name        | Policy1        |
      | Version     | v1             |
      | Summary     | Policy summary |
      | Full policy | Full text      |
      | Active      | 1              |
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version | Agreements  |
      | Policy1 Site policy, All users | Active        | v1      | 0 of 4 (0%) |
    And I log out

  Scenario: Edit active policy and save as minor change
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | active   |
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the Policy1" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Edit" "link" in the "Policy1" "table_row"
    And "Draft" "field" should not exist
    And "Active" "field" should not exist
    And "Minor change" "field" should exist
    And "Save as draft" "button" should exist
    And I set the field "Version" to "v1 amended"
    And I set the field "Minor change" to "1"
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Active        | v1 amended | 1 of 4 (25%) |
    And I log out

  Scenario: Edit active policy and save as draft
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | active   |
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the Policy1" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Edit" "link" in the "Policy1" "table_row"
    And I set the field "Version" to "v2"
    And I press "Save as draft"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Active        | v1         | 1 of 4 (25%) |
      | Policy1 Site policy, All users | Draft         | v2         | N/A          |
    And I log out

  Scenario: Edit active policy and save as new active version
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | active   |
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the Policy1" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Edit" "link" in the "Policy1" "table_row"
    And I set the field "Name" to "Policy2"
    And I set the field "Version" to "v2"
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy2 Site policy, All users | Active        | v2         | 0 of 4 (0%) |
    And I should not see "Policy1"
    And I should not see "v1"
    And I open the action menu in "Policy2" "table_row"
    And I click on "View previous versions" "link" in the "Policy2" "table_row"
    And I should see "Policy2 previous versions"
    And I should not see "v2"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Inactive      | v1         | 1 of 4 (25%) |
    And I log out

  Scenario: Edit draft policy and save as draft
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | draft    |
    And I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Edit" "link" in the "Policy1" "table_row"
    And I set the field "Version" to "v2"
    And "Draft" "field" should exist
    And "Active" "field" should exist
    And "Minor change" "field" should not exist
    And I should not see "Minor change"
    And "Save as draft" "button" should not exist
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Draft         | v2         | N/A          |
    And I should not see "v1"
    And I open the action menu in "Policy1" "table_row"
    And "View previous versions" "link" should not exist
    And I log out

  Scenario: Edit draft policy and save as active
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | draft    |
    And I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Edit" "link" in the "Policy1" "table_row"
    And I set the field "Version" to "v2"
    And I set the field "Active" to "1"
    And I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Active        | v2         | 0 of 4 (0%)  |
    And I should not see "v1"
    And I open the action menu in "Policy1" "table_row"
    And "View previous versions" "link" should not exist
    And I log out

  Scenario: Activate draft policy
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | draft    |
    And I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Set status to \"Active\"" "link" in the "Policy1" "table_row"
    Then I should see "All users will be required to agree to this new policy version to be able to use the site."
    And I press "Continue"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Active        | v1         | 0 of 4 (0%)  |
    And I open the action menu in "Policy1" "table_row"
    And "View previous versions" "link" should not exist
    And I log out

  Scenario: Edit archived policy and save as draft
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | active   |
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the Policy1" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Set status to \"Inactive\"" "link" in the "Policy1" "table_row"
    Then I should see "You are about to inactivate policy"
    And I press "Continue"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Inactive      | v1         | 1 of 4 (25%) |
    And I open the action menu in "Policy1" "table_row"
    And I click on "Create a new draft" "link" in the "Policy1" "table_row"
    And I set the field "Version" to "v2"
    And I set the field "Name" to "Policy2"
    And the field "status" matches value "0"
    And "Draft" "field" should exist
    And "Active" "field" should exist
    And "Minor change" "field" should not exist
    And I should not see "Minor change"
    And "Save as draft" "button" should not exist
    And I press "Save"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy2 Site policy, All users | Draft         | v2         | N/A          |
    And I should not see "v1"
    And I should not see "Policy1"
    And I open the action menu in "Policy2" "table_row"
    And I click on "View previous versions" "link" in the "Policy2" "table_row"
    And I should see "Policy2 previous versions"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Inactive      | v1         | 1 of 4 (25%) |
    And I should not see "v2"
    And I log out

  Scenario: Edit archived policy and save as active
    Given the following policies exist:
      | Name       | Revision | Content    | Summary     | Status   |
      | Policy1    | v1       | full text2 | short text2 | active   |
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the Policy1" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I open the action menu in "Policy1" "table_row"
    And I click on "Set status to \"Inactive\"" "link" in the "Policy1" "table_row"
    And I press "Continue"
    And I open the action menu in "Policy1" "table_row"
    And I click on "Create a new draft" "link" in the "Policy1" "table_row"
    And I set the field "Version" to "v2"
    And I set the field "Name" to "Policy2"
    And I set the field "Active" to "1"
    And I press "Save"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy2 Site policy, All users | Active        | v2         | 0 of 4 (0%)  |
    And I should not see "v1"
    And I should not see "Policy1"
    And I open the action menu in "Policy2" "table_row"
    And I click on "View previous versions" "link" in the "Policy2" "table_row"
    And I should see "Policy2 previous versions"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                           | Policy status | Version    | Agreements   |
      | Policy1 Site policy, All users | Inactive      | v1         | 1 of 4 (25%) |
    And I should not see "v2"
    And I log out
