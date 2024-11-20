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
    And I should see "Ad hoc" in the "\core\task\asynchronous_backup_task" "table_row"
    And I should see "2 hours" in the "core\task\asynchronous_backup_task" "table_row"
    And I should see "c69335460f7f" in the "core\task\asynchronous_backup_task" "table_row"
    And I should see "1915" in the "core\task\asynchronous_backup_task" "table_row"

    # Check the "asynchronous_restore_task" adhoc task details.
    And I should see "Ad hoc" in the "\core\task\asynchronous_restore_task" "table_row"
    And I should see "2 days" in the "core\task\asynchronous_restore_task" "table_row"
    And I should see "c69335460f7f" in the "core\task\asynchronous_restore_task" "table_row"
    And I should see "1916" in the "core\task\asynchronous_restore_task" "table_row"

  @javascript
  Scenario: If a task with a stored progress bar is running, I should be able to observe the progress.
    Given the following config values are set as admin:
      | progresspollinterval | 1 |
    And the following "tool_task > scheduled tasks" exist:
      | classname                                | seconds | hostname     | pid  |
      | \core\task\delete_unconfirmed_users_task | 120     | c69335460f7f | 1917 |
    And the following "stored progress bars" exist:
      | idnumber                                | percent |
      | core_task_delete_unconfirmed_users_task | 50.00   |
    And I navigate to "Server > Tasks > Tasks running now" in site administration
    And I should see "2 mins" in the "Delete unconfirmed users" "table_row"
    And I should see "c69335460f7f" in the "Delete unconfirmed users" "table_row"
    And I should see "1917" in the "Delete unconfirmed users" "table_row"
    And I should see "50.0%" in the "Delete unconfirmed users" "table_row"
    When I set the stored progress bar "core_task_delete_unconfirmed_users_task" to "75.00"
    # Wait for the progress polling.
    And I wait "1" seconds
    Then I should not see "50.0%" in the "Delete unconfirmed users" "table_row"
    And I should see "75.0%" in the "Delete unconfirmed users" "table_row"
