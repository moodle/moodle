@tool @tool_task
Feature: Run scheduled tasks from the administration UI
  In order to run scheduled tasks outside of their normal schedule
  As an admin
  I need to be able to run a task immediately or schedule it to run at the next cron execution

  Background:
    Given the following config values are set as admin:
      | enablerunnow | 1 | tool_task |
    And the PHP CLI path is set to the system PHP binary
    And I log in as "admin"
    And I navigate to "Server > Tasks > Scheduled tasks" in site administration

  Scenario: Run now button appears for an enabled task
    Given the scheduled task "\logstore_standard\task\cleanup_task" is enabled
    When I reload the page
    Then I should see "Run now" in the "Log table cleanup" "table_row"

  Scenario: Run now button does not appear for a task whose plugin is disabled
    Given I disable "db" "auth" plugin
    When I reload the page
    Then I should not see "Run now" in the "Synchronise users task" "table_row"

  @javascript
  Scenario: Confirming Run now executes the task and shows output
    When I click on "Run now" "link" in the "Log table cleanup" "table_row"
    Then I should see "Are you sure you want to run this task" in the ".modal-dialog" "css_element"
    And I should see "The task will run on the web server" in the ".modal-dialog" "css_element"
    And I click on "Run now" "button" in the ".modal-dialog" "css_element"
    And I should see "Log table cleanup"
    And I should see "Run again"

  @javascript
  Scenario: Cancelling Run now returns to the scheduled tasks list without running
    When I click on "Run now" "link" in the "Log table cleanup" "table_row"
    Then I should see "Are you sure you want to run this task" in the ".modal-dialog" "css_element"
    And I click on "Cancel" "button" in the ".modal-dialog" "css_element"
    And I should see "Scheduled tasks"
    And I should not see "Run again"

  Scenario: Run ASAP button appears for an enabled task scheduled for the future
    Given the scheduled task "\core\task\send_new_user_passwords_task" has a next run time in the future
    When I reload the page
    Then I should see "Run ASAP" in the "Send new user passwords" "table_row"

  Scenario: Run ASAP button does not appear when the task is already due
    Given the scheduled task "\core\task\send_new_user_passwords_task" has a next run time in the past
    When I reload the page
    Then I should not see "Run ASAP" in the "Send new user passwords" "table_row"

  Scenario: Run ASAP button does not appear for a disabled task
    Given the scheduled task "\core\task\send_new_user_passwords_task" is disabled
    When I reload the page
    Then I should not see "Run ASAP" in the "Send new user passwords" "table_row"

  @javascript
  Scenario: Confirming Run ASAP schedules the task and shows a success message
    Given the scheduled task "\core\task\send_new_user_passwords_task" has a next run time in the future
    And I reload the page
    When I click on "Run ASAP" "link" in the "Send new user passwords" "table_row"
    Then I should see "Are you sure you want to run this task" in the ".modal-dialog" "css_element"
    And I should see "The task will run via cron at the next available time." in the ".modal-dialog" "css_element"
    And I click on "Run ASAP" "button" in the ".modal-dialog" "css_element"
    And I should see "has been scheduled to run ASAP"

  @javascript
  Scenario: Cancelling Run ASAP returns to the scheduled tasks list without changes
    Given the scheduled task "\core\task\send_new_user_passwords_task" has a next run time in the future
    And I reload the page
    When I click on "Run ASAP" "link" in the "Send new user passwords" "table_row"
    Then I should see "Are you sure you want to run this task" in the ".modal-dialog" "css_element"
    And I click on "Cancel" "button" in the ".modal-dialog" "css_element"
    And I should see "Scheduled tasks"
    And I should not see "has been scheduled to run ASAP"
