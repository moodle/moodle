@mod @mod_customcert
Feature: Being able to correctly display options on the certificate activity edit form

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 |

  Scenario: Edit an activity as an Editing Teacher I can see all custom certificate options
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Custom certificate 1"
    And I navigate to "Settings" in current page administration
    And I should see "Email students"
    And I should see "Email teachers"
    And I should see "Email others"
    And I should see "Allow anyone to verify a certificate"
    And I should see "Required minutes in course"
    And I should see "Set protection"

  @javascript
  Scenario: Create an activity as an Editing Teacher without required capabilities I can't see all custom certificate options
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                         | permission |
      | mod/customcert:manageemailstudents | Prevent    |
      | mod/customcert:manageemailteachers | Prevent    |
      | mod/customcert:manageemailothers   | Prevent    |
      | mod/customcert:manageverifyany     | Prevent    |
      | mod/customcert:managerequiredtime  | Prevent    |
      | mod/customcert:manageprotection    | Prevent    |
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Custom certificate" to section "1"
    And I should not see "Email students"
    And I should not see "Email teachers"
    And I should not see "Email others"
    And I should not see "Allow anyone to verify a certificate"
    And I should not see "Required minutes in course"
    And I should not see "Set protection"

  Scenario: Edit an activity as an Editing Teacher without required capabilities I can't see all custom certificate options
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                         | permission |
      | mod/customcert:manageemailstudents | Prevent    |
      | mod/customcert:manageemailteachers | Prevent    |
      | mod/customcert:manageemailothers   | Prevent    |
      | mod/customcert:manageverifyany     | Prevent    |
      | mod/customcert:managerequiredtime  | Prevent    |
      | mod/customcert:manageprotection    | Prevent    |
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Custom certificate 1"
    And I navigate to "Settings" in current page administration
    And I should not see "Email students"
    And I should not see "Email teachers"
    And I should not see "Email others"
    And I should not see "Allow anyone to verify a certificate"
    And I should not see "Required minutes in course"
    And I should not see "Set protection"

  @javascript
  Scenario: Add an activity using default custom certificate options
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Custom certificate" to section "0"
    And the field "emailstudents" matches value "0"
    And the field "emailteachers" matches value "0"
    And the field "emailothers" matches value ""
    And the field "verifyany" matches value "0"
    And the field "requiredtime" matches value "0"
    And the field "protection_print" matches value "0"
    And the field "protection_modify" matches value "0"
    And the field "protection_copy" matches value "0"

  @javascript
  Scenario: Add an activity using configured custom certificate options
    And the following config values are set as admin:
      | emailstudents     | 1               | customcert |
      | emailteachers     | 1               | customcert |
      | emailothers       | test@moodle.com | customcert |
      | verifyany         | 1               | customcert |
      | requiredtime      | 5               | customcert |
      | protection_print  | 1               | customcert |
      | protection_modify | 1               | customcert |
      | protection_copy   | 1               | customcert |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Custom certificate" to section "1"
    And the field "emailstudents" matches value "1"
    And the field "emailteachers" matches value "1"
    And the field "emailothers" matches value "test@moodle.com"
    And the field "verifyany" matches value "1"
    And the field "requiredtime" matches value "5"
    And the field "protection_print" matches value "1"
    And the field "protection_modify" matches value "1"
    And the field "protection_copy" matches value "1"
