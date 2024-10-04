@mod @mod_choice
Feature: Teacher can choose whether to allow students to change their choice response
  In order to allow students to change their choice
  As a teacher
  I need to enable the option to change the choice

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Change a choice response as a student
    Given the following "activity" exists:
      | activity    | choice             |
      | course      | C1                 |
      | idnumber    | Choice name        |
      | name        | Choice name        |
      | intro       | Choice Description |
      | section     | 1                  |
      | option      | Option 1, Option 2 |
      | allowupdate | 0                  |
    When I am on the "Course 1" course page logged in as student1
    And I choose "Option 1" from "Choice name" choice activity
    And I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    Then "Save my choice" "button" should not exist

  Scenario: Change a choice response as a student
    Given the following "activity" exists:
      | activity    | choice             |
      | course      | C1                 |
      | idnumber    | Choice name        |
      | name        | Choice name        |
      | intro       | Choice Description |
      | section     | 1                  |
      | option      | Option 1, Option 2 |
      | allowupdate | 1                  |
    When I am on the "Course 1" course page logged in as student1
    And I choose "Option 1" from "Choice name" choice activity
    And I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    Then I should see "Your selection: Option 1"
    And "Save my choice" "button" should exist
    And "Remove my choice" "link" should exist
    And I set the field "Option 2" to "1"
    And I press "Save my choice"
    And I should see "Your choice has been saved"
    And I should see "Your selection: Option 2"
