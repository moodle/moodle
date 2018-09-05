@core @message @javascript
Feature: Search users
  In order to communicate with fellow users
  As a user
  I need to be able to search for them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
      | user3    | User      | 3        | user3@example.com    |

  Scenario: Search for single user
    When I log in as "user1"
    And I follow "Messages" in the user menu
    And I click on "contacts-view" "message_area_action"
    And I set the field "Search for a user or course" to "User 2"
    Then I should see "User 2" in the "search-results-area" "message_area_region"
    And I should not see "User 3" in the "search-results-area" "message_area_region"

  Scenario: Search for multiple user
    When I log in as "user1"
    And I follow "Messages" in the user menu
    And I click on "contacts-view" "message_area_action"
    And I set the field "Search for a user or course" to "User"
    Then I should see "User 2" in the "search-results-area" "message_area_region"
    And I should see "User 3" in the "search-results-area" "message_area_region"
    And I should not see "User 1" in the "search-results-area" "message_area_region"

  Scenario: Search for messages no results
    When I log in as "user1"
    And I follow "Messages" in the user menu
    And I click on "contacts-view" "message_area_action"
    And I set the field "Search for a user or course" to "No User"
    Then I should see "No results" in the "search-results-area" "message_area_region"
