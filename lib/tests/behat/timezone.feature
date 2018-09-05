@core
Feature: View timezone defaults
  In order to run all other behat tests
  As an admin
  I need to verify the default timezone is Australia/Perth

  Scenario: Admin sees default timezone Australia/Perth
    When I log in as "admin"
    And I navigate to "Location settings" node in "Site administration > Location"
    Then I should see "Default: Australia/Perth"
    And the field "Default timezone" matches value "Australia/Perth"
