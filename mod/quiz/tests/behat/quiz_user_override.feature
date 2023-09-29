@mod @mod_quiz
Feature: Quiz user override
  In order to grant a student special access to a quiz
  As a teacher
  I need to create an override for that user.

  Background:
    And the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | frog       | Favourite frog |
    Given the following "users" exist:
      | username | firstname | lastname | email                | profile_field_frog |
      | teacher  | Teacher   | One      | teacher@example.com  |                    |
      | helper   | Exam      | Helper   | helper@example.com   |                    |
      | student1 | Student   | One      | student1@example.com | yellow frog        |
      | student2 | Student   | Two      | student2@example.com | prince frog        |
      | student3 | Student   | Three    | student3@example.com | Kermit             |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | helper   | C1     | teacher        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |

  @javascript
  Scenario: Add, modify then delete a user override
    Given the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher"
    And I change window size to "large"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student One (student1@example.com) |
      | id_timeclose_enabled | 1                                  |
      | timeclose[day]       | 1                                  |
      | timeclose[month]     | January                            |
      | timeclose[year]      | 2020                               |
      | timeclose[hour]      | 08                                 |
      | timeclose[minute]    | 00                                 |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"

    And I click on "Edit" "link" in the "Student One" "table_row"
    And I set the following fields to these values:
      | timeclose[year] | 2030 |
    And I press "Save"
    And I should see "Action"
    And I should see "Tuesday, 1 January 2030, 8:00" in the "Student One" "table_row"
    And I should see "student1@example.com" in the "Student One" "table_row"

    And I click on "Delete" "link" in the "Student One" "table_row"
    And I should see "Are you sure you want to delete the override for user Student One (student1@example.com)?"
    And I press "Continue"
    And I should not see "Student One"

  @javascript
  Scenario: Add multiple user overrides, one after another
    Given the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And I am on the "Test quiz" "mod_quiz > View" page logged in as "teacher"
    And I change window size to "large"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user      | Student One                |
      | timeclose[enabled] | 1                          |
      | Close the quiz     | ## 1 January 2020 08:00 ## |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override user      | Student Two                |
      | timeclose[enabled] | 1                          |
      | Close the quiz     | ## 2 January 2020 08:00 ## |
    When I press "Save"
    Then the following should exist in the "generaltable" table:
      | User        | Overrides   | -4-                  |
      | Student One | Quiz closes | 1 January 2020, 8:00 |
      | Student Two | Quiz closes | 2 January 2020, 8:00 |

  @javascript
  Scenario: Can add a user override when the quiz is not available to the student
    Given the following "activities" exist:
      | activity   | name      | course | idnumber | visible |
      | quiz       | Test quiz | C1     | quiz1    | 0       |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user    | Student One (student1@example.com) |
      | Attempts allowed | 1                                  |
    And I press "Save"
    Then I should see "This override is inactive"
    And I should see "Action"
    And "Edit" "icon" should exist in the "Student One" "table_row"
    And "copy" "icon" should exist in the "Student One" "table_row"
    And "Delete" "icon" should exist in the "Student One" "table_row"
    And I follow "Student One"
    And I should see "Student One"
    And I should see "User details"

  @javascript
  Scenario: Teacher without 'See full user identity in lists' can see and edit overrides
    Given the following "permission overrides" exist:
      | capability                   | permission | role           | contextlevel | reference |
      | moodle/site:viewuseridentity | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber | visible |
      | quiz       | Test quiz | C1     | quiz1    | 0       |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user    | Student One |
      | Attempts allowed | 1           |
    And I press "Save"
    And I should see "Action"
    And I should not see "student1@example.com"
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
      | teacher  | G1    |
      | student2 | G2    |
    And the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name      | course | idnumber | groupmode |
      | quiz     | Test quiz | C1     | quiz1    | 1         |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    And I press "Add user override"
    Then the "Override user" select box should contain "Student One (student1@example.com)"
    And the "Override user" select box should not contain "Student Two (student2@example.com)"

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
      | activity | name      | course | idnumber | groupmode |
      | quiz     | Test quiz | C1     | quiz1    | 1         |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    Then I should see "No groups you can access."
    And the "Add user override" "button" should be disabled

  Scenario: A non-editing teacher can see the overrides, but not change them
    Given the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "mod_quiz > user overrides" exist:
      | quiz      | user     | attempts |
      | Test quiz | student1 | 2        |
      | Test quiz | student2 | 2        |
    And I am on the "Test quiz" "mod_quiz > View" page logged in as "helper"
    When I navigate to "Overrides" in current page administration
    Then "Student One" "table_row" should exist
    And "Student Two" "table_row" should exist
    And "Add user override" "button" should not exist
    And I should not see "Action"
    And "Edit" "link" should not exist in the "Student One" "table_row"
    And "Copy" "link" should not exist in the "Student One" "table_row"
    And "Delete" "link" should not exist in the "Student One" "table_row"
    And I am on the "Test quiz" "mod_quiz > View" page
    And I should see "Settings overrides exist (Users: 2)"

  @javascript
  Scenario: Teachers can see user additional user identity information
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_frog |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "mod_quiz > user overrides" exist:
      | quiz      | user     | attempts |
      | Test quiz | student1 | 2        |
      | Test quiz | student2 | 2        |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    Then I should see "yellow frog" in the "Student One" "table_row"
    And I should see "prince frog" in the "Student Two" "table_row"

    And I press "Add user override"
    And I expand the "Override user" autocomplete
    And I should see "Kermit"
    And I should not see "Student one"
    And I should not see "Student two"
    And I press "Cancel"

    And I click on "Edit" "link" in the "Student One" "table_row"
    And I should see "Student One (student1@example.com, yellow frog)"
    And I press "Cancel"

    And I click on "Delete" "link" in the "Student One" "table_row"
    And I should see "Student One (student1@example.com, yellow frog)"

  Scenario: Add button disabled if no users
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | 0        |
    And the following "activities" exist:
      | activity   | name       | course | idnumber |
      | quiz       | Other quiz | C2     | quiz2    |
    When I am on the "Other quiz" "mod_quiz > User overrides" page logged in as "admin"
    Then the "Add user override" "button" should be disabled

  @javascript
  Scenario: Should see only enrolled users in user selector
    Given the following "users" exist:
      | username | firstname | lastname | email           |
      | manager  | Max       | Manager  | man@example.com |
    And the following "role assigns" exist:
      | user    | role    | contextlevel | reference |
      | manager | manager | System       |           |
    And the following "activities" exist:
      | activity | name      | course | idnumber | groupmode |
      | quiz     | Test quiz | C1     | quiz1    | 1         |
    And the following "role capability" exists:
      | role             | manager |
      | mod/quiz:attempt | allow   |
    When I am on the "Test quiz" "mod_quiz > User overrides" page logged in as "teacher"
    And I press "Add user override"
    And I click on "Override user" "field"
    And I type "Max Manager"
    Then I should see "No suggestions"
