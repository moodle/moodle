@format @format_topics
Feature: Custom sections format supports course linear navigation
  In order to navigate through the course activities in a linear way
  As a course creator
  I need my courses to support course linear navigation when I choose to enable it in the course settings

  @javascript
  Scenario Outline: Global defaults set linear navigation for custom sections format
    Given the following config values are set as admin:
      | enablelinearnav | <value> | format_topics |
    When I log in as "admin"
    And I create a course with:
      | Course full name  | Course 1 |
      | Course short name | C1       |
      | Format            | topics   |
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "Enable linear navigation" matches value "<expected>"

    Examples:
      | value | expected |
      | 1     | Yes      |
      | 0     | No       |
