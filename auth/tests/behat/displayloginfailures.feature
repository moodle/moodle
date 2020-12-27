@core @core_auth
Feature: Test the 'showlogfailures' feature works.
  In order to see my recent login failures when logging in
  As a user
  I need to have at least one failed login attempt and then log in

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |
    And the following config values are set as admin:
      | displayloginfailures | 1 |

  # Given the user has at least one failed login attempt, when they login, then they should see both header and footer notices.
  Scenario: Check that 'displayloginfailures' works without javascript for teachers.
    # Simulate a log in failure for the teacher.
    Given I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "wrongpass"
    And I press "Log in"
    And I should see "Invalid login, please try again"
    # Now, log in with the correct credentials.
    When I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I press "Log in"
    # Confirm the notices are displayed.
    Then I should see "1 failed logins since your last login" in the ".navbar" "css_element"
    And I should see "1 failed logins since your last login" in the "page-footer" "region"
    # Confirm the notices disappear when navigating to another page.
    And I am on homepage
    And I should not see "1 failed logins since your last login" in the ".navbar" "css_element"
    And I should not see "1 failed logins since your last login" in the "page-footer" "region"

  # Given the user has at least one failed login attempt, when they login, then they should see both header and footer notices.
  Scenario: Check that 'displayloginfailures' works without javascript for admins.
    # Simulate a log in failure for the teacher.
    Given I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "admin"
    And I set the field "Password" to "wrongpass"
    And I press "Log in"
    And I should see "Invalid login, please try again"
    # Now, log in with the correct credentials.
    When I set the field "Username" to "admin"
    And I set the field "Password" to "admin"
    And I press "Log in"
    # Confirm the notices are displayed.
    Then I should see "1 failed logins since your last login" in the ".navbar" "css_element"
    And I should see "1 failed logins since your last login (Logs)" in the "page-footer" "region"
    # Confirm that the link works and that the notices disappear when navigating to another page.
    And I click on "Logs" "link" in the "page-footer" "region"
    And I should see "User login failed" in the "table.reportlog" "css_element"
    And I should not see "1 failed logins since your last login" in the ".navbar" "css_element"
    And I should not see "1 failed logins since your last login (Logs)" in the "page-footer" "region"
