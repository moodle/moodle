@core @core_admin
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
    Then the following should exist in the "users" table:
      | First name / Surname | Email address   |
      | User One             | one@example.com |
      | User Two             | two@example.com |
    # Should not see other identity fields or non-default name fields.
    And I should not see "Department" in the "table" "css_element"
    And I should not see "Attack"
    And I should not see "Favourite frog" in the "table" "css_element"
    And I should not see "Kermit"
    And I should not see "First name - phonetic" in the "table" "css_element"
    And I should not see "Yoozare"

  Scenario: User accounts with extra name fields
    Given the following config values are set as admin:
      | alternativefullnameformat | firstnamephonetic lastname |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "users" table:
      | First name - phonetic / Surname | Email address   |
      | Yewzer One                      | one@example.com |
      | Yoozare Two                     | two@example.com |

  Scenario: User accounts with specified identity fields
    Given the following config values are set as admin:
      | showuseridentity | department,profile_field_frog |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    Then the following should exist in the "users" table:
      | First name / Surname | Favourite frog  | Department |
      | User One             | Kermit          | Attack     |
      | User Two             | Tree            | Defence    |
    And I should not see "Email address" in the "table" "css_element"
    And I should not see "one@example.com"

  Scenario: Sort user accounts by custom profile field
    Given the following config values are set as admin:
      | showuseridentity | profile_field_frog |
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Favourite frog"
    Then "Kermit" "text" should appear before "Tree" "text"
    And I follow "Favourite frog"
    Then "Tree" "text" should appear before "Kermi" "text"
