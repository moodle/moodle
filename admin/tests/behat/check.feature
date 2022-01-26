@core @core_admin
Feature: Staff can check user permissions
  In order to find out whether a user can or can't do something
  As an admin
  I can check their permissions in a particular context

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name           |
      | text     | frog      | Favourite frog |
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_frog |
    And the following "users" exist:
      | username | firstname | lastname | email           | profile_field_frog |
      | user1    | User      | One      | one@example.com | Kermit             |
      | user2    | User      | Two      | two@example.com | Tree               |
      | user3    | User      | Three    | thr@example.com | Kermit             |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
      | C2        | Course 2 |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | user1 | C1     | editingteacher |
      | user2 | C1     | editingteacher |
      | user3 | C2     | editingteacher |

  @javascript
  Scenario: Search for a user (enrolled on the course) by custom field and select them to see permissions
    When I am on the "C1" "permissions" page logged in as "admin"
    And I select "Check permissions" from the "jump" singleselect
    And I set the field "Search" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label='Matching enrolled users (1)']" "css_element" should exist
    And I should see "User One (one@example.com, Kermit)"
    And I should not see "User Two"
    And I set the field "reportuser" to "User One (one@example.com, Kermit)"
    And I press "Show this user's permissions"
    And I should see "Permissions for user User One"
    And I should see "Yes" in the "Add a new forum" "table_row"

  @javascript
  Scenario: Search for a user (not enrolled on the course) by custom field and select them to see permissions
    When I am on the "C1" "permissions" page logged in as "admin"
    And I select "Check permissions" from the "jump" singleselect
    And I set the field "Search" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label*='Potential users matching'][label*=' (1)']" "css_element" should exist
    And I should see "User Three (thr@example.com, Kermit)"
    And I should not see "User Two"
    And I set the field "reportuser" to "User Three (thr@example.com, Kermit)"
    And I press "Show this user's permissions"
    Then I should see "Permissions for user User Three"
    And I should see "No" in the "Add a new forum" "table_row"
