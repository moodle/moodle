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
      | activity | name                 | intro                   | course | assignsubmission_onlinetext_enabled |
      | assign   | Test assignment name | Submit your online text | C1     | 1                                   |

  @javascript
  Scenario: Add, modify then delete a user override
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Student1                            |
      | Due date      | ##first day of January 2020 08:00## |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"
    And I click on "Edit" "link" in the "Sam1 Student1" "table_row"
    And I set the following fields to these values:
      | Due date      | ##first day of January 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Sam1 Student1"

  @javascript
  Scenario: Duplicate a user override
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Student1             |
      | Due date      | ##2020-01-01 08:00## |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"
    And I click on "copy" "link"
    And I set the following fields to these values:
      | Override user | Student2             |
      | Due date      | ##2030-01-01 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Sam2 Student2"

  @javascript
  Scenario: Allow a user to have a different due date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Allow submissions from | disabled             |
      | Due date               | ##1 Jan 2000 08:00## |
      | Cut-off date           | disabled             |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user | Student1             |
      | Due date      | ##1 Jan 2020 08:00## |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    And the activity date in "Test assignment name" should contain "Due: Saturday, 1 January 2000, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And the activity date in "Test assignment name" should contain "Due: Wednesday, 1 January 2020, 8:00"

  @javascript
  Scenario: Allow a user to have a different cut off date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Due date               | disabled             |
      | Allow submissions from | disabled             |
      | Cut-off date           | ##1 Jan 2000 08:00## |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user       | Student1             |
      | Cut-off date        | ##1 Jan 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    Then I should not see "Add submission"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And I should see "Add submission"

  @javascript
  Scenario: Allow a user to have a different start date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Due date               | disabled                 |
      | Allow submissions from | ##1 January 2030 08:00## |
      | Cut-off date           | disabled                 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user          | Student1             |
      | Allow submissions from | ##1 Jan 2015 08:00## |
    And I press "Save"
    And I should see "Thursday, 1 January 2015, 8:00"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I click on "Test assignment name" "link" in the "region-main" "region"
    Then the activity date in "Test assignment name" should contain "Opens: Tuesday, 1 January 2030, 8:00"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "Test assignment name" "link" in the "region-main" "region"
    And I should not see "1 January 2030, 8:00"

  Scenario: Override a user when teacher is in no group, and does not have accessallgroups permission, and the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And I am on the "Assignment 2" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    Then I should see "No groups you can access."
    And the "Add user override" "button" should be disabled

  Scenario: A teacher without accessallgroups permission should only be able to add user override for users that he/she shares groups with,
        when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    And I am on the "Assignment 2" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
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
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | student1 | G1    |
      | student2 | G2    |
    And I am on the "Assignment 2" Activity page logged in as admin
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user          | Student1                            |
      | Allow submissions from | ##first day of January 2015 08:00## |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override user          | Student2                            |
      | Allow submissions from | ##first day of January 2015 08:00## |
    And I press "Save"
    And I log out

    And I am on the "Assignment 2" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    Then I should see "Student1" in the ".generaltable" "css_element"
    But I should not see "Student2" in the ".generaltable" "css_element"

  @javascript
  Scenario: Create a user override when the assignment is not available to the student
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Availability" to "Hide from students"
    And I click on "Save and display" "button"
    When I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user          | Student1             |
      | Allow submissions from | ##1 Jan 2015 08:00## |
    And I press "Save"
    Then I should see "This override is inactive"
    And "Edit" "icon" should exist in the "Sam1 Student1" "table_row"
    And "copy" "icon" should exist in the "Sam1 Student1" "table_row"
    And "Delete" "icon" should exist in the "Sam1 Student1" "table_row"
