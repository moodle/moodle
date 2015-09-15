@core @core_message
Feature: Recent conversations contains my recent conversations
  In order to view my recent conversations
  As a user
  I have the option to filter messages by recent conversations

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email  |
      | user1 | User | One   | one@example.com   |
      | user2 | User | Two   | two@example.com   |
      | user3 | User | Three | three@example.com |

  Scenario: View that I don't have recent conversations
    Given I log in as "user1"
    And I follow "Messages" in the user menu
    When I select "Recent conversations" from the "Message navigation:" singleselect
    Then I should not see "User Two"
    And I should not see "User Three"

  Scenario: View my recent conversations
    Given I log in as "user1"
    And I send "Message from user1 to user2" message to "User Two" user
    And I send "Message from user1 to user3" message to "User Three" user
    And I follow "Messages" in the user menu
    When I select "Recent conversations" from the "Message navigation:" singleselect
    Then I should see "User Two"
    And I should see "User Three"
    And I should see "Message from user1 to user2"
    And I should see "Message from user1 to user3"
    And I log out
    And I log in as "user2"
    And I follow "Messages" in the user menu
    And I select "Recent conversations" from the "Message navigation:" singleselect
    And I should see "Message from user1 to user2"
    And I should not see "Message from user1 to user3"
