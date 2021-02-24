@report @report_log
Feature: User can view activity log.
  In order to view user log
  As an teacher
  I need to view user today's and all report

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
      | activity                            | assign                  |
      | course                              | C1                      |
      | idnumber                            | 0001                    |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | section                             | 1                       |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I log out

  Scenario: View Todays' and all log report for user
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    When I follow "Today's logs"
    And I should see "Assignment: Test assignment name"
    And I follow "Student 1"
    And I follow "All logs"
    Then I should see "Assignment: Test assignment name"

  Scenario: No log reader enabled should be visible when no log store enabled.
    Given I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    When I follow "Today's logs"
    And I should see "No log reader enabled"
    And I follow "Student 1"
    And I follow "All logs"
    Then I should see "No log reader enabled"
