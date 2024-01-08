@core @core_cohort
Feature: Add cohorts of users
  In order to create site-wide groups
  As an admin
  I need to create cohorts and add users on them

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username | firstname | lastname | email              | profile_field_fruit |
      | user1    | First     | User     | first@example.com  | Apple               |
      | user2    | Second    | User     | second@example.com | Banana              |
      | user3    | Third     | User     | third@example.com  | Apple               |
      | user4    | Forth     | User     | forth@example.com  | Pear                |
    And the following "cohort" exists:
      | name        | Test cohort name        |
      | idnumber    | 333                     |
      | description | Test cohort description |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration

  @javascript
  Scenario: Add a cohort
    When I follow "Add new cohort"
    And I set the following fields to these values:
      | Name        | My new cohort         |
      | Context     | System                |
      | Cohort ID   | mynewcohort           |
      | Description | My new cohort is cool |
    And I press "Save changes"
    Then the following should exist in the "generaltable" table:
      | Name          | Cohort ID   | Description           |
      | My new cohort | mynewcohort | My new cohort is cool |
    And I should see "Test cohort name"
    And I should see "333"
    And I should see "Test cohort description"
    And I should see "Created manually"

  @javascript
  Scenario: Add users to a cohort selecting them from the system users list
    When I add "First User (first@example.com)" user to "333" cohort members
    And I add "Second User (second@example.com)" user to "333" cohort members
    Then the following should exist in the "reportbuilder-table" table:
      | Name              | Cohort size  |
      | Test cohort name  | 2            |
    And I press "Assign" action in the "Test cohort name" report row
    And the "Current users" select box should contain "First User (first@example.com)"
    And the "Current users" select box should contain "Second User (second@example.com)"
    And the "Current users" select box should not contain "Forth User (forth@example.com)"

  @javascript
  Scenario: Add user to cohort using custom user field search
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    When I press "Assign" action in the "Test cohort name" report row
    And I set the field "addselect_searchtext" to "Apple"
    And I wait "1" seconds
    Then the "Potential users" select box should contain "First User (first@example.com\, Apple)"
    And the "Potential users" select box should not contain "Second User (second@example.com\, Banana)"
    And the "Potential users" select box should not contain "Forth User (forth@example.com\, Pear)"
    And I set the field "Potential users" to "Third User (third@example.com\, Apple)"
    And I press "Add"
    And the "Current users" select box should contain "Third User (third@example.com\, Apple)"

  @javascript
  Scenario: Remove user from cohort using custom user field search
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And the following "cohort members" exist:
      | cohort | user  |
      | 333    | user1 |
      | 333    | user2 |
    When I press "Assign" action in the "Test cohort name" report row
    And I set the field "removeselect_searchtext" to "Apple"
    And I wait "1" seconds
    Then the "Current users" select box should not contain "Second User (second@example.com\, Banana)"
    And I set the field "Current users" to "First User (first@example.com\, Apple)"
    And I press "Remove"
    And the "Potential users" select box should contain "First User (first@example.com\, Apple)"

  @javascript
  Scenario: Add users to a cohort using a bulk user action
    When I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "Third User"
    And I press "Add to selection"
    And I set the field "Available" to "Forth User"
    And I press "Add to selection"
    And I set the field "id_action" to "Add to cohort"
    And I press "Go"
    And I set the field "Cohort" to "Test cohort name [333]"
    And I press "Add to cohort"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | Name              | Cohort size  |
      | Test cohort name  | 2            |
    And I press "Assign" action in the "Test cohort name" report row
    And the "Current users" select box should contain "Third User (third@example.com)"
    And the "Current users" select box should contain "Forth User (forth@example.com)"
    And the "Current users" select box should not contain "First User (first@example.com)"

  @javascript
  Scenario: Add users to a cohort using a user list bulk action
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "Third User" "checkbox"
    And I click on "Forth User" "checkbox"
    And I set the field "Bulk user actions" to "Add to cohort"
    And I set the field "Cohort" to "Test cohort name [333]"
    And I press "Add to cohort"
    And I should see "Browse list of users"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | Name              | Cohort size  |
      | Test cohort name  | 2            |
    And I press "Assign" action in the "Test cohort name" report row
    And the "Current users" select box should contain "Third User (third@example.com)"
    And the "Current users" select box should contain "Forth User (forth@example.com)"
    And the "Current users" select box should not contain "First User (first@example.com)"

  @javascript
  Scenario: Edit cohort name in-place
    When I navigate to "Users > Accounts > Cohorts" in site administration
    And I set the field "Edit cohort name" to "Students cohort"
    Then I should not see "Test cohort name"
    And I should see "Students cohort"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I should see "Students cohort"
