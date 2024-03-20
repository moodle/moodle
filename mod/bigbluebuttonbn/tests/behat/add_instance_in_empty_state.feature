@mod @mod_bigbluebuttonbn @javascript
Feature: I can create a bigbluebuttonbn instance with default server
    In case the BigBlueButton server has not been configured
    As a user
    I want to see a notification message

  Background:  Make sure that a course is created
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | user1    | User1G1       | 1        | user1@example.com    |
      | teacher1 | TeacherG1     | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course          | role    |
      | user1    | Test course     | student |
      | teacher1 | Test course     | editingteacher |
    And the following "activities" exist:
      | activity        | course          | name                | type |
      | bigbluebuttonbn | Test course     | BBB Instance name   | 0    |
      | bigbluebuttonbn | Test course     | BBB Instance name 2 | 1    |
      | bigbluebuttonbn | Test course     | BBB Instance name 3 | 2    |
    And I am on the "Test course" "course" page logged in as "admin"

  Scenario Outline: Add an activity using default server for the three types of instance types
    When I change window size to "large"
    And I add a bigbluebuttonbn activity to course "Test course" section "1"
    And I select "<type>" from the "Instance type" singleselect
    Then I should see "Restrict access"

    Examples:
      | type                          |
      | Room with recordings          |
      | Room only                     |
      | Recordings only               |

  Scenario Outline: Users should see a notification message when accessing activities if the default server is used
    When I am on the "BBB Instance name" Activity page logged in as <user>
    Then "Join session" "link" should exist
    And I <messageexist> "<shouldseemessage>"

    Examples:
      | user       | shouldseemessage | messageexist |
      | user1      | The use of default server credentials will soon expire.| should not see |
      | teacher1   | The use of default server credentials will soon expire.| should see |
      | admin      | Default BigBlueButton plugin credentials will soon expire.| should see |
