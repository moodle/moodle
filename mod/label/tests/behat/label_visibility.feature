@mod @mod_label

Feature: Check label visibility works
  In order to check label visibility works
  As a teacher
  I should create label activity

  @javascript
  Scenario: Hidden label activity should be show as hidden.
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
    Given I log in as "teacher"
    And I follow "Test"
    And I turn editing mode on
    When I add a "label" to section "1" and I fill the form with:
      | Label text | Swanky label |
      | Visible | Hide |
    Then "Swanky label" activity should be hidden
    And I turn editing mode off
    And "Swanky label" activity should be hidden
    And I log out
    And I log in as "student"
    And I follow "Test"
    And I should not see "Swanky label"
    And I log out

  @javascript
  Scenario: Visible label activity should be shown as visible.
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
    Given I log in as "teacher"
    And I follow "Test"
    And I turn editing mode on
    When I add a "label" to section "1" and I fill the form with:
      | Label text | Swanky label |
      | Visible | Show |
    Then "Swanky label" activity should be visible
    And I log out
    And I log in as "student"
    And I follow "Test"
    And "Swanky label" activity should be visible
    And I log out

  @javascript
  Scenario: Teacher can not show label inside the hidden section
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
    Given I log in as "teacher"
    And I follow "Test"
    And I turn editing mode on
    When I add a "label" to section "1" and I fill the form with:
      | Label text | Swanky label |
      | Visible | Show |
    And I hide section "1"
    Then "Swanky label" activity should be dimmed
    And I open "Swanky label" actions menu
    And "Swanky label" actions menu should not have "Show" item
    And "Swanky label" actions menu should not have "Hide" item
    And "Swanky label" actions menu should not have "Make available" item
    And "Swanky label" actions menu should not have "Make unavailable" item
    And I click on "Edit settings" "link" in the "Swanky label" activity
    And I expand all fieldsets
    And the "Visible" select box should contain "Hidden from students"
    And the "Visible" select box should not contain "Available but not displayed on course page"
    And the "Visible" select box should not contain "Hide"
    And the "Visible" select box should not contain "Show"
    And I log out
    And I log in as "student"
    And I follow "Test"
    And I should not see "Swanky label"
    And I log out
