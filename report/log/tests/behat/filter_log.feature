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
      | username | firstname | lastname    | email                | idnumber | middlename | alternatename | firstnamephonetic | lastnamephonetic |
      | teacher1 | Teacher   | One         | teacher1@example.com | t1       |            | fred          |                   |                  |
      | student1 | Grainne   | Beauchamp   | student1@example.com | s1       | Ann        | Jill          | Gronya            | Beecham          |
    And the following "course enrolments" exist:
      | user | course | role |
      | admin | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | fullnamedisplay | firstname |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |
    And I log in as "admin"

  Scenario: Filter log report for standard log reader
    Given I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Ann, Jill, Grainne, Beauchamp"
    And I click on "Log in as" "link"
    And I press "Continue"
    And I log out
    And I log in as "admin"
    When I navigate to "Reports > Logs" in site administration
    And I set the field "id" to "Acceptance test site (Site)"
    And I set the field "user" to "All participants"
    And I press "Get these logs"
    Then I should see "User logged in as another user"
