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
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    Given I log in as "admin"
    And I follow "Test"
    And I turn editing mode on
    When I add a "label" to section "1" and I fill the form with:
      | Label text | Swanky label |
      | Visible | Hide |
    Then "Swanky label" activity should be hidden

  @javascript
  Scenario: Visible label activity should be shown as visible.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
    Given I log in as "admin"
    And I follow "Test"
    And I turn editing mode on
    When I add a "label" to section "1" and I fill the form with:
      | Label text | Swanky label |
      | Visible | Show |
    Then "Swanky label" activity should be visible
