@report @report_log
Feature: In a activity page, navigate through the More / Logs menu, test for report log page
  In order to navigate through report page
  As an admin
  Go to the activity page, click on More / Logs menu, and check for the report log page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "activities" exist:
      | activity | name        | course | section |
      | page     | Test page 1 | C1     | 1       |
      | page     | Test page 2 | C1     | 1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test page 1"
    And I am on "Course 1" course homepage
    And I follow "Test page 2"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test page 1"
    And I log out

  Scenario: Report selectors should be targeted toward course module
    Given I am on the "Test page 1" Activity page logged in as "admin"
    And I navigate to "Logs" in current page administration
    And "menuid" "select" should not exist
    And "modid" "select" should not exist
    And I should see "All participants" in the "user" "select"
    And I should see "All days" in the "date" "select"
    And I should see "All sources" in the "origin" "select"
    And I should see "All events" in the "edulevel" "select"
    And I should see "Test page 1" in the "#page-header" "css_element"
    And I should see "Student 1" in the "user" "select"
    When I set the field "user" to "Student 1"
    And I click on "Get these logs" "button"
    Then I should see "Test page 1" in the "#page-header" "css_element"
    And I should not see "Student 2" in the "table.reportlog" "css_element"
    And I should see "Page: Test page 1" in the "table.reportlog" "css_element"

  Scenario: Report submission stays in the same course module page
    Given I am on the "Test page 1" Activity page logged in as "admin"
    When I navigate to "Logs" in current page administration
    And I click on "Get these logs" "button"
    Then I should see "Test page 1" in the "#page-header" "css_element"
