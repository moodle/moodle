@core @core_group
Feature: Automatic creation of groups
  In order to quickly create groups
  As a teacher
  I need to create groups automatically and allocate them in groupings if necessary

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
      | student5 | Student | 5 | student5@example.com |
      | student6 | Student | 6 | student6@example.com |
      | student7 | Student | 7 | student7@example.com |
      | student8 | Student | 8 | student8@example.com |
      | student9 | Student | 9 | student9@example.com |
      | student10 | Student | 10 | student10@example.com |
      | suspendedstudent11 | Suspended student | 11 | suspendedstudent11@example.com |
    And the following "course enrolments" exist:
      | user | course | role | status |
      | teacher1 | C1 | editingteacher | 0 |
      | student1 | C1 | student | 0 |
      | student2 | C1 | student | 0 |
      | student3 | C1 | student | 0 |
      | student4 | C1 | student | 0 |
      | student5 | C1 | student | 0 |
      | student6 | C1 | student | 0 |
      | student7 | C1 | student | 0 |
      | student8 | C1 | student | 0 |
      | student9 | C1 | student | 0 |
      | student10 | C1 | student | 0 |
      | suspendedstudent11 | C1 | student | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    When I press "Auto-create groups"
    And I expand all fieldsets

  @javascript
  Scenario: Split automatically the course users in groups and add the groups to a new grouping
    Given I set the following fields to these values:
      | Auto create based on | Number of groups |
      | Group/member count | 2 |
      | Grouping of auto-created groups | New grouping |
      | Grouping name | Grouping name |
    And I press "Preview"
    Then I should see "Group members"
    And I should see "User count"
    And I should see "Group A"
    And I should see "Group B"
    And I press "Submit"
    And the "groups" select box should contain "Group A (5)"
    And the "groups" select box should contain "Group B (5)"
    # Check that group messaging is not enabled for the auto-created groups.
    And I set the field "groups" to "Group A"
    And I press "Edit group settings"
    And I should see "No" in the "Group messaging" "select"
    And I press "Cancel"
    And I set the field "groups" to "Group B"
    And I press "Edit group settings"
    And I should see "No" in the "Group messaging" "select"
    And I press "Cancel"
    # Check groupings.
    And I follow "Groupings"
    And I should see "Grouping name"
    And I click on "Show groups in grouping" "link" in the "Grouping name" "table_row"
    And the "removeselect" select box should contain "Group A"
    And the "removeselect" select box should contain "Group B"

  @javascript
  Scenario: Split automatically the course users in groups based on group member count
    Given I set the following fields to these values:
      | Auto create based on | Members per group |
      | Group/member count | 4 |
      | Grouping of auto-created groups | New grouping |
      | Grouping name | Grouping name |
      | Allocate members | Alphabetically by last name, first name |
    And I press "Preview"
    Then the following should exist in the "generaltable" table:
      | Groups (3)   | Group members                    | User count (10) |
      | Group A      | Student 1 (student1@example.com) | 4               |
      | Group B      | Student 5 (student5@example.com) | 4               |
      | Group C      | Student 9 (student9@example.com) | 2               |
    And I set the field "Prevent last small group" to "1"
    And I press "Preview"
    And I should see "Group A" in the ".generaltable" "css_element"
    And I should see "Group B" in the ".generaltable" "css_element"
    And I should see "5" in the "Group A" "table_row"
    And I should see "5" in the "Group B" "table_row"

  @javascript
  Scenario: Split automatically the course users in groups that are not in groups
    Given I press "Cancel"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 1 |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
      | Group name | Group 2 |
    And I press "Save changes"
    When I add "Student 1" user to "Group 1" group members
    And I add "Student 2" user to "Group 1" group members
    And I add "Student 3" user to "Group 2" group members
    And I add "Student 4" user to "Group 2" group members
    And I press "Auto-create groups"
    And I expand all fieldsets
    And I set the field "Auto create based on" to "Number of groups"
    And I set the field "Group/member count" to "2"
    And I set the field "Grouping of auto-created groups" to "No grouping"
    And I set the field "Ignore users in groups" to "1"
    And I press "Submit"
    And the "groups" select box should contain "Group A (3)"
    And the "groups" select box should contain "Group B (3)"

  @javascript
  Scenario: Split users into groups based on existing groups or groupings
    Given I set the following fields to these values:
      | Naming scheme | Group @ |
      | Auto create based on | Number of groups |
      | Group/member count | 2 |
      | Grouping of auto-created groups | No grouping |
    And I press "Submit"
    And I press "Auto-create groups"
    And I set the following fields to these values:
      | Naming scheme | Test @ |
      | Auto create based on | Number of groups |
      | Group/member count | 2 |
      | groupid | Group A |
      | Grouping of auto-created groups | New grouping |
      | Grouping name | Sub Grouping |
    And I press "Submit"
    And the "groups" select box should contain "Test A (3)"
    And the "groups" select box should contain "Test B (2)"
    And I press "Auto-create groups"
    And I set the following fields to these values:
      | Naming scheme | Test # |
      | Auto create based on | Number of groups |
      | Group/member count | 2 |
      | Select members from grouping | Sub Grouping |
      | Grouping of auto-created groups | No grouping |
    And I press "Submit"
    And the "groups" select box should contain "Test 1 (3)"
    And the "groups" select box should contain "Test 2 (2)"

  Scenario: Exclude suspended users when auto-creating groups
    Given I set the field "Include only active enrolments" to "1"
    And I set the field "Auto create based on" to "Members per group"
    When I set the field "Group/member count" to "11"
    And I press "Preview"
    Then I should not see "Suspended Student 11"

  Scenario: Include suspended users when auto-creating groups
    Given I set the field "Include only active enrolments" to "0"
    And I set the field "Auto create based on" to "Members per group"
    When I set the field "Group/member count" to "11"
    And I press "Preview"
    Then I should see "Suspended student 11 (suspendedstudent11@example.com)"

  Scenario: Do not display 'Include only active enrolments' if user does not have the 'moodle/course:viewsuspendedusers' capability
    Given I log out
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability | permission |
      | moodle/course:viewsuspendedusers | Prevent |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Groups" in current page administration
    When I press "Auto-create groups"
    Then I should not see "Include only active enrolments"
    And I set the field "Group/member count" to "11"
    And I press "Preview"
    And I should not see "Suspended Student 11"

  @javascript
  Scenario: Auto-create groups with group messaging
    Given I set the following fields to these values:
      | Naming scheme | Group @ |
      | Auto create based on | Number of groups |
      | Group/member count | 2 |
      | Grouping of auto-created groups | No grouping |
      | Group messaging | Yes |
    And I press "Submit"
    And I set the field "groups" to "Group A"
    When I press "Edit group settings"
    Then I should see "Yes" in the "Group messaging" "select"
    And I press "Cancel"
    And I set the field "groups" to "Group B"
    And I press "Edit group settings"
    And I should see "Yes" in the "Group messaging" "select"
