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
    And I should see "Log table cleanup" in the "tr.table-primary" "css_element"

  Scenario: Enable scheduled task
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I set the following fields to these values:
      | disabled             | 0 |
    And I press "Save changes"
    Then I should see "Changes saved"
    And I should not see "Task disabled" in the "Log table cleanup" "table_row"
    And I should see "Log table cleanup" in the "tr.table-primary" "css_element"

  Scenario: Edit scheduled task
    When I click on "Edit task schedule: Log table cleanup" "link" in the "Log table cleanup" "table_row"
    Then I should see "Edit task schedule: Log table cleanup"
    And I should see "\logstore_standard\task\cleanup_task"
    And I should see "From component: Standard log"
    And I should see "logstore_standard"
    And I should see "Default: R" in the "Minute" "fieldset"
    And I should see "Default: *" in the "Day" "fieldset"
    And I set the following fields to these values:
      | minute               | frog |
    And I press "Save changes"
    And I should see "Data submitted is invalid"
    And I set the following fields to these values:
      | minute               | */5 |
      | hour                 | 1   |
      | day                  | 2   |
      | month                | 3   |
      | dayofweek            | 4   |
    And I press "Save changes"
    And I should see "Changes saved"
    And the following should exist in the "admintable" table:
      | Component                      | Minute         | Hour         | Day          | Day of week  | Month        |
      | Standard log logstore_standard | */5 Default: R | 1 Default: 4 | 2 Default: * | 4 Default: * | 3 Default: * |
    And I should see "Log table cleanup" in the "tr.table-primary" "css_element"
    And I should see "*/5 Default: R" in the "td.table-warning" "css_element"

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
    And I should see "Log table cleanup" in the "tr.table-primary" "css_element"

  Scenario: Disabled plugin's tasks are labelled as disabled too
    When "CAS users sync job \auth_cas\task\sync_task" row "Next run" column of "Scheduled tasks" table should contain "Plugin disabled"
    Then "CAS users sync job \auth_cas\task\sync_task" row "Component" column of "Scheduled tasks" table should contain "Disabled"
    And "Background processing for scheduled allocation \workshopallocation_scheduled\task\cron_task" row "Next run" column of "Scheduled tasks" table should not contain "Plugin disabled"
    And "Background processing for scheduled allocation \workshopallocation_scheduled\task\cron_task" row "Component" column of "Scheduled tasks" table should not contain "Disabled"
