@report @report_usersessions
Feature: In a report, admin can see current sessions
  In order see usersession data
  As a admin
  I need to view usersessions report and see if the current session is listed

  @javascript
  Scenario: Check usersessions report shows current session
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    When I follow "Browser sessions"
    Then I should see "Current session"
