@mod @mod_data
Feature: Control database activity entry based on read-only dates
  In order to restrict or allow student entries based on specific dates
  As a teacher
  I need to be able to set read-only dates for the database activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |

  Scenario Outline: Student can add entries only when the current date falls outside the read-only date range
    Given the following "activities" exist:
      | activity | course | name            | idnumber | timeviewfrom | timeviewto |
      | data     | C1     | Data Activity 1 | DB1      | <viewfrom>   | <viewto>   |
    And the following "mod_data > fields" exist:
      | database | type | name       |
      | DB1      | text | DB Field 1 |
    When I am on the "Data Activity 1" "data activity" page logged in as student1
    # The "Add entry" button is visible only when the current date falls outside the read-only date range.
    Then "Add entry" "button" <btnvisibility> exist

    Examples:
      | viewfrom             | viewto             | btnvisibility |
      | ##yesterday##        | ##tomorrow##       | should not    |
      | ##tomorrow##         | ##tomorrow +1day## | should        |
      | ##1 week ago##       | ##yesterday##      | should        |
