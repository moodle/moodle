@core @core_message
Feature: Users can send messages to each other
  In order to communicate with another user
  As a user
  I can send private messages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@asd.com |
      | user2 | User | Two | two@asd.com |

  @javascript
  Scenario: Using the 'Send message' dialog on one's profile
    Given I log in as "admin"
    And I set the following administration settings values:
      | forceloginforprofiles | 0 |
    And I log out
    And I log in as "user1"
    And I navigate to "Messages" node in "My profile"
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I follow "Picture of User Two"
    When I follow "Send a message"
    And I set the field "Message to send" to "Lorem ipsum sa messagus textus"
    And I press "Send message"
    And I am on homepage
    And I navigate to "Messages" node in "My profile"
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
    When I follow "Send a message"
    And I set the field "Message to send" to "Lorem ipsum sa messagus textus"
    And I press "Send message"
    And I am on homepage
    And I navigate to "Messages" node in "My profile"
    And I set the field "Search people and messages" to "User Two"
    And I press "Search people and messages"
    And I follow "Send message to User Two"
    Then I should see "Lorem ipsum sa messagus textus"
