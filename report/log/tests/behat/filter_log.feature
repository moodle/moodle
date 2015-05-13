@report @report_log
Feature: In a report, admin can filter log data
  In order to filter log data
  As an admin
  I need to log in with different user and go to log and apply filter

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
    And I log in as "admin"

  @javascript
  Scenario: Filter log report for standard and legacy log reader
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I follow "Home"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 1"
    And I click on "Log in as" "link"
    And I press "Continue"
    And I log out
    And I log in as "admin"
    When I navigate to "Logs" node in "Site administration > Reports"
    And I set the field "id" to "Acceptance test site (Site)"
    And I set the field "user" to "All participants"
    And I set the field "logreader" to "Standard log"
    And I press "Get these logs"
    Then I should see "User logged in as another user"
    And I set the field "logreader" to "Legacy log"
    And I press "Get these logs"
    And I should see "user login"
    And I should not see "Nothing to display"

  @javascript
  Scenario: Filter log report for standard log reader
    Given I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 1"
    And I click on "Log in as" "link"
    And I press "Continue"
    And I log out
    And I log in as "admin"
    When I navigate to "Logs" node in "Site administration > Reports"
    And I set the field "id" to "Acceptance test site (Site)"
    And I set the field "user" to "All participants"
    And I press "Get these logs"
    Then I should see "User logged in as another user"

  @javascript
  Scenario: Filter log report for legacy log reader
    Given I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I follow "Home"
    And I follow "Course 1"
    And I follow "Home"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Enrolled users"
    And I follow "Student 1"
    And I click on "Log in as" "link"
    And I press "Continue"
    And I log out
    And I log in as "admin"
    When I navigate to "Logs" node in "Site administration > Reports"
    And I set the field "id" to "Acceptance test site (Site)"
    And I set the field "user" to "All participants"
    And I press "Get these logs"
    Then I should see "user login"
