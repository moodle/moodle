@core @core_group
Feature: Uniqueness of Group ID number
  In order to create unique groups and groupings
  As a teacher
  I need to create groups with unique identificators

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"

  @javascript
  Scenario: Group ID number uniqueness
    Given I press "Create group"
    And I fill the moodle form with:
      | Group name | Group 1 |
      | Group ID number | G1 |
    And I press "Save changes"
    When I press "Create group"
    And I fill the moodle form with:
      | Group name | Group 2 |
      | Group ID number | G1 |
    And I press "Save changes"
    Then I should see "This ID number is already taken"
    And I fill the moodle form with:
      | Group ID number | G2 |
    And I press "Save changes"
    And I select "Group 1 (0)" from "groups"
    And I press "Edit group settings"
    And I fill the moodle form with:
      | Group ID number | G2 |
    And I press "Save changes"
    And I should see "This ID number is already taken"
    And I press "Cancel"

  @javascript
  Scenario: Grouping ID number uniqueness
    Given I follow "Groupings"
    And I press "Create grouping"
    And I fill the moodle form with:
      | Grouping name | Grouping 1 |
      | Grouping ID number | GG1 |
    And I press "Save changes"
    When I press "Create grouping"
    And I fill the moodle form with:
      | Grouping name | Grouping 2 |
      | Grouping ID number | GG1 |
    And I press "Save changes"
    Then I should see "This ID number is already taken"
    And I fill the moodle form with:
      | Grouping ID number | GG2 |
    And I press "Save changes"
    And I click on "Edit" "link" in the "Grouping 1" table row
    And I fill the moodle form with:
      | Grouping ID number | GG2 |
    And I press "Save changes"
    And I should see "This ID number is already taken"
    And I press "Cancel"
