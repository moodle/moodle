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
    And I set the following fields in the "Class name" "core_reportbuilder > Filter" to these values:
      | Class name value    | <name>   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "Filters (1)" in the "#dropdownFiltersButton" "css_element"
    And the following should exist in the "reportbuilder-table" table:
      | Type      | Name    |
      | Scheduled | <match> |
    And the following should not exist in the "reportbuilder-table" table:
      | Type      | Name       |
      | Scheduled | <nonmatch> |
    Examples:
      | name                         | match                        | nonmatch                     |
      | Cleanup event monitor events | Cleanup event monitor events | Incoming email pickup        |
      | Incoming email pickup        | Incoming email pickup        | Cleanup event monitor events |

  @javascript
  # Task duration is dependent on many factors, we are asserting here that no task has a duration >2 minutes.
  Scenario Outline: Filter task logs by duration
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Server > Tasks > Task logs" in site administration
    When I click on "Filters" "button"
    And I set the following fields in the "Duration" "core_reportbuilder > Filter" to these values:
      | Duration operator | <operator> |
      | Duration value    | 2          |
      | Duration unit     | minute(s)  |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I <shouldornotsee> "Nothing to display"
    Examples:
      | operator     | shouldornotsee |
      | Less than    | should not see |
      | Greater than | should see     |
