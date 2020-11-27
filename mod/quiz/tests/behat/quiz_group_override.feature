@mod @mod_quiz
Feature: Quiz group override
  In order to grant a group special access to a quiz
  As a teacher
  I need to create an override for that group.

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname  | email                |
      | teacher1 | Terry 1    | Teacher 1 | teacher1@example.com |
      | student1 | Sam 1      | Student 1 | student1@example.com |
      | teacher2 | Terry 2    | Teacher 2 | teacher2@example.com |
      | student2 | Sam 2      | Student 2 | student2@example.com |
      | teacher3 | Terry 3    | Teacher 3 | teacher3@example.com |
      | student3 | Sam 3      | Student 3 | student3@example.com |
      | helper   | Exam       | Helper    | helper@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | teacher2 | C1     | editingteacher |
      | student2 | C1     | student        |
      | teacher3 | C1     | editingteacher |
      | student3 | C1     | student        |
      | helper   | C1     | teacher        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group 3 | C1     | G3       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | teacher1 | G1    |
      | teacher1 | G3    |
      | student2 | G2    |
      | teacher2 | G2    |
      | teacher2 | G3    |
      | student3 | G3    |
      | helper   | G1    |
      | helper   | G2    |
      | helper   | G3    |
    And the following "activities" exist:
      | activity | name      | intro                 | course | idnumber | groupmode |
      | quiz     | Test quiz | Test quiz description | C1     | quiz1    | 1         |

  Scenario: Override Group 1 as teacher of Group 1
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher1"
    And I press "Add group override"
    Then the "Override group" select box should contain "Group 1"
    And the "Override group" select box should not contain "Group 2"

  Scenario: Override Group 1 as teacher in no group
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher3"
    Then I should see "No groups you can access."
    And the "Add group override" "button" should be disabled

  Scenario: A teacher can create an override
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "teacher1"
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group   | Group 1 |
      | Attempts allowed | 2       |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override group   | Group 3 |
      | Attempts allowed | 2       |
    And I press "Save"
    Then "Group 1" "table_row" should exist

  Scenario: A teacher with accessallgroups permission should see all group overrides
    Given the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher1"
    Then I should see "Settings overrides exist (Groups: 2)"
    And I follow "Groups: 2"
    And "Group 1" "table_row" should exist
    And "Group 2" "table_row" should exist

  Scenario: A teacher without accessallgroups permission should only see the group overrides within his/her groups, when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher1"
    Then I should see "Settings overrides exist (Groups: 1) for your groups"
    And I follow "Groups: 1"
    Then "Group 1" "table_row" should exist
    And "Group 2" "table_row" should not exist

  Scenario: A non-editing teacher can see the overrides, but not change them
    Given the following "mod_quiz > group overrides" exist:
      | quiz      | group | attempts |
      | Test quiz | G1    | 2        |
      | Test quiz | G2    | 2        |
    When I am on the "Test quiz" "mod_quiz > Group overrides" page logged in as "helper"
    Then "Group 1" "table_row" should exist
    And "Group 2" "table_row" should exist
    And "Add group override" "button" should not exist
    And "Edit" "link" should not exist in the "Group 1" "table_row"
    And "Copy" "link" should not exist in the "Group 1" "table_row"
    And "Delete" "link" should not exist in the "Group 1" "table_row"
