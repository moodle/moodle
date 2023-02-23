@tool @tool_monitor
Feature: In a course administration page, navigate through report page, test for course event monitor page
  In order to navigate through report page
  As an admin
  Go to course administration -> reports -> Event monitoring rules

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | enablecompletion |
      | Course 1 | C1 | 0 | 1 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Selector should be available in the course event monitoring page
    Given I log in as "admin"
    And I navigate to "Reports > Event monitoring rules" in site administration
    And I click on "Enable" "link"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    Then "Report" "field" should exist
    And the "Report" select box should contain "Event monitoring rules"
    And the field "Report" matches value "Event monitoring rules"
