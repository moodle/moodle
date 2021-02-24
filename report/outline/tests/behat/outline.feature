@report @report_outline
Feature: View an outline report
  In order to ensure the outline report works as expected
  As a teacher
  I need to log in as a teacher and view the outline report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity   | name                      | intro                    | course | idnumber |
      | forum      | Forum name                | Forum description        | C1     | forum1   |
      | book       | Book name                 | Book description         | C1     | book1    |
    When I log in as "admin"
    And I am on "Course 1" course homepage

  Scenario: View the outline report when only the legacy log reader is enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then I should see "2 views by 2 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when only the standard log reader is enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then I should see "2 views by 2 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when both the standard and legacy log readers are enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then I should see "2 views by 2 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when no log reader is enabled
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I am on "Course 1" course homepage
    When I navigate to "Reports > Activity report" in current page administration
    Then I should see "No log reader enabled"

  Scenario: Multiple views from a single user are identified as not distinct
    Given I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity report" in current page administration
    Then I should see "3 views by 1 users" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"

  Scenario: Multiple views from multiple users are identified as not distinct
    Given I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on "Course 1" course homepage
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity report" in current page administration
    Then I should see "6 views by 2 users" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"

  Scenario: No views from any users
    Given I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity report" in current page administration
    Then I should see "-" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"
