@mod @mod_customcert
Feature: Being able to view the certificates that have been issued
  In order to ensure that a user can view the certificates that have been issued
  As a teacher
  I need to view the certificates that have been issued

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 |
    And the following config values are set as admin:
      | showuseridentity | username,email |

  Scenario: View the issued certificates
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I press "View certificate"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I press "View certificate"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I should see "Student 1"
    And I should see "Student 2"
    And I should see "Email address"
    And I should see "Username"
    And "Student 1" row "Email address" column of "generaltable" table should contain "student1@example.com"
    And "Student 1" row "Username" column of "generaltable" table should contain "student1"
    And "Student 2" row "Email address" column of "generaltable" table should contain "student2@example.com"
    And "Student 2" row "Username" column of "generaltable" table should contain "student2"

  Scenario: Delete an issued certificate
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I press "View certificate"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I press "View certificate"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I should see "Student 1"
    And I should see "Student 2"
    And I click on ".delete-icon" "css_element" in the "Student 2" "table_row"
    And I press "Cancel"
    And I should see "Student 1"
    And I should see "Student 2"
    And I click on ".delete-icon" "css_element" in the "Student 2" "table_row"
    And I press "Continue"
    And I should see "Student 1"
    And I should not see "Student 2"
