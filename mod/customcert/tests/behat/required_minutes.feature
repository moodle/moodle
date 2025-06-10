@mod @mod_customcert
Feature: Being able to set the required minutes in a course before viewing the certificate
  In order to ensure the required minutes in a course setting works as expected
  As a teacher
  I need to ensure students can not view a certificate until the required minutes have passed

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    | requiredtime |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 | 1            |

  Scenario: Check the user can not access the certificate before the required time
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I should see "You must spend at least a minimum of"
    And I should not see "View certificate"
    And I press "Continue"
    And I should see "Custom certificate 1"

  Scenario: Check the user can access the certificate after the required time
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait "60" seconds
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I should not see "You must spend at least a minimum of"
    And I should see "View certificate"
