@mod @mod_assign
Feature: Authorised user can view user identity fields in assignments
  In order to view user identity fields within assignments
  As a teacher
  I should be limited depending on permissions and assignment settings

  Background:
    Given the following "custom profile fields" exist:
      | datatype  | shortname           | name                | param1 | param2 | visible |
      | text      | registrationnumber  | Registration Number | 30     | 30     | 3       |
    And the following "users" exist:
      | username | firstname | lastname | email                | profile_field_registrationnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com |                                  |
      | user1    | User      | 1        | user1@example.com    | 12345678901                      |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | user1    | C1     | student        |
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_registrationnumber |

  Scenario: Assignment grading table displays custom identity fields
    Given the following "activities" exist:
      | activity | course | name     | assignsubmission_onlinetext_enabled | blindmarking |
      | assign   | C1     | Assign 1 | 1                                   | 0            |
    And the following "mod_assign > submissions" exist:
      | assign   | user  | onlinetext            |
      | Assign 1 | user1 | user1 submission text |
    When I am on the "Assign 1" Activity page logged in as teacher1
    And I follow "Submissions"
    Then I should see "Registration Number" in the "table" "css_element"
    And I should see "Email" in the "table" "css_element"
    And I should see "12345678901" in the "user1" "table_row"
    And I should see "user1@example.com" in the "user1" "table_row"

  Scenario: Assignment grading table hides custom identity fields when blind marking
    Given the following "activities" exist:
      | activity | course | name     | assignsubmission_onlinetext_enabled | blindmarking |
      | assign   | C1     | Assign 2 | 1                                   | 1            |
    And the following "mod_assign > submissions" exist:
      | assign   | user  | onlinetext            |
      | Assign 2 | user1 | user1 submission text |
    When I am on the "Assign 2" Activity page logged in as teacher1
    And I follow "Submissions"
    Then I should not see "Registration Number" in the "table" "css_element"
    And I should not see "Email" in the "table" "css_element"
    And I should not see "12345678901" in the "table" "css_element"
    And I should not see "user1@example.com" in the "table" "css_element"
    # Anonymouse identifiers.
    And I should see "Identifier" in the "table" "css_element"
    And I should see "Participant" in the "table" "css_element"
