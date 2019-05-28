@core @core_message @javascript
Feature: Self conversation
  In order to have self-conversations
  As a user
  I need to be able to send messages to myself and read them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following config values are set as admin:
      | messaging        | 1 |
      | messagingminpoll | 1 |

  Scenario: Self conversation exists
    Given I log in as "student1"
    When I open messaging
    Then "Student 1" "group_message" should exist
    And I select "Student" conversation in messaging
    And I should see "Personal space"

  Scenario: Self conversation can be unstarred
    Given I log in as "student1"
    When I open messaging
    Then "Student 1" "group_message" should exist
    And I select "Student" conversation in messaging
    And I open contact menu
    And I click on "Unstar" "link" in the "Student 1" "group_message_header"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should not see "Student 1" in the "favourites" "group_message_list_area"
    And I open the "Private" conversations list
    And I should see "Student 1" in the "messages" "group_message_list_area"

  Scenario: Self conversation can be deleted
    Given I log in as "student1"
    When I open messaging
    Then "Student 1" "group_message" should exist
    And I select "Student 1" conversation in messaging
    And I open contact menu
    And I click on "Delete conversation" "link" in the "Student 1" "group_message_header"
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-conversation']" "xpath_element"
    And I should not see "Delete"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should not see "Student 1" in the "favourites" "group_message_list_area"
    And I open the "Private" conversations list
    And I should not see "Student 1" in the "messages" "group_message_list_area"

  Scenario: Send a message to a self-conversation via message drawer
    Given I log in as "student1"
    When I open messaging
    Then "Student 1" "group_message" should exist
    And I select "Student 1" conversation in messaging
    And I send "Hi!" message in the message area
    And I should see "Hi!" in the "Student 1" "group_message_conversation"
    And I should see "##today##j F##" in the "Student 1" "group_message_conversation"

  Scenario: Send a message to a self-conversation via user profile
    Given I log in as "student1"
    When I follow "Profile" in the user menu
    Then I should see "Message"
    And I click on "Message" "icon"
    And I send "Hi!" message in the message area
    And I should see "Hi!" in the "Student 1" "group_message_conversation"
    And I should see "##today##j F##" in the "Student 1" "group_message_conversation"
