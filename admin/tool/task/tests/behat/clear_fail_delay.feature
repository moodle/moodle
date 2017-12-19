@tool @tool_task
Feature: Clear scheduled task fail delay
  In order to stop failures from delaying a scheduled task run
  As an admin
  I need to be able to clear the fail delay on a task

  Background:
    Given the scheduled task "\core\task\send_new_user_passwords_task" has a fail delay of "60" seconds
    And I log in as "admin"
    And I navigate to "Scheduled tasks" node in "Site administration > Server"

  Scenario: Clear fail delay
    When I click on "Clear" "text" in the "Send new user passwords" "table_row"
    And I should see "Are you sure you want to clear the fail delay"
    And I press "Clear"

    Then I should not see "60" in the "Send new user passwords" "table_row"
    And I should not see "Clear" in the "Send new user passwords" "table_row"

  Scenario: Cancel clearing the fail delay
    When I click on "Clear" "text" in the "Send new user passwords" "table_row"
    And I press "Cancel"

    Then I should see "60" in the "Send new user passwords" "table_row"
    And I should see "Clear" in the "Send new user passwords" "table_row"
