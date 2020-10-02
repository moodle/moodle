@mod @mod_quiz
Feature: Quiz user override
  In order to grant a student special access to a quiz
  As a teacher
  I need to create an override for that user.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |

  @javascript
  Scenario: Add, modify then delete a user override
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Quiz 1"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_timeclose_enabled | 1        |
      | timeclose[day]       | 1        |
      | timeclose[month]     | January  |
      | timeclose[year]      | 2020     |
      | timeclose[hour]      | 08       |
      | timeclose[minute]    | 00       |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "Student One" "table_row"
    And I set the following fields to these values:
      | timeclose[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Student One"

  @javascript
  Scenario: Being able to modify a user override when the quiz is not available to the student
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Availability" to "Hide from students"
    And I click on "Save and display" "button"
    When I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user    | Student1 |
      | Attempts allowed | 1        |
    And I press "Save"
    Then I should see "This override is inactive"
    And "Edit" "icon" should exist in the "Student One" "table_row"
    And "copy" "icon" should exist in the "Student One" "table_row"
    And "Delete" "icon" should exist in the "Student One" "table_row"

  Scenario: A teacher without accessallgroups permission should only be able to add user override for users that he/she shares groups with,
        when the activity's group mode is to "separate groups"
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | teacher1 | G1    |
      | student2 | G2    |
    And the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | groupmode |
      | quiz     | Quiz 2 | Quiz 2 description | C1     | quiz2    | 1         |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 2"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    Then the "Override user" select box should contain "Student One, student1@example.com"
    And the "Override user" select box should not contain "Student Two, student2@example.com"

  Scenario: Override user in an activity with group mode set to "separate groups" as a teacher who is not a member in any group, and does not have accessallgroups permission
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
    And the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | groupmode |
      | quiz     | Quiz 2 | Quiz 2 description | C1     | quiz2    | 1         |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 2"
    And I navigate to "User overrides" in current page administration
    Then I should see "No groups you can access."
    And the "Add user override" "button" should be disabled
