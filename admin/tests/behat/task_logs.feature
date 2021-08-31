@core @core_admin @core_reportbuilder
Feature: View task logs report and use its filters
  In order to view task logs report and use its filters
  As an admin
  I need to navigate to Server > Tasks > Task logs

  Background:
    # We need to run cron to populate the report.
    Given I trigger cron

  @javascript
  Scenario Outline: Filter task logs by name
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Server > Tasks > Task logs" in site administration
    When I click on "Filters" "button"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator | Contains |
      | Name value    | <name>   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And the following should exist in the "reportbuilder-table" table:
      | Type      | Name    |
      | Scheduled | <match> |
    And the following should not exist in the "reportbuilder-table" table:
      | Type      | Name       |
      | Scheduled | <nonmatch> |
    Examples:
      | name               | match                        | nonmatch                     |
      | task\\clean_events | Cleanup event monitor events | Incoming email pickup        |
      | task\\pickup_task  | Incoming email pickup        | Cleanup event monitor events |
