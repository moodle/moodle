@tool @tool_task @javascript
Feature: Delete an adhoc task
  In order to manage adhoc tasks
  As an admin
  I need to be able to delete adhoc tasks

  Scenario: Delete an existing adhoc task
    Given I log in as "admin"
    And the following "tool_task > adhoc tasks" exist:
      | classname                            | seconds | hostname     | pid  |
      | \core\task\asynchronous_backup_task  | 7201    | c69335460f7f | 1915 |
      | \core\task\asynchronous_restore_task | 172800  | c69335460f7f | 1916 |
    And I navigate to "Server > Tasks > Ad hoc tasks" in site administration
    Then "asynchronous_backup_task" "table_row" should exist
    And "asynchronous_restore_task" "table_row" should exist
    And I follow "asynchronous_backup_task"
    When I follow "Delete"
    And I click on "Delete" "button" in the ".modal-dialog" "css_element"
    Then I navigate to "Server > Tasks > Ad hoc tasks" in site administration
    And "asynchronous_backup_task" "table_row" should not exist
    And "asynchronous_restore_task" "table_row" should exist
