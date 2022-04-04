@report @report_participation
Feature: In a participation report, admin can filter student actions
  In order to filter participation data
  As a student
  I need to log action and then log in as admin to view participation report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | course      | C1             |
      | activity    | book           |
      | name        | Test book name |
      | description | Test book      |
      | idnumber    | book1          |
    And I am on the "Test book name" "book activity" page logged in as teacher1
    And I set the following fields to these values:
      | Chapter title | Test chapter |
      | Content | Test chapter content |
    And I log out

  @javascript
  Scenario: Filter participation report when only legacy log reader is enabled
    Given I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out

    And I am on the "Test book name" "book activity" page logged in as student1
    And I log out

    When I am on the "Course 1" course page logged in as admin
    When I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    Then I should see "Yes (1)"

  @javascript
  Scenario: Filter participation report when standard log reader is enabled later
    Given I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I log out

    And I am on the "Test book name" "book activity" page logged in as student1
    And I log out

    And I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Standard log" "table_row"
    And I log out

    And I am on the "Test book name" "book activity" page logged in as student1
    And I log out

    And I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    Then I should see "Yes (2)"

  @javascript
  Scenario: Filter participation report when only standard log reader is enabled by default
    Given I am on the "Test book name" "book activity" page logged in as student1
    And I log out

    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the field "instanceid" to "Test book name"
    And I set the field "roleid" to "Student"
    And I press "Go"
    Then I should see "Yes (1)"
