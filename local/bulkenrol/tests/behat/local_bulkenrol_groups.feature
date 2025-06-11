@local @local_bulkenrol @local_bulkenrol_groups @javascript
Feature: Using the local_bulkenrol plugin for group management
  In order to manage groups in the course
  As user with the appropriate rights
  I need to be able to use the plugin local_bulkenrol

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config      | value   | plugin          |
      | enrolplugin | manual  | local_bulkenrol |
      | navigation  | navpart | local_bulkenrol |
    Given I log in as "admin"
    And I navigate to "Plugins > Enrolments > User bulk enrolment" in site administration
    And I set the following fields to these values:
      | Role | Student |
    And I press "Save changes"
    And I set the following system permissions of "Teacher" role:
      | capability                 | permission |
      | local/bulkenrol:enrolusers | Allow      |
    And I log out

  Scenario: Bulk enrol students into the course and into (existing) groups
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | CG1      |
      | Group 2 | C1     | CG2      |
      | Group 3 | C1     | CG3      |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      # Group 1
      student1@example.com
      # Group 2
      student2@example.com
      # Group 3
      student3@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_groupinfos" table:
      | Group name | Group status         |
      | Group 1    | Group already exists |
      | Group 2    | Group already exists |
      | Group 3    | Group already exists |
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        | Group membership |
      | student1@example.com | Student    | 1         | User will be enrolled | Group 1          |
      | student2@example.com | Student    | 2         | User will be enrolled | Group 2          |
      | student3@example.com | Student    | 3         | User will be enrolled | Group 3          |
    # We have to check the group membership action badge in a separate step,
    # otherwise we would trigger the 'Table contains duplicate column headers' coding exception message.
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | Group membership            |
      | student1@example.com | User will be added to group |
      | student2@example.com | User will be added to group |
      | student3@example.com | User will be added to group |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   | Groups  |
      | student1@example.com | Student    | 1         | Student | Group 1 |
      | student2@example.com | Student    | 2         | Student | Group 2 |
      | student3@example.com | Student    | 3         | Student | Group 3 |

  Scenario: Bulk enrol students into the course with students already enrolled and who only have to be added to (existing) groups
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | CG1      |
      | Group 2 | C1     | CG2      |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      # Group 1
      student1@example.com
      # Group 2
      student2@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_groupinfos" table:
      | Group name | Group status         |
      | Group 1    | Group already exists |
      | Group 2    | Group already exists |
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           | Group membership |
      | student1@example.com | Student    | 1         | User is already enrolled | Group 1          |
      | student2@example.com | Student    | 2         | User is already enrolled | Group 2          |
    # We have to check the group membership action badge in a separate step,
    # otherwise we would trigger the 'Table contains duplicate column headers' coding exception message.
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | Group membership            |
      | student1@example.com | User will be added to group |
      | student2@example.com | User will be added to group |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   | Groups  |
      | student1@example.com | Student    | 1         | Student | Group 1 |
      | student2@example.com | Student    | 2         | Student | Group 2 |

  Scenario: Bulk enrol students into the course with students already enrolled and who are also a member of the given (existing) groups
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | CG1      |
      | Group 2 | C1     | CG2      |
    And the following "group members" exist:
      | group | user     |
      | CG1   | student1 |
      | CG2   | student2 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      # Group 1
      student1@example.com
      # Group 2
      student2@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_groupinfos" table:
      | Group name | Group status         |
      | Group 1    | Group already exists |
      | Group 2    | Group already exists |
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           | Group membership |
      | student1@example.com | Student    | 1         | User is already enrolled | Group 1          |
      | student2@example.com | Student    | 2         | User is already enrolled | Group 2          |
    # We have to check the group membership action badge in a separate step,
    # otherwise we would trigger the 'Table contains duplicate column headers' coding exception message.
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | Group membership             |
      | student1@example.com | User is already group member |
      | student2@example.com | User is already group member |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   | Groups  |
      | student1@example.com | Student    | 1         | Student | Group 1 |
      | student2@example.com | Student    | 2         | Student | Group 2 |

  Scenario: Bulk enrol students into the course and create groups if needed
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | CG1      |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      # Group 1
      student1@example.com
      # Group 2
      student2@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_groupinfos" table:
      | Group name | Group status          |
      | Group 1    | Group already exists  |
      | Group 2    | Group will be created |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   | Groups  |
      | student1@example.com | Student    | 1         | Student | Group 1 |
      | student2@example.com | Student    | 2         | Student | Group 2 |
