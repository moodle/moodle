@core @core_admin @core_reportbuilder
Feature: An administrator can browse user accounts
  In order to find the user accounts I am looking for
  As an admin
  I can browse users and see their basic information

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name           |
      | text     | frog      | Favourite frog |
    And the following "users" exist:
      | username | firstname | lastname | email           | department | profile_field_frog | firstnamephonetic |
      | user1    | User      | One      | one@example.com | Attack     | Kermit             | Yewzer            |
      | user2    | User      | Two      | two@example.com | Defence    | Tree               | Yoozare           |
    And I log in as "admin"

  Scenario: User accounts display default fields
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    # Name field always present, email field is default for showidentity.
    Then the following should exist in the "reportbuilder-table" table:
      | First name           | Email address   |
      | User One             | one@example.com |
      | User Two             | two@example.com |
    # Should not see other identity fields or non-default name fields.
    And I should not see "Department" in the "reportbuilder-table" "table"
    And I should not see "Attack" in the "reportbuilder-table" "table"
    And I should not see "Favourite frog" in the "reportbuilder-table" "table"
    And I should not see "Kermit" in the "reportbuilder-table" "table"
    And I should not see "First name - phonetic" in the "reportbuilder-table" "table"
    And I should not see "Yoozare" in the "reportbuilder-table" "table"

  Scenario: User accounts with extra name fields
    Given the following config values are set as admin:
      | alternativefullnameformat | firstnamephonetic lastname |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name - phonetic           | Email address   |
      | Yewzer One                      | one@example.com |
      | Yoozare Two                     | two@example.com |

  Scenario: User accounts with specified identity fields
    Given the following config values are set as admin:
      | showuseridentity | department,profile_field_frog |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name           | Favourite frog  | Department |
      | User One             | Kermit          | Attack     |
      | User Two             | Tree            | Defence    |
    And I should not see "Email address" in the "table" "css_element"
    And I should not see "one@example.com"

  @javascript
  Scenario: Sort user accounts by custom profile field
    Given the following config values are set as admin:
      | showuseridentity | profile_field_frog |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Favourite frog"
    Then "Kermit" "text" should appear before "Tree" "text"
    And I follow "Favourite frog"
    Then "Tree" "text" should appear before "Kermi" "text"

  @javascript
  Scenario: Edit user information
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Edit" action in the "User One" report row
    And I set the field "Last name" to "OneOne"
    And I press "Update profile"
    Then I should see "User OneOne"

  @javascript
  Scenario: Suspend and activate user account
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Suspend user account" action in the "User One" report row
    And I should see "Suspended" in the "User One" "table_row"
    And I press "Activate user account" action in the "User One" report row
    Then I should not see "Suspended" in the "User One" "table_row"

  @javascript
  Scenario: Delete a user account
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Delete" action in the "User One" report row
    And I should see "Are you sure you want to delete user User One" in the "Delete user" "dialogue"
    And I click on "Delete" "button" in the "Delete user" "dialogue"
    Then I should see "Deleted user User One"
    And I should not see "User One" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Resend email and confirm a user account
    Given the following "users" exist:
      | username | firstname | lastname | email             | confirmed |
      | user3    | User      | Three    | three@example.com | 0         |
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I change window size to "large"
    Then I should see "Confirmation pending" in the "User Three" "table_row"
    And I press "Resend confirmation email" action in the "User Three" report row
    And I should see "Confirmation email sent successfully"
    And I press "Confirm" action in the "User Three" report row
    And I should not see "Confirmation pending" in the "User Three" "table_row"

  @javascript
  Scenario: User report filters
    Given the following "users" exist:
      | username | firstname | lastname | email             | profile_field_frog |
      | user3    | User      | Three    | three@example.com | Glass              |
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name             | Email address     |
      | User One               | one@example.com   |
      | User Two               | two@example.com   |
      | User Three             | three@example.com |
    And I click on "Filters" "button"
    And I set the following fields in the "Last name" "core_reportbuilder > Filter" to these values:
      | Last name operator | Is equal to |
      | Last name value    | Three       |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I click on "Filters" "button"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I set the following fields in the "Favourite frog" "core_reportbuilder > Filter" to these values:
      | Favourite frog operator | Is equal to |
      | Favourite frog value    | Kermit      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"

  @javascript
  Scenario: User report enrolled in any course filter
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user2 | C1     | student |
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name             | Email address     |
      | User One               | one@example.com   |
      | User Two               | two@example.com   |
    And I click on "Filters" "button"
    And I set the following fields in the "Enrolled in any course" "core_reportbuilder > Filter" to these values:
      | Enrolled in any course operator | Yes |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"

  @javascript
  Scenario: User report system role and course role filters
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user3    | User      | Three    | three@example.com |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | user1 | C1     | student        |
      | user2 | C1     | editingteacher |
      | user3 | C1     | student        |
    And the following "role assigns" exist:
      | user  | role          | contextlevel | reference |
      | user1 | coursecreator | system       |           |
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name             | Email address     |
      | User One               | one@example.com   |
      | User Two               | two@example.com   |
      | User Three             | three@example.com |
    And I click on "Filters" "button"
    And I set the field "System role value" in the "System role" "core_reportbuilder > Filter" to "Course creator"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I click on "Filters" "button"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I set the field "Role name" in the "Course role" "core_reportbuilder > Filter" to "Teacher"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"

  Scenario: Add a new user
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "Add a new user" "link"
    Then I should see "Username"
    And I should see "User picture"
    And I should see "Additional names"

  @javascript
  Scenario: Browse user list as a person with limited capabilities
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | manager  | Max       | Manager  | manager@example.com |
    And the following "roles" exist:
      | name           | shortname | description      | archetype |
      | Custom manager | custom1   | My custom role 1 |           |
    And the following "permission overrides" exist:
      | capability             | permission | role    | contextlevel | reference |
      | moodle/site:configview | Allow      | custom1 | System       |           |
      | moodle/user:update     | Allow      | custom1 | System       |           |
    And the following "role assigns" exist:
      | user    | role    | contextlevel | reference |
      | manager | custom1 | System       |           |
    When I log in as "manager"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "User One" "checkbox"
    And the "Bulk user actions" select box should contain "Confirm"
    And the "Bulk user actions" select box should not contain "Delete"
    And I set the field "Bulk user actions" to "Force password change"
    And I should see "Are you absolutely sure you want to force a password change to User One ?"
    And I press "Yes"
    And I press "Continue"
    And I should see "Browse list of users"
