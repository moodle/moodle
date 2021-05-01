@report @report_log
Feature: In a course administration page, navigate through report page, test for report log page
  In order to navigate through report page
  As an admin
  Go to course administration -> reports
  The reports page by default points to logs page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
      | Course 2 | C2 | 0 | 1 |
      | Course 3 | C3 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | student1 | C1 | student |
      | admin | C2 | editingteacher |
      | student1 | C2 | student |
      | admin | C3 | editingteacher |
      | student1 | C3 | student |

  @javascript
  Scenario: Default page accessed for Report is log page
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    Then "Report" "field" should exist
    And the "Report" select box should contain "Logs"
    And the field "Report" matches value "Logs"

  @javascript
  Scenario: Verify the session setting is saved for different courses
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And "Report" "field" should exist
    And the "Report" select box should contain "Logs"
    And the field "Report" matches value "Logs"
    # Now select the Live logs for Course 2
    And I am on "Course 2" course homepage
    And I navigate to "Reports > Live logs" in current page administration
    # now come back to course 1 and see if the default is logs page or not
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And the "Report" select box should contain "Logs"
    # Now come back again to Course 2
    And I am on "Course 2" course homepage
    When I navigate to "Reports" in current page administration
    Then the field "Report" matches value "Live logs"
