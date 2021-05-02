@report @report_outline
Feature: View the user page for the outline report
  In order to ensure the user page for the outline report works as expected
  As a student
  I need to log in as a student and view the user page for the outline report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | showreports |
      | Course 1 | C1 | topics | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name        | description              | course | idnumber |
      | folder   | Folder name | Folder description       | C1     | folder1  |
    And the following "activities" exist:
      | activity | name        | description     | course | idnumber | externalurl            |
      | url      | URL name    | URL description | C1     | folder1  |  http://www.google.com |
    When I log in as "admin"

  Scenario: View the user page when only the legacy log reader is enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "URL name"
    And I follow "URL name"
    And I follow "URL name"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    When I follow "Outline report"
    Then I should see "4 views" in the "Folder name" "table_row"
    And I should see "3 views" in the "URL name" "table_row"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I follow "Complete report"
    And I should see "4 views"
    And I should see "3 views"

  Scenario: View the user page when only the standard log reader is enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "URL name"
    And I follow "URL name"
    And I follow "URL name"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    When I follow "Outline report"
    Then I should see "4 views" in the "Folder name" "table_row"
    And I should see "3 views" in the "URL name" "table_row"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    When I follow "Complete report"
    And I should see "4 views"
    And I should see "3 views"

  Scenario: View the user page when both the standard and legacy log readers are enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I follow "Folder name"
    And I am on "Course 1" course homepage
    # We want to view this multiple times, to make sure the count is working.
    And I follow "URL name"
    And I follow "URL name"
    And I follow "URL name"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    When I follow "Outline report"
    Then I should see "4 views" in the "Folder name" "table_row"
    And I should see "3 views" in the "URL name" "table_row"
    And I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    When I follow "Complete report"
    And I should see "4 views"
    And I should see "3 views"

  Scenario: View the user complete report page when there is a no-grade forum
    Given the following "activities" exist:
      | activity | name   | description     | course | idnumber |
      | forum    | forum1 | C1 first forum  | C1     | forum1   |
    And I am on "Course 1" course homepage
    When I follow "Participants"
    And I follow "Student 1"
    And I follow "Outline report"
    Then I should see "Outline report"
    When I follow "Participants"
    And I follow "Student 1"
    And I follow "Complete report"
    Then I should see "Complete report"
