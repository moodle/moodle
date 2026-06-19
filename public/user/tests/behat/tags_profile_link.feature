@core @core_user @core_tag
Feature: Tags link in user profile Miscellaneous section
  In order to navigate to site-wide tag search
  As a logged-in user
  I need to see a Tags link in the Miscellaneous section of my profile

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |

  Scenario: Tags link is present in the user profile Miscellaneous section for admin
    Given I log in as "admin"
    When I follow "Profile" in the user menu
    Then "Tags" "link" should exist

  Scenario: Tags link is present in the user profile Miscellaneous section for a regular user
    Given I log in as "user1"
    When I follow "Profile" in the user menu
    Then "Tags" "link" should exist

  Scenario: Tags link navigates to the tag search page
    Given I log in as "user1"
    When I follow "Profile" in the user menu
    And I follow "Tags"
    Then I should see "Search tags"

  Scenario: Tags link is not visible when tags are disabled
    Given the following config values are set as admin:
      | usetags | 0 |
    And I log in as "user1"
    When I follow "Profile" in the user menu
    Then "Tags" "link" should not exist

  Scenario: Tags link is present when viewing another user's profile
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
    And I log in as "user1"
    When I am on the "user2" "user > profile" page
    Then "Tags" "link" should exist
