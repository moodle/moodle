@core @core_cohort
Feature: Add cohorts of users
  In order to create site-wide groups
  As an admin
  I need to create cohorts and add users on them

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | user1 | First | User | first@user.com |
      | user2 | Second | User | second@user.com |
      | user3 | Third | User | third@user.com |
      | user4 | Forth | User | forth@user.com |
    And I log in as "admin"
    And I collapse "Front page settings" node
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    And I follow "Cohorts"
    And I press "Add"
    And I fill the moodle form with:
      | Name | Test cohort name |
      | Context | System |
      | Cohort ID | 333 |
      | Description | Test cohort description |
    And I press "Save changes"

  @javascript
  Scenario: Add a cohort
    When I follow "Cohorts"
    Then I should see "Test cohort name"
    And I should see "333"
    And I should see "Test cohort description"
    And I should see "Created manually"

  @javascript
  Scenario: Add users to a cohort selecting them from the system users list
    When I add "user1" user to "333" cohort
    And I add "user2" user to "333" cohort
    Then I should see "2" in the "#cohorts" "css_element"
    And I follow "Assign"
    And the "Current users" select box should contain "First User (first@user.com)"
    And the "Current users" select box should contain "Second User (second@user.com)"
    And the "Current users" select box should not contain "Forth User (forth@user.com)"

  @javascript
  Scenario: Add users to a cohort using a bulk user action
    When I follow "Bulk user actions"
    And I select "Third User" from "Available"
    And I press "Add to selection"
    And I select "Forth User" from "Available"
    And I press "Add to selection"
    And I select "Add to cohort" from "id_action"
    And I press "Go"
    And I select "Test cohort name [333]" from "Cohort"
    And I press "Add to cohort"
    And I follow "Cohorts"
    Then I should see "2" in the "#cohorts" "css_element"
    And I follow "Assign"
    And the "Current users" select box should contain "Third User (third@user.com)"
    And the "Current users" select box should contain "Forth User (forth@user.com)"
    And the "Current users" select box should not contain "First User (first@user.com)"
