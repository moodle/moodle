@core
Feature: Enable dashboard setting
  In order to hide/show dashboard in navigation
  As an administrator
  I can enable or disable it

  Scenario: Hide setting when dashboard is disabled
    Given the following config values are set as admin:
      | enabledashboard | 0 |
# 2 = User preference.
      | defaulthomepage | 2 |
    When I log in as "admin"
    And I navigate to "Appearance > Navigation" in site administration
    Then the field "Enable Dashboard" matches value "0"
    And I should not see "Allow guest access to Dashboard"
    And I should not see "Dashboard" in the "Start page for users" "select"
    And I follow "Appearance"
    And I should not see "Default Dashboard page"
    And I follow "Preferences" in the user menu
    And I follow "Start page"
    And I should not see "Dashboard" in the "Start page" "select"
