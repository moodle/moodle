@core @core_my
Feature: Welcome message
  In order to welcome new or existing user
  As a user
  I will see welcome message when I log into moodle

  Scenario: Log in and being redirected to course page
    Given the following "users" exist:
      | username | password | firstname | lastname | email            |
      | wf       | test     | Fei       | Wang     | fei@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Math 101 | M1O1      |
    When I am on "Math 101" course homepage
    And I should see "You are not logged in" in the "page-footer" "region"
    And I set the field "Username" to "wf"
    And I set the field "Password" to "test"
    And I press "Log in"
    And I should see "Math 101" in the "page-header" "region"
    And I should not see "Welcome, Fei!" in the "page-header" "region"
    And I follow "Dashboard"
    Then I should see "Welcome, Fei!" in the "page-header" "region"

  @javascript
  Scenario: Log in and being redirected to default home page
    When I log in as "admin"
    And I should see "You are logged in as Admin User" in the "page-footer" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I log out
    And I should see "You are not logged in" in the "page-footer" "region"
    And I log in as "admin"
    Then I should see "Hi, Admin!" in the "page-header" "region"

  @accessibility @javascript
  Scenario Outline: The start page must meet accessibility standards when the welcome message is displayed
    Given the following config values are set as admin:
      | defaulthomepage | <defaulthomepage> |
    When I log in as "admin"
    Then I should see "Welcome, Admin!" in the "page-header" "region"
    And the page should meet accessibility standards

    Examples:
      | defaulthomepage |
      # Home.
      | 0               |
      # Dashboard.
      | 1               |
      # My courses.
      | 2               |
