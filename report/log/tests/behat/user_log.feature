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
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: View Todays' and all log report for user
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 1"
    When I navigate to "Today's logs" node in "Profile settings for Student 1 > Activity reports"
    And I should see "Assignment: Test assignment name"
    And I navigate to "All logs" node in "Profile settings for Student 1 > Activity reports"
    Then I should see "Assignment: Test assignment name"

  @javascript
  Scenario: No log reader enabled should be visible when no log store enabled.
    Given I log in as "admin"
    And I navigate to "Manage log stores" node in "Site administration > Plugins > Logging"
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"
    And I follow "Student 1"
    When I navigate to "Today's logs" node in "Profile settings for Student 1 > Activity reports"
    And I should see "No log reader enabled"
    And I navigate to "All logs" node in "Profile settings for Student 1 > Activity reports"
    Then I should see "No log reader enabled"
