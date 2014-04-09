@core @core_message
Feature: Block users from contacting me
  In order to block other users
  As a user
  I need to prevent specific users to sending me messages

  @javascript
  Scenario: Block users from contacting me with Javascript enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@asd.com |
      | user2 | User | Two | two@asd.com |
    And I log in as "user1"
    And I expand "My profile" node
    And I follow "Messages"
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    When I click on "Block contact" "link" in the "User Two" "table_row"
    Then the "Message navigation:" select box should contain "Blocked users (1)"
    And I set the field "Message navigation:" to "Blocked users (1)"
    And I should see "User Two"
    And I log out
    And I log in as "user2"
    And I expand "My profile" node
    And I follow "Messages"
    And I set the field "Search people and messages" to "User One"
    And I press "Search people and messages"
    And I follow "Send message to User One"
    And I should see "This user has blocked you from sending messages to them"
