@local @local_bulkenrol @local_bulkenrol_users @javascript
Feature: Using the local_bulkenrol plugin for user enrolments
  In order to bulk enrol users into the course
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

  Scenario: Bulk enrol students into the course who are not enrolled yet with authentication method self
    Given the following config values are set as admin:
      | config      | value | plugin          |
      | enrolplugin | self  | local_bulkenrol |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com
      student2@example.com
      student3@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method | Assigned role |
      | Self enrolment   | Student       |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
      | student2@example.com | Student    | 2         | Student |
      | student3@example.com | Student    | 3         | Student |
    And "div[data-fullname='Student 1'][data-enrolinstancename='Self enrolment (Student)'][data-status='Active']" "css_element" should exist

  Scenario: Bulk enrol students into the course who are not enrolled yet with authentication method manual
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com
      student2@example.com
      student3@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method  | Assigned role |
      | Manual enrolments | Student       |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
      | student2@example.com | Student    | 2         | Student |
      | student3@example.com | Student    | 3         | Student |
    And "div[data-fullname='Student 1'][data-enrolinstancename='Manual enrolments'][data-status='Active']" "css_element" should exist

  Scenario: Bulk enrol users into the course who are not enrolled yet with role teacher
    Given I log in as "admin"
    And I navigate to "Plugins > Enrolments > User bulk enrolment" in site administration
    And I set the following fields to these values:
      | Role | Teacher |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com
      student2@example.com
      student3@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method  | Assigned role |
      | Manual enrolments | Teacher       |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Teacher |
      | student2@example.com | Student    | 2         | Teacher |
      | student3@example.com | Student    | 3         | Teacher |
    And "div[data-fullname='Student 1'][data-enrolinstancename='Manual enrolments'][data-status='Active']" "css_element" should exist

  Scenario: Bulk enrol students into the course with students already enrolled
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com
      student2@example.com
      student3@example.com
      """
    And I click on "Enrol users" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           |
      | student1@example.com | Student    | 1         | User is already enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled    |
      | student3@example.com | Student    | 3         | User will be enrolled    |
    And I click on "Enrol users" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
      | student2@example.com | Student    | 2         | Student |
      | student3@example.com | Student    | 3         | Student |

  Scenario: Respect existing self enrolments during bulk enrol
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I add "Self enrolment" enrolment method in "Course 1" with:
      | Custom instance name | Self enrolment |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I press "Enrol me"
    And I should see "New section"
    And I should not see "Enrol me in this course"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com
      """
    And I click on "Enrol users" "button"
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           |
      | student1@example.com | Student    | 1         | User is already enrolled |
    And I click on "Enrol users" "button"
    And the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
    Then "div[data-fullname='Student 1'][data-enrolinstancename='Manual enrolments'] a[data-action=showdetails]" "css_element" should not exist
    And "div[data-fullname='Student 1'][data-enrolinstancename='Self enrolment'] a[data-action=showdetails]" "css_element" should exist

  Scenario: Try to bulk enrol a student into the course that is not existent in the system.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student4@example.com
      """
    And I click on "Enrol users" "button"
    Then I should see "No existing Moodle user account with e-mail address student4@example.com."

  Scenario: Try to bulk enrol a list of invalid users.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      foo
      bar
      """
    And I click on "Enrol users" "button"
    Then I should see "No valid e-mail address was found in the given list."
    And I should see "Please go back and check your input"
    And "Enrol users" "button" should not exist

  Scenario: Try to bulk enrol a list of mixed invalid users and empty line.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student1@example.com

      foo
      """
    And I click on "Enrol users" "button"
    Then I should see "Line 2 is empty and will be ignored."
    And I should see "No e-mail address found in line 3 (foo). This line will be ignored."
    And I should see "Manual enrolments"
