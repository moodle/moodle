@tool @tool_task
Feature: See running scheduled tasks
  In order to configure scheduled tasks
  As an admin
  I need to see if tasks are running

  Background:
    Given I log in as "admin"

  Scenario: If no task is running, I should see the corresponding message
    Given I navigate to "Server > Tasks > Tasks running now" in site administration
    Then I should see "Nothing to display"

  Scenario: If tasks are running, I should see task details
    Given the following "tool_task > scheduled tasks" exist:
      | classname                            | seconds | hostname     | pid  |
      | \core\task\automated_backup_task     | 121     | c69335460f7f | 1914 |
    And the following "tool_task > adhoc tasks" exist:
      | classname                            | seconds | hostname     | pid  |
      | \core\task\asynchronous_backup_task  | 7201    | c69335460f7f | 1915 |
      | \core\task\asynchronous_restore_task | 172800  | c69335460f7f | 1916 |
    And I navigate to "Server > Tasks > Tasks running now" in site administration

    # Check the scheduled task details.
    Then I should see "Scheduled" in the "\core\task\automated_backup_task" "table_row"
    And I should see "2 mins" in the "Automated backups" "table_row"
    And I should see "c69335460f7f" in the "Automated backups" "table_row"
    And I should see "1914" in the "Automated backups" "table_row"

    # Check the "asynchronous_backup_task" adhoc task details.
    And I should see "Ad-hoc" in the "\core\task\asynchronous_backup_task" "table_row"
    And I should see "2 hours" in the "core\task\asynchronous_backup_task" "table_row"
    And I should see "c69335460f7f" in the "core\task\asynchronous_backup_task" "table_row"
    And I should see "1915" in the "core\task\asynchronous_backup_task" "table_row"

    # Check the "asynchronous_restore_task" adhoc task details.
    And I should see "Ad-hoc" in the "\core\task\asynchronous_restore_task" "table_row"
    And I should see "2 days" in the "core\task\asynchronous_restore_task" "table_row"
    And I should see "c69335460f7f" in the "core\task\asynchronous_restore_task" "table_row"
    And I should see "1916" in the "core\task\asynchronous_restore_task" "table_row"
