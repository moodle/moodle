@report @report_usersessions
Feature: In a report, admin can see current sessions
  In order see usersession data
  As a admin
  I need to view usersessions report and see if the current session is listed

  Scenario: Check usersessions report shows current session
    Given I log in as "admin"
    When I navigate to "Browser sessions" node in "My profile settings > Activity reports"
    Then I should see "Current session"
