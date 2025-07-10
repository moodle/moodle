@tool @tool_task @javascript
Feature: Manage adhoc task
  In order to manage adhoc tasks
  As an admin
  I need to be able to view adhoc tasks

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
