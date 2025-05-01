@tool @tool_task @javascript
Feature: Manage adhoc task
  In order to manage adhoc tasks
  As an admin
  I need to be able to view and delete adhoc tasks

  Scenario Outline: View adhoc tasks next run time
    Given the following "tool_task > adhoc tasks" exist:
      | classname                            | seconds | hostname     | pid  | nextruntime   |
      | \core\task\asynchronous_backup_task  | 0       | c69335460f7f | 1915 | <nextruntime> |
    When I log in as "admin"
    And I navigate to "Server > Tasks > Ad hoc tasks" in site administration
    Then the following should exist in the "Ad hoc tasks" table:
      | Component / Class name   | Next run         |
      | asynchronous_backup_task | <nextruntimestr> |
    And I click on "asynchronous_backup_task" "link" in the "Ad hoc tasks" "table"
    And the following should exist in the "\core\task\asynchronous_backup_task Ad hoc tasks" table:
      | Next run         |
      | <nextruntimestr> |
    Examples:
      | nextruntime       | nextruntimestr                         |
      | ##yesterday##     | ASAP                                   |
      | ##tomorrow noon## | ##tomorrow noon##%A, %d %B %Y, %I:%M## |

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
