@core @core_my
Feature: Add blocks to my home page
  In order to add more functionality to my home page
  As a user
  I need to add blocks to my home page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"

  Scenario: Add blocks to page
    When I press "Customise this page"
    And I add the "Latest news" block
    Then I should see "Latest news" in the "Latest news" "block"
    And I should see "My latest badges" in the "My latest badges" "block"
    And I should see "Calendar" in the "Calendar" "block"
    And I should see "Upcoming events" in the "Upcoming events" "block"