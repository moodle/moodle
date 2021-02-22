@report @report_outline
Feature: In a course administration page, navigate through report page, test for activity report page
  In order to navigate through report page
  As an admin
  Go to course administration -> Reports -> Activity report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Selector should be available in course activiy report page
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then "Report" "field" should exist
    And the "Report" select box should contain "Activity report"
    And the field "Report" matches value "Activity report"
