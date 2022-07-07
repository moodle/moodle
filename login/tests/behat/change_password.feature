@core
Feature: Change password
  In order to ensure the password change works as expected
  As a user
  I need to test all the way to change my password

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | testuser | Test | User | moodle@example.com |

  Scenario: A user can change his password manually
    Given I am on site homepage
    And I log in as "testuser"
    And I follow "Preferences" in the user menu
    When I follow "Change password"
    And I set the field "Current password" to "testuser"
    And I set the field "New password" to "NewPassword1*"
    And I set the field "New password (again)" to "NewPassword1*"
    And I click on "Save changes" "button"
    Then I should see "Password has been changed"
    And I click on "Continue" "button"
    And I should see "Preferences" in the "region-main" "region"
    And I log out
    And I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "NewPassword1*"
    And I press "Log in"
    Then I should see "You are logged in as Test User" in the "page-footer" "region"

  Scenario: A user with expired password must change it when log in directly and then be redirected to the home page
    Given I force a password change for user "testuser"
    And I log in as "testuser"
    And I should see "You must change your password to proceed"
    When I set the field "Current password" to "testuser"
    And I set the field "New password" to "NewPassword1*"
    And I set the field "New password (again)" to "NewPassword1*"
    And I click on "Save changes" "button"
    Then I should see "Password has been changed"
    And I click on "Continue" "button"
    And I am on site homepage

  @javascript
  Scenario: A user with expired password trying to visit a required login page must change and it and then be redirected to this page
    Given I force a password change for user "testuser"
    And the following "courses" exist:
      | fullname | shortname | visible |
      | Course 1 | c1 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | testuser | c1 | student |
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "testuser"
    And I press "Log in"
    Then I should see "You must change your password to proceed"
    And I set the field "Current password" to "testuser"
    And I set the field "New password" to "NewPassword1*"
    And I set the field "New password (again)" to "NewPassword1*"
    And I click on "Save changes" "button"
    And I should see "Password has been changed"
    And I click on "Continue" "button"
    And I am on site homepage
    And I should see "Course 1"
