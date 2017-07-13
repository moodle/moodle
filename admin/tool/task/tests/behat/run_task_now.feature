@tool @tool_task
Feature: Run tasks from web interface
  In order to run scheduled tasks immediately
  As an admin
  I need to be able to run a task from the web interface

  Scenario: Run a task
    Given I log in as "admin"
    When I navigate to "Scheduled tasks" node in "Site administration > Server"
    Then I should see "Never" in the "Log table cleanup" "table_row"

    And I click on "Run now" "text" in the "Log table cleanup" "table_row"
    And I should see "Are you sure you want to run this task"
    And I press "Run now"

    And I should see "Log table cleanup" in the "h2" "css_element"
    And I should see "Scheduled task complete: Log table cleanup"

    And I follow "Back to scheduled tasks"
    And I should not see "Never" in the "Log table cleanup" "table_row"

  Scenario: Cancel running a task
    Given I log in as "admin"
    When I navigate to "Scheduled tasks" node in "Site administration > Server"
    And I click on "Run now" "text" in the "Log table cleanup" "table_row"
    And I press "Cancel"
    # Confirm we're back on the scheduled tasks page by looking for the table.
    Then "Log table cleanup" "table_row" should exist

  Scenario: Cannot run a task when the option is disabled
    Given the following config values are set as admin:
      | enablerunnow | 0 | tool_task |
    When I log in as "admin"
    And I navigate to "Scheduled tasks" node in "Site administration > Server"
    Then I should not see "Run now"
