@core @core_user
Feature: The visibility of table columns can be toggled
  In order to customise my view of participants data
  As a user
  I need to be able to hide and show columns in the participants table

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | t1       | Agatha    | T        | agatha@example.com  |
      | s1       | Matilda   | W        | matilda@example.com |
      | s2       | Mick      | H        | mick@example.com    |
    And the following "course enrolments" exist:
      | user | course | role           |
      | t1   | C1     | editingteacher |
      | s1   | C1     | student        |
      | s2   | C1     | student        |

  @javascript
  Scenario: The visibility of columns can be individually toggled within the participants table
    Given I log in as "t1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I should see "Email address" in the "participants" "table"
    And I should see "matilda@example.com" in the "participants" "table"
    And I should see "Roles" in the "participants" "table"
    And I should see "Student" in the "participants" "table"
    When I follow "Hide Email address"
    Then I should not see "Email address" in the "participants" "table"
    And I should not see "matilda@example.com" in the "participants" "table"
    And I should see "Roles" in the "participants" "table"
    And I should see "Student" in the "participants" "table"
    And I follow "Hide Roles"
    And I should not see "Roles" in the "participants" "table"
    And I should not see "Student" in the "participants" "table"
    And I should not see "matilda@example.com" in the "participants" "table"
    And I follow "Show Email address"
    And I should see "Email address" in the "participants" "table"
    And I should see "matilda@example.com" in the "participants" "table"
    And I should not see "Roles" in the "participants" "table"
    And I should not see "Student" in the "participants" "table"
    And I follow "Show Roles"
    And I should see "Roles" in the "participants" "table"
    And I should see "Student" in the "participants" "table"
    And I should see "Email address" in the "participants" "table"
    And I should see "matilda@example.com" in the "participants" "table"
