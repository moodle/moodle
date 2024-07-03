@core @core_cohort
Feature: Delete cohorts
  In order to delete multiple cohorts
  As an admin
  I need to select specific cohorts and perform that action

  Background:
    Given the following "cohorts" exist:
      | name     | idnumber | contextlevel | reference | description    |
      | Cohort 1 | cohort1  | System       |           | About cohort 1 |
      | Cohort 2 | cohort2  | System       |           | About cohort 2 |
      | Cohort 3 | cohort3  | System       |           | About cohort 3 |

  @javascript
  Scenario: I can delete multiple cohorts using the checkboxes
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I should see "Cohort 1"
    And I should see "Cohort 2"
    And I should see "Cohort 3"
    And I click on "Select 'Cohort 1'" "checkbox"
    And I click on "Select 'Cohort 2'" "checkbox"
    When I click on "Delete selected" "button"
    Then I should see "Do you really want to delete the selected cohorts?"
    And I click on "Delete" "button" in the "Delete selected" "dialogue"
    And I should not see "Cohort 1"
    And I should not see "Cohort 2"
    And I should see "Cohort 3"
