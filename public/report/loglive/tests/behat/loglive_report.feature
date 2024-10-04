@report @report_loglive
Feature: In a report, admin can see loglive data
  In order see loglive data
  As an admin
  I need to view loglive report and see if the live update feature works

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname    | email                | idnumber | middlename | alternatename | firstnamephonetic | lastnamephonetic |
      | student1 | Grainne   | Beauchamp   | student1@example.com | s1       | Ann        | Jill          | Gronya            | Beecham          |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | fullnamedisplay | firstname |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |
    And I log in as "admin"
    And I add a data activity to course "Course 1" section "3" and I fill the form with:
      | Name | Test name |
      | Description | Test database description |

  @javascript
  Scenario: Check loglive report entries and make sure the report works for standard reader
    Given I navigate to "Reports > Live logs" in site administration
    When I should see "Course module created"
    Then I should see "Test name"

  @javascript @_switch_window
  Scenario: Check loglive report entries and make sure the pause/resume button works for standard reader along with ajax calls
    Given I am on site homepage
    When I navigate to "Reports > Live logs" in site administration
    And I should not see "Test name2"
    And I press "Pause live updates"
    And I follow "Course module created"
    And I switch to "action" window
    And I am on "Course 1" course homepage
    And I change window size to "large"
    And I reload the page
    And I add a data activity to course "Course 1" section "3" and I fill the form with:
      | Name | Test name2 |
      | Description | Test database description |
    And I switch to the main window
    And I wait "8" seconds
    Then I should not see "Test name2"
    And I press "Resume live updates"
    And I wait "8" seconds
    And I should see "Test name2"

  @javascript
  Scenario: Check course loglive report entries for a user
    Given I log out
    And I am on the "Test name" "data activity" page logged in as student1
    And I log out
    And I am on the "Course 1" Course page logged in as admin
    When I navigate to "Reports > Live logs" in site administration
    Then I should see "Course module viewed"
    And I should see "Test name"
    And I should see "Ann, Jill, Grainne, Beauchamp"
