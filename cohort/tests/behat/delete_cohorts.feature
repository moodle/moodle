@core @core_cohort
Feature: Delete cohorts
  In order to delete cohorts
  As an admin
  I need to select specific cohorts and perform that action

  Background:
    Given the following "cohorts" exist:
      | name     | idnumber | contextlevel | reference | description    |
      | Cohort 1 | cohort1  | System       |           | About cohort 1 |
      | Cohort 2 | cohort2  | System       |           | About cohort 2 |
      | Cohort 3 | cohort3  | System       |           | About cohort 3 |

  @javascript
  Scenario: Delete single cohort
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And the following should exist in the "Cohorts" table:
      | Name     | Cohort ID |
      | Cohort 1 | cohort1   |
      | Cohort 2 | cohort2   |
      | Cohort 3 | cohort3   |
    When I press "Delete" action in the "Cohort 1" report row
    Then I should see "This will delete cohort 'Cohort 1' and all associated data." in the "Delete selected" "dialogue"
    And I click on "Delete" "button" in the "Delete selected" "dialogue"
    And I should see "Deleted cohort"
    And I should not see "Cohort 1" in the "Cohorts" "table"
    And I should see "Cohort 2" in the "Cohorts" "table"
    And I should see "Cohort 3" in the "Cohorts" "table"

  @javascript
  Scenario: Delete multiple cohorts
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And the following should exist in the "Cohorts" table:
      | Name     | Cohort ID |
      | Cohort 1 | cohort1   |
      | Cohort 2 | cohort2   |
      | Cohort 3 | cohort3   |
    When I click on "Select 'Cohort 1'" "checkbox"
    And I click on "Select 'Cohort 2'" "checkbox"
    And I click on "Delete selected" "button"
    Then I should see "This will delete the cohorts and all associated data." in the "Delete selected" "dialogue"
    And I click on "Delete" "button" in the "Delete selected" "dialogue"
    And I should see "Deleted selected cohorts"
    And I should not see "Cohort 1" in the "Cohorts" "table"
    And I should not see "Cohort 2" in the "Cohorts" "table"
    And I should see "Cohort 3" in the "Cohorts" "table"
