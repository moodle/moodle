@core @core_user
Feature: Reset my profile page to default
  In order to remove customisations from my profile page
  As a user
  I need to reset my profile page

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
    And I log in as "admin"
    And I follow "View profile"

  Scenario: Add blocks to page and reset
    When I press "Customise this page"
    And I add the "Latest news" block
    And I press "Reset page to default"
    Then I should not see "Latest news"
    And I should not see "Reset page to default"