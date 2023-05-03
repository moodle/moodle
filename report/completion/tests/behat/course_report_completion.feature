@report @report_completion
Feature: In a course administration page, navigate through report page, test for course completion page
  In order to navigate through report page
  As an admin
  Go to course administration -> reports -> course completion

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
  Scenario: Selector should be available in the course completion page
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_criteria_self | 1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    Then "Report" "field" should exist in the "tertiary-navigation" "region"
    And I should see "Course completion" in the "tertiary-navigation" "region"
