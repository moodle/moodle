@mod @mod_label

Feature: Check label visibility works
  In order to check label visibility works
  As a teacher
  I should create label activity

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | Frist | teacher1@example.com |
      | student | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
      | student | C1 | student |
    And the following "activities" exist:
      | activity | course | section | intro          | idnumber | visible |
      | label    | C1     | 1       | Swanky label   | 1        | 1       |
      | label    | C1     | 1       | Swanky label 2 | 2        | 0       |

  Scenario: Hidden label activity should be show as hidden.
    Given I log in as "teacher"
    When I am on "Test" course homepage with editing mode on
    Then "Swanky label 2" label should be hidden
    And I turn editing mode off
    And "Swanky label 2" label should be hidden
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should not see "Swanky label 2"
    And I log out

  Scenario: Visible label activity should be shown as visible.
    Given I log in as "teacher"
    When I am on "Test" course homepage with editing mode on
    Then "Swanky label" activity should be visible
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And "Swanky label" activity should be visible
    And I log out

  @javascript
  Scenario: Teacher can not show label inside the hidden section
    Given I log in as "teacher"
    And I am on "Test" course homepage with editing mode on
    When I hide section "1"
    Then "Swanky label" label should be hidden
    And I open "Swanky label" actions menu
    And "Swanky label" actions menu should not have "Availability" item
    And I click on "Edit settings" "link" in the "Swanky label" activity
    And I expand all fieldsets
    And the "Availability" select box should contain "Hide on course page"
    And the "Availability" select box should not contain "Make available but don't show on course page"
    And the "Availability" select box should not contain "Show on course page"
    And I log out
    And I log in as "student"
    And I am on "Test" course homepage
    And I should not see "Swanky label"
    And I log out
