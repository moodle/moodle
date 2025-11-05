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

  Scenario: Bulk enrol students into the course who are not enrolled yet with enrolment method self
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
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method | Assigned role |
      | Self enrolment   | Student       |
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
      | student2@example.com | Student    | 2         | Student |
      | student3@example.com | Student    | 3         | Student |
    And "div[data-fullname='Student 1'][data-enrolinstancename='Self enrolment (Student)'][data-status='Active']" "css_element" should exist

  Scenario: Bulk enrol students into the course who are not enrolled yet with enrolment method manual
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
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method  | Assigned role |
      | Manual enrolments | Student       |
    And I click on "Execute user enrolment" "button"
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
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment        |
      | student1@example.com | Student    | 1         | User will be enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled |
      | student3@example.com | Student    | 3         | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method  | Assigned role |
      | Manual enrolments | Teacher       |
    And I click on "Execute user enrolment" "button"
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
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           |
      | student1@example.com | Student    | 1         | User is already enrolled |
      | student2@example.com | Student    | 2         | User will be enrolled    |
      | student3@example.com | Student    | 3         | User will be enrolled    |
    And I click on "Execute user enrolment" "button"
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
    And I click on "Execute user enrolment" "button"
    And the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment           |
      | student1@example.com | Student    | 1         | User is already enrolled |
    And I click on "Execute user enrolment" "button"
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
    And I click on "Execute user enrolment" "button"
    Then I should see "No existing Moodle user account with e-mail address student4@example.com."

  Scenario: Try to bulk enrol a student into the course for which two accounts exist in the system.
    Given the following config values are set as admin:
      | config                 | value |
      | allowaccountssameemail | 1     |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student4 | Student   | 4        | student2@example.com |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      student2@example.com
      """
    And I click on "Execute user enrolment" "button"
    Then I should see "More than one existing Moodle user account with e-mail address student2@example.com found."

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
    And I click on "Execute user enrolment" "button"
    Then I should see "No valid e-mail address was found in the given list."
    And I should see "Please go back and check your input"
    And "Execute user enrolment" "button" should not exist

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
    And I click on "Execute user enrolment" "button"
    Then I should see "Line 2 is empty and will be ignored."
    And I should see "No e-mail address found in line 3 (foo). This line will be ignored."
    And I should see "Manual enrolments"

  Scenario: Bulk enrol students into the course while disrespecting the case of the given e-mail addresses
    Given the following "users" exist:
      | username      | firstname | lastname        | email                            |
      | studentupper1 | Student   | Upper Account 1 | studentUPPERACCOUNT1@example.com |
      | studentupper2 | Student   | Upper Input 2   | studentupperinput2@example.com   |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      studentupperaccount1@example.com
      studentUPPERinput2@example.com
      """
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address                    | First name | Last name       | User enrolment        |
      | studentUPPERACCOUNT1@example.com | Student    | Upper Account 1 | User will be enrolled |
      | studentupperinput2@example.com   | Student    | Upper Input 2   | User will be enrolled |
    And the following should exist in the "localbulkenrol_enrolinfo" table:
      | Enrolment method  | Assigned role |
      | Manual enrolments | Student       |
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "participants" table:
      | Email address                    | First name | Last name       | Roles   |
      | studentUPPERACCOUNT1@example.com | Student    | Upper Account 1 | Student |
      | studentupperinput2@example.com   | Student    | Upper Input 2   | Student |

  Scenario: Bulk unenrol students from the course who are already enrolled with enrolment method self
    Given the following config values are set as admin:
      | config      | value | plugin          |
      | enrolplugin | self  | local_bulkenrol |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student4 | Student   | 4        | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      !  student1@example.com
      !student2@example.com
      !student4@example.com
      """
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment          |
      | student1@example.com | Student    | 1         | User will be unenrolled |
      | student2@example.com | Student    | 2         | User will be unenrolled |
      | student4@example.com | Student    | 4         | User is not enrolled    |
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student3@example.com | Student    | 3         | Student |
    And the following should not exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student1@example.com | Student    | 1         | Student |
      | student2@example.com | Student    | 2         | Student |
      | student4@example.com | Student    | 4         | Student |

  Scenario: Bulk enrol / unenrol students into the course with multiple adresses per line
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student4 | Student   | 4        | student4@example.com |
      | student5 | Student   | 5        | student5@example.com |
      | student6 | Student   | 6        | student6@example.com |
      | student7 | Student   | 7        | student7@example.com |
      | student8 | Student   | 8        | student8@example.com |
      | student9 | Student   | 9        | student9@example.com |
      | studenta | Student   | a        | studenta@example.com |
      | studentb | Student   | b        | studentb@example.com |
      | studentc | Student   | c        | studentc@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
      | student4 | C1     | student |
      | student5 | C1     | student |
      | student6 | C1     | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I set the field "Participants tertiary navigation" to "User bulk enrolment"
    And I set the field "List of e-mail addresses" to multiline:
      """
      !student1@example.com,student2@example.com
      !student3@example.com, student4@example.com
      ! student5@example.com student6@example.com
      student7@example.com,student8@example.com
      student9@example.com, studenta@example.com
      studentb@example.com studentc@example.com
      """
    And I click on "Execute user enrolment" "button"
    Then the following should exist in the "localbulkenrol_enrolusers" table:
      | Email address        | First name | Last name | User enrolment          |
      | student1@example.com | Student    | 1         | User will be unenrolled |
      | student2@example.com | Student    | 2         | User will be unenrolled |
      | student3@example.com | Student    | 3         | User will be unenrolled |
      | student4@example.com | Student    | 4         | User will be unenrolled |
      | student5@example.com | Student    | 5         | User will be unenrolled |
      | student6@example.com | Student    | 6         | User will be unenrolled |
      | student7@example.com | Student    | 7         | User will be enrolled   |
      | student8@example.com | Student    | 8         | User will be enrolled   |
      | student9@example.com | Student    | 9         | User will be enrolled   |
      | studenta@example.com | Student    | a         | User will be enrolled   |
      | studentb@example.com | Student    | b         | User will be enrolled   |
      | studentc@example.com | Student    | c         | User will be enrolled   |
    And I click on "Execute user enrolment" "button"
    Then the following should not exist in the "participants" table:
      | Email address                    | First name | Last name       |
      | student1@example.com             | Student    | 1               |
      | student2@example.com             | Student    | 2               |
      | student3@example.com             | Student    | 3               |
      | student4@example.com             | Student    | 4               |
      | student5@example.com             | Student    | 5               |
      | student6@example.com             | Student    | 6               |
    And the following should exist in the "participants" table:
      | Email address        | First name | Last name | Roles   |
      | student7@example.com | Student    | 7         | Student |
      | student8@example.com | Student    | 8         | Student |
      | student9@example.com | Student    | 9         | Student |
      | studenta@example.com | Student    | a         | Student |
      | studentb@example.com | Student    | b         | Student |
      | studentc@example.com | Student    | c         | Student |
