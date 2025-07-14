@core @core_admin
Feature: Staff can assign user roles
  In order to assign users to roles at site or activity module level
  As an admin
  I can add and remove users from the roles

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
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
    And the following "activities" exist:
      | activity | name     | course | idnumber |
      | page     | TestPage | C1     | page1    |

  @javascript
  Scenario: Enrol at system level using custom field search
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Users > Permissions > Assign system roles" in site administration
    And I follow "Manager"
    And I set the field "addselect_searchtext" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label*='Potential users matching'][label*=' (1)']" "css_element" should exist
    And I set the field "addselect" to "User One (one@example.com, Kermit)"
    And I press "Add"
    And I should see "User One" in the "#removeselect" "css_element"

  @javascript
  Scenario: Unenrol at system level using custom field search
    Given the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | System       |           |
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Users > Permissions > Assign system roles" in site administration
    And I follow "Manager"
    And I set the field "removeselect_searchtext" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label*='Existing users matching'][label*=' (1)']" "css_element" should exist
    And I set the field "removeselect" to "User One (one@example.com, Kermit)"
    And I press "Remove"
    And I should not see "User One" in the "#removeselect" "css_element"

  @javascript
  Scenario: Enrol at activity level using custom field search
    When I am on the "page1" "Activity" page logged in as "admin"
    And I navigate to "Permissions" in current page administration
    And I set the field "Participants tertiary navigation" to "Locally assigned roles"
    And I follow "Teacher"
    And I set the field "addselect_searchtext" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label*='Potential users matching'][label*=' (1)']" "css_element" should exist
    And I set the field "addselect" to "User One (one@example.com, Kermit)"
    And I press "Add"
    And I should see "User One" in the "#removeselect" "css_element"

  @javascript
  Scenario: Unenrol at activity level using custom field search
    Given the following "role assigns" exist:
      | user  | role           | contextlevel    | reference |
      | user1 | editingteacher | Activity module | page1     |
    When I am on the "page1" "Activity" page logged in as "admin"
    And I navigate to "Permissions" in current page administration
    And I set the field "Participants tertiary navigation" to "Locally assigned roles"
    And I follow "Teacher"
    And I set the field "removeselect_searchtext" to "Kermit"
    # The Behat 'I should see' step doesn't work for optgroup labels.
    Then "optgroup[label*='Users in this Activity module matching'][label*=' (1)']" "css_element" should exist
    And I set the field "removeselect" to "User One (one@example.com, Kermit)"
    And I press "Remove"
    And I should not see "User One" in the "#removeselect" "css_element"
