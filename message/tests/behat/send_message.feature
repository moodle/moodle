@core @core_message
Feature: Users can send messages to each other
  In order to communicate with another user
  As a user
  I can send private messages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@example.com |
      | user2 | User | Two | two@example.com |

  @javascript
  Scenario: Using the 'Send message' dialog on one's profile
    Given the following config values are set as admin:
      | forceloginforprofiles | 0 |
    And I log in as "user1"
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I follow "Picture of User Two"
    When I press "Message"
    And I set the field "Message to send" to "Lorem ipsum sa messagus textus"
    And I press "Send message"
    And I am on homepage
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I follow "Send message to User Two"
    Then I should see "Lorem ipsum sa messagus textus"

  @javascript
  Scenario: Using the 'Send message' dialog on one's course profile
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | student |
      | user2 | C1 | student |
    And I log in as "user1"
    And I follow "Course 1"
    And I follow "Participants"
    And I follow "User Two"
    When I press "Message"
    And I set the field "Message to send" to "Lorem ipsum sa messagus textus"
    And I press "Send message"
    And I am on homepage
    And I follow "Messages" in the user menu
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I follow "Send message to User Two"
    Then I should see "Lorem ipsum sa messagus textus"
