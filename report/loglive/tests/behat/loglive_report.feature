@report @report_loglive
Feature: In a report, admin can see loglive data
  In order see loglive data
  As an admin
  I need to view loglive report and see if the live update feature works

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Legacy log" "table_row"
    And the following config values are set as admin:
      | loglegacy | 1 | logstore_legacy |
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |

  @javascript
  Scenario: Check loglive report entries and make sure the report works for standard and legacy reader
    Given I navigate to "Reports > Live logs" in site administration
    When I set the field "reader" to "Standard log"
    Then I should see "Course module created"
    And I should see "Test name"
    And I set the field "reader" to "Legacy log"
    And I wait to be redirected
    And I should see "course_add mod"
    And I log out

  @javascript @_switch_window
  Scenario: Check loglive report entries and make sure the pause/resume button works for standard reader along with ajax calls
    Given I am on site homepage
    When I navigate to "Reports > Live logs" in site administration
    And I set the field "reader" to "Standard log"
    And I wait to be redirected
    And I should not see "Test name2"
    And I press "Pause live updates"
    And I follow "Course module created"
    And I switch to "action" window
    And I am on "Course 1" course homepage
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name2 |
      | Description | Test database description |
    And I switch to the main window
    And I wait "8" seconds
    Then I should not see "Test name2"
    And I press "Resume live updates"
    And I wait "8" seconds
    And I should see "Test name2"
    And I log out

  @javascript @_switch_window
  Scenario: Check loglive report entries and make sure the pause/resume button works for legacy reader along with ajax calls
    Given I am on site homepage
    When I navigate to "Reports > Live logs" in site administration
    And I set the field "reader" to "Legacy log"
    And I wait to be redirected
    And I should not see "Test name2"
    And I press "Pause live updates"
    And I follow "course_add mod"
    And I switch to "action" window
    And I am on "Course 1" course homepage
    And I add a "Database" to section "3" and I fill the form with:
      | Name | Test name2 |
      | Description | Test database description |
    And I switch to the main window
    And I wait "8" seconds
    Then I should not see "Test name2"
    And I press "Resume live updates"
    And I wait "8" seconds
    And I should see "Test name2"
    And I log out
