@core @core_my
Feature: Reset dashboard page to default
  In order to remove customisations from dashboard page
  As a user
  I need to reset dashboard page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "student1"
    And I click on "Dashboard" "link" in the "Navigation" "block"

  Scenario: Add blocks to page and reset
    When I press "Customise this page"
    And I add the "Latest announcements" block
    And I add the "Comments" block
    And I press "Reset page to default"
    Then I should not see "Latest announcements"
    And I should see "Latest badges"
    And I should see "Calendar"
    And I should see "Upcoming events"
    And I should not see "Comments"
    And I should not see "Reset page to default"
