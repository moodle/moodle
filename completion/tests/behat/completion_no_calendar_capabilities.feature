@core @core_completion
Feature: Completion with no calendar capabilites
  In order to allow work effectively
  As a teacher
  I need to be able to create activities with completion enabled without calendar capabilities

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | enablecompletion |
      | Course 1 | C1 | 0 | 1 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity    | forum                       |
      | course      | C1                          |
      | idnumber    | 00001                       |
      | name        | Test forum name             |
      | completion  | 2                           |
    And I am on the "Test forum name" "forum activity editing" page logged in as admin
    And I set the following fields to these values:
      | id_completionexpected_enabled | 1    |
      | id_completionexpected_day     | 1    |
      | id_completionexpected_month   | 1    |
      | id_completionexpected_year    | 2017 |
    And I press "Save and return to course"
    And I am on the "Course 1" "permissions" page
    And I override the system permissions of "Teacher" role with:
      | capability | permission |
      | moodle/calendar:manageentries | Prohibit |

  Scenario: Editing completion date
    When I am on the "Test forum name" "forum activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_completionexpected_year | 2018 |
    And I press "Save and return to course"
    Then I should see "Test forum name"
