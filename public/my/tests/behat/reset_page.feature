@core @core_my
Feature: Reset dashboard page to default
  In order to remove customisations from dashboard page
  As a user
  I need to reset dashboard page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |

  @javascript
  Scenario: Add blocks to page and reset
    When I log in as "student1"
    And I turn editing mode on
    And I add the "Latest announcements" block
    And I press "Reset page to default"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then "Latest announcements" "block" should not exist
    And "Timeline" "block" should exist
    And "Calendar" "block" should exist
    And I should not see "Reset page to default"
