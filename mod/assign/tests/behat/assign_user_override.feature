@mod @mod_assign
Feature: Assign user override
  In order to grant a student special access to an assignment
  As a teacher
  I need to create an override for that user.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | name                 | intro                   | course | idnumber | assignsubmission_onlinetext_enabled |
      | assign   | Test assignment name | Submit your online text | C1     | assign1  | 1                                   |

  @javascript
  Scenario: Add, modify then delete a user override
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I follow "Test assignment name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "Sam1 Student1" "table_row"
    And I set the following fields to these values:
      | duedate[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Sam1 Student1"

  @javascript
  Scenario: Duplicate a user override
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I follow "Test assignment name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "copy" "link"
    And I set the following fields to these values:
      | Override user  | Student2  |
      | duedate[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Sam2 Student2"

  @javascript
  Scenario: Allow a user to have a different due date
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 1 |
      | id_allowsubmissionsfromdate_enabled | 0 |
      | id_cutoffdate_enabled | 0 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2000 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_duedate_enabled | 1 |
      | duedate[day]       | 1 |
      | duedate[month]     | January |
      | duedate[year]      | 2020 |
      | duedate[hour]      | 08 |
      | duedate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "Saturday, 1 January 2000, 8:00"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "Wednesday, 1 January 2020, 8:00"

  @javascript
  Scenario: Allow a user to have a different cut off date
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 0 |
      | id_allowsubmissionsfromdate_enabled | 0 |
      | id_cutoffdate_enabled | 1 |
      | cutoffdate[day]       | 1 |
      | cutoffdate[month]     | January |
      | cutoffdate[year]      | 2000 |
      | cutoffdate[hour]      | 08 |
      | cutoffdate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1 |
      | id_cutoffdate_enabled | 1 |
      | cutoffdate[day]       | 1 |
      | cutoffdate[month]     | January |
      | cutoffdate[year]      | 2020 |
      | cutoffdate[hour]      | 08 |
      | cutoffdate[minute]    | 00 |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should not see "You have not made a submission yet."
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "You have not made a submission yet."

  @javascript
  Scenario: Allow a user to have a different start date
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_duedate_enabled | 0 |
      | id_allowsubmissionsfromdate_enabled | 1 |
      | id_cutoffdate_enabled | 0 |
      | allowsubmissionsfromdate[day]       | 1 |
      | allowsubmissionsfromdate[month]     | January |
      | allowsubmissionsfromdate[year]      | 2020 |
      | allowsubmissionsfromdate[hour]      | 08 |
      | allowsubmissionsfromdate[minute]    | 00 |
    And I press "Save and display"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_allowsubmissionsfromdate_enabled | 1 |
      | allowsubmissionsfromdate[day]       | 1 |
      | allowsubmissionsfromdate[month]     | January |
      | allowsubmissionsfromdate[year]      | 2015 |
      | allowsubmissionsfromdate[hour]      | 08 |
      | allowsubmissionsfromdate[minute]    | 00 |
    And I press "Save"
    And I should see "Thursday, 1 January 2015, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "This assignment will accept submissions from Wednesday, 1 January 2020, 8:00"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "This assignment will accept submissions from Wednesday, 1 January 2020, 8:00"

  Scenario: Override a user when teacher is in no group, and does not have accessallgroups permission, and the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | idnumber | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | assign2  | 1         |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Assignment 2"
    And I navigate to "User overrides" in current page administration
    Then I should see "No groups you can access."
    And the "Add user override" "button" should be disabled

  Scenario: A teacher without accessallgroups permission should only be able to add user override for users that he/she shares groups with,
        when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | idnumber | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | assign2  | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Assignment 2"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    Then the "Override user" select box should contain "Sam1 Student1, student1@example.com"
    And the "Override user" select box should not contain "Sam2 Student2, student2@example.com"

  @javascript
  Scenario: A teacher without accessallgroups permission should only be able to see the user override for users that he/she shares groups with,
        when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | idnumber | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | assign2  | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Assignment 2"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user                       | Student1 |
      | id_allowsubmissionsfromdate_enabled | 1       |
      | allowsubmissionsfromdate[day]       | 1       |
      | allowsubmissionsfromdate[month]     | January |
      | allowsubmissionsfromdate[year]      | 2015    |
      | allowsubmissionsfromdate[hour]      | 08      |
      | allowsubmissionsfromdate[minute]    | 00      |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override user                       | Student2 |
      | id_allowsubmissionsfromdate_enabled | 1        |
      | allowsubmissionsfromdate[day]       | 1        |
      | allowsubmissionsfromdate[month]     | January  |
      | allowsubmissionsfromdate[year]      | 2015     |
      | allowsubmissionsfromdate[hour]      | 08       |
      | allowsubmissionsfromdate[minute]    | 00       |
    And I press "Save"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Assignment 2"
    And I navigate to "User overrides" in current page administration
    Then I should see "Student1" in the ".generaltable" "css_element"
    And I should not see "Student2" in the ".generaltable" "css_element"
