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
      | teacher1 | Teacher | 1 | teacher1@example.com
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Forum name |
      | Description | Forum description |
    And I add a "Book" to section "1" and I fill the form with:
      | Name | Book name |
      | Description | Book description |

  Scenario: View the outline report when only the legacy log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2 by 2 users" in the "Book name" "table_row"
    And I should see "1 by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when only the standard log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2 by 2 users" in the "Book name" "table_row"
    And I should see "1 by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when both the standard and legacy log readers are enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Book name"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "2 by 2 users" in the "Book name" "table_row"
    And I should see "1 by 1 users" in the "Forum name" "table_row"

  Scenario: View the outline report when no log reader is enabled
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And "Enable" "link" should exist in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I am on site homepage
    And I follow "Course 1"
    When I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "No log reader enabled"

  Scenario: Multiple views from a single user are identified as not distinct
    Given I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "3 by 1 users" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"

  Scenario: Multiple views from multiple users are identified as not distinct
    Given I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I follow "Course 1"
    And I follow "Forum name"
    And I am on site homepage
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "6 by 2 users" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"

  Scenario: No views from any users
    Given I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Activity report" node in "Course administration > Reports"
    Then I should see "-" in the "Forum name" "table_row"
    And I should see "-" in the "Book name" "table_row"
