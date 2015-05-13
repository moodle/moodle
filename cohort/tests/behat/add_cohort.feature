@core @core_cohort
Feature: Add cohorts of users
  In order to create site-wide groups
  As an admin
  I need to create cohorts and add users on them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | First | User | first@example.com |
      | user2 | Second | User | second@example.com |
      | user3 | Third | User | third@example.com |
      | user4 | Forth | User | forth@example.com |
    And I log in as "admin"
    And I navigate to "Cohorts" node in "Site administration > Users > Accounts"
    And I follow "Add new cohort"
    And I set the following fields to these values:
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
    When I add "First User (first@example.com)" user to "333" cohort members
    And I add "Second User (second@example.com)" user to "333" cohort members
    Then I should see "2" in the "#cohorts" "css_element"
    And I follow "Assign"
    And the "Current users" select box should contain "First User (first@example.com)"
    And the "Current users" select box should contain "Second User (second@example.com)"
    And the "Current users" select box should not contain "Forth User (forth@example.com)"

  @javascript
  Scenario: Add users to a cohort using a bulk user action
    When I follow "Bulk user actions"
    And I set the field "Available" to "Third User"
    And I press "Add to selection"
    And I set the field "Available" to "Forth User"
    And I press "Add to selection"
    And I set the field "id_action" to "Add to cohort"
    And I press "Go"
    And I set the field "Cohort" to "Test cohort name [333]"
    And I press "Add to cohort"
    And I follow "Cohorts"
    Then I should see "2" in the "#cohorts" "css_element"
    And I follow "Assign"
    And the "Current users" select box should contain "Third User (third@example.com)"
    And the "Current users" select box should contain "Forth User (forth@example.com)"
    And the "Current users" select box should not contain "First User (first@example.com)"
