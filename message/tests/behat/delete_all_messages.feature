@core @core_message @javascript
Feature: Delete all messages
  In order to communicate with fellow users
  As a user
  I need to be able to delete all messages

  Scenario: Delete all messages
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
    And I log in as "user2"
    And I send "User 2 to User 1 message 1" message to "User 1" user
    And I send "User 2 to User 1 message 2" message in the message area
    And I send "User 2 to User 1 message 3" message in the message area
    And I log out
    When I log in as "user1"
    And I follow "Messages" in the user menu
    And I click on "start-delete-messages" "message_area_action"
    And I click on "Delete all" "button"
    # Confirm dialogue.
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    # Confirm the interface is immediately updated.
    Then I should not see "User 2" in the "conversations" "message_area_region_content"
    # Confirm the changes are persisted.
    And I reload the page
    And I should not see "User 2" in the "conversations" "message_area_region_content"
