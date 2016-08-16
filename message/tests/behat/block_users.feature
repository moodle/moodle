@core @core_message
Feature: Block users from contacting me
  In order to block other users
  As a user
  I need to prevent specific users to sending me messages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@example.com |
      | user2 | User | Two | two@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | student |
      | user2 | C1 | student |
    And I log in as "user1"
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I click on "Block contact" "link" in the "User Two" "table_row"
    And I log out

  @javascript
  Scenario: Block users display in message navigation
    Given I log in as "user1"
    When I follow "Messages" in the user menu
    Then the "Message navigation:" select box should contain "Blocked users (1)"
    And I set the field "Message navigation:" to "Blocked users (1)"
    And I should see "User Two"

  @javascript
  Scenario: Block users from contacting me
    Given I log in as "user2"
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User One"
    And I press "Search people and messages"
    When I follow "Send message to User One"
    Then I should see "This user has blocked you from sending messages to them"
    And I follow "Picture of User One"
    And I press "Message"
    And I should see "This user has blocked you from sending messages to them"
