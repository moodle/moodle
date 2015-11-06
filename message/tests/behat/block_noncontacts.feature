@core @core_message
Feature: Block non contacts from contacting me
  In order to reduce unsolicited messages
  As a user
  I need to prevent non-contacts from sending me messages

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
    And I follow "Preferences" in the user menu
    And I follow "Messaging"
    And I set the field "blocknoncontacts" to "1"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Block non-contacts warning on messages page
    Given I log in as "user1"
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    When I follow "Send message to User Two"
    Then I should see "User Two will not be able to reply as you have blocked non-contacts"

  @javascript
  Scenario: Non-contact can't send message
    Given I log in as "user2"
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User One"
    And I press "Search people and messages"
    When I follow "Send message to User One"
    Then I should see "User One only accepts messages from their contacts."
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Participants"
    And I follow "User One"
    And I press "Message"
    And I should see "User One only accepts messages from their contacts."
