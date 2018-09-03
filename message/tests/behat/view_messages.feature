@core @message @javascript
Feature: View messages
  In order to communicate with fellow users
  As a user
  I need to be able to view my messages

  Scenario: View messages from multiple users
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
      | user3    | User      | 3        | user3@example.com    |
    And I log in as "user2"
    And I send "User 2 to User 1" message to "User 1" user
    And I log out
    And I log in as "user3"
    And I send "User 3 to User 1" message to "User 1" user
    And I log out
    And I log in as "user1"
    When I follow "Messages" in the user menu
    Then I should see "User 3 to User 1" in the "conversations" "message_area_region_content"
    And I click on "User 2" "text" in the "conversations" "message_area_region_content"
    And I should see "User 2 to User 1" in the "conversations" "message_area_region_content"
