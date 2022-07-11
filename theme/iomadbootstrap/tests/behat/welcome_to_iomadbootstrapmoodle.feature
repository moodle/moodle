@javascript @theme_iomadbootstrap
Feature: Welcome message on iomadbootstrap
  To be welcome in moodle
  As a User
  I need to see a welcome message on the first page

  Scenario: Login and be welcomed on the homepage
    Given the following config values are set as admin:
      | defaulthomepage | 0 |
    When I log in as "admin"
    Then I should see "Acceptance test site" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"

  Scenario: Login and be welcomed on the dashboard
    Given the following config values are set as admin:
      | defaulthomepage | 1 |
    When I log in as "admin"
    Then I should see "Dashboard" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"

  Scenario: Login and be welcomed on the my courses page
    Given the following config values are set as admin:
      | defaulthomepage | 3 |
    When I log in as "admin"
    Then I should see "My courses" in the "page-header" "region"
    And I should see "Welcome, Admin!" in the "page-header" "region"
    And I reload the page
    And I should not see "Welcome, Admin!" in the "page-header" "region"
