@core @core_user
Feature: Add blocks to my profile page
  In order to add more functionality to my profile page
  As a user
  I need to add blocks to my profile page

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

  Scenario: Add blocks to page
    When I turn editing mode on
    And I add the "Latest announcements" block
    Then I should see "Latest announcements"
