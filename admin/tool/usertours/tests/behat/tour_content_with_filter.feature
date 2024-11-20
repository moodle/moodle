@tool @tool_usertours
Feature: Apply Moodle filter to a tour
  In order to give more content to a tour
  As an administrator
  I need to create a user tour with Moodle filters applied

  Background:
    Given I log in as "admin"
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name              | content                      | course |
      | page     | Page for Usertour | Content of page for Usertour | C1     |
    And I add a new user tour with:
      | Name               | Activity names auto-linking tour             |
      | Description        | Tour with activity names auto-linking filter |
      | Apply to URL match | /course/view.php%                            |
      | Tour is enabled    | 1                                            |
    And I add steps to the "Activity names auto-linking tour" tour:
      | targettype                | Title                       | id_content                                                | Content type |
      | Display in middle of page | Activity names auto-linking | Test Activity names auto-linking Filter Page for Usertour | Manual       |

  @javascript
  Scenario: Add a new tour with Activity names auto-linking filter off
    Given the "activitynames" filter is "off"
    When I am on "Course 1" course homepage
    Then I should see "Test Activity names auto-linking Filter" in the "Activity names auto-linking" "dialogue"
    And I should see "Page for Usertour" in the "Activity names auto-linking" "dialogue"
    And "Page for Usertour" "link" should not exist in the "Activity names auto-linking" "dialogue"

  @javascript
  Scenario: Add a new tour with Activity names auto-linking filter on
    Given the "activitynames" filter is "on"
    When I am on "Course 1" course homepage
    Then I should see "Test Activity names auto-linking Filter" in the "Activity names auto-linking" "dialogue"
    And I should see "Page for Usertour" in the "Activity names auto-linking" "dialogue"
    And "Page for Usertour" "link" should exist in the "Activity names auto-linking" "dialogue"
    And I click on "Page for Usertour" "link" in the "Activity names auto-linking" "dialogue"
    And I should see "Content of page for Usertour"
