@tool @tool_task
Feature: See warning message if cron is disabled
  In order to manage scheduled tasks
  As a Moodle Administrator
  I need to be able to view a warning message if cron is disabled

  Background:
    Given I log in as "admin"

  Scenario: If cron is disabled, I should see the message
    When the following config values are set as admin:
      | cron_enabled | 0 |
    And I navigate to "Server > Tasks > Scheduled tasks" in site administration
    Then I should see "Cron is disabled"

  Scenario: If cron is enabled, I should not see the message
    When the following config values are set as admin:
      | cron_enabled | 1 |
    And I navigate to "Server > Tasks > Scheduled tasks" in site administration
    Then I should not see "Cron is disabled"
