@core @core_group
Feature: Uniqueness of Group ID number
  In order to create unique groups and groupings
  As a teacher
  I need to create groups with unique identificators

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration

  Scenario: Group ID number uniqueness
    Given I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
      | Group ID number | G1 |
    And I press "Save changes"
    When I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 2 |
      | Group ID number | G1 |
    And I press "Save changes"
    Then I should see "This ID number is already taken"
    And I set the following fields to these values:
      | Group ID number | G2 |
    And I press "Save changes"
    And I set the field "groups" to "Group 1 (0)"
    And I press "Edit group settings"
    And I set the following fields to these values:
      | Group ID number | G2 |
    And I press "Save changes"
    And I should see "This ID number is already taken"
    And I press "Cancel"

  Scenario: Grouping ID number uniqueness
    Given I follow "Groupings"
    And I press "Create grouping"
    And I set the following fields to these values:
      | Grouping name | Grouping 1 |
      | Grouping ID number | GG1 |
    And I press "Save changes"
    When I press "Create grouping"
    And I set the following fields to these values:
      | Grouping name | Grouping 2 |
      | Grouping ID number | GG1 |
    And I press "Save changes"
    Then I should see "This ID number is already taken"
    And I set the following fields to these values:
      | Grouping ID number | GG2 |
    And I press "Save changes"
    And I click on "Edit" "link" in the "Grouping 1" "table_row"
    And I set the following fields to these values:
      | Grouping ID number | GG2 |
    And I press "Save changes"
    And I should see "This ID number is already taken"
    And I press "Cancel"
