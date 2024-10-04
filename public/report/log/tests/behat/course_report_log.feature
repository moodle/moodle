@report @report_log
Feature: In a course administration page, navigate through report page, test for report log page
  In order to navigate through report page
  As an admin
  Go to course administration -> reports

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
  Scenario: Report selector should be available in the report log page
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    Then "Report" "field" should exist in the "tertiary-navigation" "region"
    And I should see "Logs" in the "tertiary-navigation" "region"

  @javascript
  Scenario: Course filter should be available only in the Site administration and Home report log page
    Given I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    Then I should see "Acceptance test site" in the "region-main" "region"
    And I click on "Home" "link"
    And I navigate to "Reports > Logs" in current page administration
    And I should see "Acceptance test site" in the "region-main" "region"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I should not see "Acceptance test site" in the "region-main" "region"
    And I should not see "Course 1" in the "region-main" "region"
    And I click on "Get these logs" "button"
    And I should see "Course 1" in the "region-main" "region"
