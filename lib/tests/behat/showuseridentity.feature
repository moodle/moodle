@core
Feature: Select user identity fields
  In order to see who users are at my institution
  As an administrator
  I can configure which user fields show with lists of users

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname  | name       | param2 |
      | text     | speciality | Speciality | 255    |
      | checkbox | fool       | Foolish    |        |
      | text     | thesis     | Thesis     | 100000 |
    And the following "users" exist:
      | username | department | profile_field_speciality | email              |
      | user1    | Amphibians | Frogs                    | email1@example.org |
      | user2    | Undead     | Zombies                  | email2@example.org |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | manager |
      | user2 | C1     | manager |

  Scenario: The admin settings screen should show text custom fields of certain length (and let you choose them)
    When I log in as "admin"
    And I navigate to "Users > Permissions > User policies" in site administration
    Then I should see "Speciality" in the "#admin-showuseridentity" "css_element"
    And I should not see "Foolish" in the "#admin-showuseridentity" "css_element"
    And I should not see "Thesis" in the "#admin-showuseridentity" "css_element"
    And I set the field "Speciality" to "1"
    And I press "Save changes"
    And the field "Speciality" matches value "1"

  Scenario: The admin settings screen correctly formats custom field names
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "custom profile field" exists:
      | datatype  | text  |
      | name      | <span class="multilang" lang="en">Field (EN)</span><span class="multilang" lang="de">Field (DE)</span> |
      | shortname | stuff |
      | param2    | 100   |
    When I log in as "admin"
    And I navigate to "Users > Permissions > User policies" in site administration
    Then I should see "Field (EN)" in the "#admin-showuseridentity" "css_element"
    And I should not see "Field (DE)" in the "#admin-showuseridentity" "css_element"

  Scenario: When you choose custom fields, these should be displayed in the 'Browse list of users' screen
    Given the following config values are set as admin:
      | showuseridentity | username,department,profile_field_speciality |
    When I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    Then I should see "Speciality" in the "thead" "css_element"
    And I should see "Department" in the "thead" "css_element"
    And I should not see "Email" in the "thead" "css_element"
    Then I should see "Amphibians" in the "user1" "table_row"
    And I should see "Frogs" in the "user1" "table_row"
    And I should not see "email1@example.org"
    And I should see "Undead" in the "user2" "table_row"
    And I should see "Zombies" in the "user2" "table_row"
    And I should not see "email2@example.org"

  Scenario: When you choose custom fields, these should be displayed in the 'Participants' screen
    Given the following config values are set as admin:
      | showuseridentity | username,department,profile_field_speciality |
    When I am on the "C1" "Course" page logged in as "user1"
    And I navigate to course participants
    Then I should see "Frogs" in the "user1" "table_row"
    And I should see "Zombies" in the "user2" "table_row"

  @javascript
  Scenario: The user filtering options on the participants screen should work for custom profile fields
    Given the following config values are set as admin:
      | showuseridentity | username,department,profile_field_speciality |
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." in the "Filter 1" "fieldset" to "Frogs"
    # You have to tab out to make it actually apply.
    And I press tab
    And I click on "Apply filters" "button"
    Then I should see "user1" in the "participants" "table"
    And I should not see "user2" in the "participants" "table"
