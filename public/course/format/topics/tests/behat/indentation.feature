@format @format_topics
Feature: Custom sections format supports indentation
  In order to create courses with a clear structure
  As a course creator
  I need my courses to support indentation for better organization

  @javascript
  Scenario: Admins could disable indentation in custom sections format
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name            | intro                  | course | idnumber |
      | forum    | Test forum name | Test forum description | C1     | forum1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I open "Test forum name" actions menu
    And "Move right" "link" should be visible
    When the following config values are set as admin:
      | indentation | 0 | format_topics |
    And I am on "Course 1" course homepage with editing mode on
    And I open "Test forum name" actions menu
    Then "Move right" "link" should not exist
    And "Move left" "link" should not exist
