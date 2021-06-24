@mod @mod_customcert
Feature: Being able to view the certificates you have been issued
  In order to ensure that a user can view the certificates they have been issued
  As a student
  I need to view the certificates I have been issued

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And the following "activities" exist:
      | activity   | name                 | intro                      | course | idnumber    |
      | customcert | Custom certificate 1 | Custom certificate 1 intro | C1     | customcert1 |
      | customcert | Custom certificate 2 | Custom certificate 2 intro | C2     | customcert2 |

  Scenario: View your issued certificates on the my certificates page
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Custom certificate 1"
    And I press "View certificate"
    And I follow "Profile" in the user menu
    And I follow "My certificates"
    And I should see "Custom certificate 1"
    And I should not see "Custom certificate 2"
    And I am on "Course 2" course homepage
    And I follow "Custom certificate 2"
    And I press "View certificate"
    And I follow "Profile" in the user menu
    And I follow "My certificates"
    And I should see "Custom certificate 1"
    And I should see "Custom certificate 2"
