@javascript @theme_boost
Feature: Welcome message on boost
  To be welcome in moodle
  As a User
  I need to see a welcome message on the first page

  @accessibility
  Scenario: Login and be welcomed on the homepage
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    When I log in as "admin"
    Then I should see "Acceptance test site" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"
    And I should see "Acceptance test site" in the "page-header" "region"
    And the page should meet accessibility standards

  @accessibility
  Scenario: Login and be welcomed on the dashboard
    Given the following config values are set as admin:
      | defaulthomepage | 1 |
    When I log in as "admin"
    Then I should see "Dashboard" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"
    And I should see "Dashboard" in the "page-header" "region"
    And the page should meet accessibility standards with "best-practice" extra tests

  @accessibility
  Scenario: Login and be welcomed on the my courses page
    Given the following config values are set as admin:
      | defaulthomepage | 3 |
    When I log in as "admin"
    Then I should see "My courses" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"
    And I should see "My courses" in the "page-header" "region"
    And the page should meet accessibility standards
