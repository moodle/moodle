@filter_data @mod_data
Feature: When using data filter the entry will be linked based on autolink.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the "data" filter is "on"
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |

  Scenario Outline: Text entries are linked automatically based if autolink is enabled or not.
    # Param1 refers to `Autolink`.
    Given the following "mod_data > fields" exist:
      | database | type | name            | description                | param1   |
      | data1    | text | Test field name | Test field description     | 0        |
      | data1    | text | Test field ref  | Test field ref description | <param1> |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name             | Test field ref |
      | data1    | teacher1 | Linked entry to Data2 entry | Data1 entry    |
      | data1    | teacher1 | Linked entry to Data1 entry | Data2 entry    |
    When I am on the "Test database name" "data activity" page logged in as teacher1
    Then "Data1 entry" "link" <autolink> exist
    Then "Data2 entry" "link" <autolink> exist

    Examples:
      | param1 | autolink   |
      | 0      | should not |
      | 1      | should     |
