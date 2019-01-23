@tool @tool_task @javascript
Feature: Manage scheduled tasks
  In order to configure scheduled tasks
  As an admin
  I need to be able to disable, enable, edit and reset to default scheduled tasks

  Background:
    Given I log in as "admin"
    And I navigate to "Server > Tasks > Scheduled tasks" in site administration

  Scenario: Disable scheduled task
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I set the following fields to these values:
      | disabled             | 1 |
    And I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Task disabled" in the "Log table cleanup" "table_row"

  Scenario: Enable scheduled task
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I set the following fields to these values:
      | disabled             | 0 |
    And I press "Save changes"
    Then I should see "Changes saved"
    And I should not see "Task disabled" in the "Log table cleanup" "table_row"

  Scenario: Edit scheduled task
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I set the following fields to these values:
      | minute               | */5 |
      | hour                 | 1   |
      | day                  | 2   |
      | month                | 3   |
      | dayofweek            | 4   |
    And I press "Save changes"
    Then I should see "Changes saved"
    And the following should exist in the "admintable" table:
      | Component    | Minute | Hour | Day | Day of week | Month |
      | Standard log | */5    | 1    | 2   | 4           | 3     |

  Scenario: Reset scheduled task to default
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I set the following fields to these values:
      | resettodefaults      | 1   |
    And I press "Save changes"
    Then I should see "Changes saved"
    And the following should not exist in the "admintable" table:
      | Name               | Component    | Minute | Hour | Day | Day of week | Month |
      | Log table cleanup  | Standard log | */5    | 1    | 2   | 4           | 3     |