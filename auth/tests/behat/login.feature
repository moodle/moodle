@core @core_auth
Feature: Authentication
  In order to validate my credentials in the system
  As a user
  I need to log into the system

  Scenario: Log in with the predefined admin user with Javascript disabled
    Given I log in as "admin"
    Then I should see "You are logged in as Admin User" in the "page-footer" "region"

  @javascript
  Scenario: Log in with the predefined admin user with Javascript enabled
    Given I log in as "admin"
    Then I should see "You are logged in as Admin User" in the "page-footer" "region"

  Scenario: Log in as an existing admin user filling the form
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    And I am on homepage
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "testuser"
    And I press "Log in"
    Then I should see "You are logged in as" in the "page-footer" "region"

  Scenario: Log in as an unexisting user filling the form
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    And I am on homepage
    When I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "unexisting"
    And I press "Log in"
    Then I should see "Invalid login, please try again"

  Scenario: Log out
    Given I log in as "admin"
    When I log out
    Then I should see "You are not logged in" in the "page-footer" "region"

  Scenario: Admin can login as another user and logout
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    Given I log in as "admin"
    And I navigate to "Participants" node in "Site pages"
    And I follow "Test User"
    And I follow "Log in as"
    Then I should see "You are logged in as Test User" in the "#region-main" "css_element"
    And I press "Continue"
    And I should see "[Admin User] You are logged in as Test User (Log out)" in the "page-footer" "region"
    And I click on "Admin User" "link" in the "page-footer" "region"
    And I should see "You are not logged in"

  Scenario: Admin can login as another user and return to original session
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    And I log in as "admin"
    And I set the following administration settings values:
      | simplereturnloginas | 1 |
    And I press "Save changes"
    When I navigate to "Participants" node in "Site pages"
    And I follow "Test User"
    And I follow "Log in as"
    Then I should see "You are logged in as Test User" in the "#region-main" "css_element"
    And I press "Continue"
    And I should see "[Admin User] You are logged in as Test User (Log out)" in the "page-footer" "region"
    And I click on "Admin User" "link" in the "page-footer" "region"
    And I should see "You have successfully returned to your session as Admin User" in the "#region-main" "css_element"
    And I press "Continue"
    And I should see "You are logged in as Admin User (Log out)" in the "page-footer" "region"
    And I should not see "Test User"
