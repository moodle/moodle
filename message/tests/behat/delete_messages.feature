@core @core_message @javascript
Feature: Delete messages from conversations
  In order to manage a course group in a course
  As a user
  I need to be able to delete messages from conversations

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1 |
      | student2 | G1 |
    And the following "group messages" exist:
      | user     | group  | message                   |
      | student1 | G1     | Hi!                       |
      | student2 | G1     | How are you?              |
      | student1 | G1     | Can somebody help me?     |
    And the following "private messages" exist:
      | user     | contact  | message       |
      | student1 | student2 | Hi!           |
      | student2 | student1 | Hello!        |
      | student1 | student2 | Are you free? |
    And the following config values are set as admin:
      | messaging        | 1 |
      | messagingminpoll | 1 |

  Scenario: Delete a message sent by the user from a group conversation
    Given I log in as "student1"
    And I open messaging
    And "Group 1" "core_message > Message" should exist
    And I select "Group 1" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I click on "How are you?" "core_message > Message content"
    And I click on "Can somebody help me?" "core_message > Message content"
    And I should see "3" in the "[data-region='message-selected-court']" "css_element"
#   Clicking to unselect
    And I click on "How are you?" "core_message > Message content"
    And I click on "Can somebody help me?" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should see "##today##j F##" in the "Group 1" "core_message > Message conversation"
    And I should see "How are you?" in the "Group 1" "core_message > Message conversation"
    And I should see "Can somebody help me?" in the "Group 1" "core_message > Message conversation"
    And I should not see "Messages selected"

  Scenario: Delete two messages from a group conversation; one sent by another user.
    Given I log in as "student1"
    And I open messaging
    And "Group 1" "core_message > Message" should exist
    And I select "Group 1" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And I click on "How are you?" "core_message > Message content"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should see "##today##j F##" in the "Group 1" "core_message > Message conversation"
    And I should not see "How are you?" in the "Group 1" "core_message > Message conversation"
    And I should see "Can somebody help me?" in the "Group 1" "core_message > Message conversation"
    And I should not see "Messages selected"
#   Check messages were not deleted for other users
    And I log out
    And I log in as "student2"
    And I open messaging
    And I select "Group 1" conversation in messaging
    And I should see "Hi!"
    And I should see "How are you?"
    And I should see "Can somebody help me?"

  Scenario: Cancel deleting two messages from a group conversation
    Given I log in as "student1"
    And I open messaging
    And "Group 1" "core_message > Message" should exist
    And I select "Group 1" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I click on "How are you?" "core_message > Message content"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Canceling deletion, so messages should be there
    And I should see "Cancel"
    And I click on "//button[@data-action='cancel-confirm']" "xpath_element"
    Then I should not see "Cancel"
    And I should see "Hi!"
    And I should see "How are you?" in the "Group 1" "core_message > Message conversation"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"

  Scenario: Delete a message sent by the user from a private conversation
    Given I log in as "student1"
    And I open messaging
    And I should see "Private"
    And I open the "Private" conversations list
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should see "##today##j F##" in the "Student 2" "core_message > Message conversation"
    And I should see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should see "Are you free?" in the "Student 2" "core_message > Message conversation"
    And I should not see "Messages selected"

  Scenario: Delete two messages from a private conversation; one sent by another user
    Given I log in as "student1"
    And I open messaging
    And I should see "Private"
    And I open the "Private" conversations list
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And I click on "Hello!" "core_message > Message content"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should not see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##j F##" in the "Student 2" "core_message > Message conversation"
    And I should see "Are you free?" in the "Student 2" "core_message > Message conversation"
    And I should not see "Messages selected"
#   Check messages were not deleted for the other user
    And I log out
    And I log in as "student2"
    And I open messaging
    And I open the "Private" conversations list
    And I select "Student 1" conversation in messaging
    And I should see "Hi!"
    And I should see "Hello!"
    And I should see "Are you free?"

  Scenario: Cancel deleting two messages from a private conversation
    Given I log in as "student1"
    And I open messaging
    And I should see "Private"
    And I open the "Private" conversations list
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I click on "Hello!" "core_message > Message content"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Canceling deletion, so messages should be there
    And I should see "Cancel"
    And I click on "//button[@data-action='cancel-confirm']" "xpath_element"
    Then I should not see "Cancel"
    And I should see "Hi!"
    And I should see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"

  Scenario: Delete a message sent by the user from a favorite conversation
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    And I log in as "student1"
    And I open messaging
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should see "##today##j F##" in the "Student 2" "core_message > Message conversation"
    And I should see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should not see "Messages selected"

  Scenario: Delete two messages from a favourite conversation; one sent by another user
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    And I log in as "student1"
    And I open messaging
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I should see "1" in the "[data-region='message-selected-court']" "css_element"
    And I click on "Hello!" "core_message > Message content"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Deleting, so messages should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    Then I should not see "Delete"
    And I should not see "Hi!"
    And I should not see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##j F##" in the "Student 2" "core_message > Message conversation"
    And I should see "Are you free?" in the "Student 2" "core_message > Message conversation"
    And I should not see "Messages selected"

  Scenario: Cancel deleting two messages from a favourite conversation
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    And I log in as "student1"
    And I open messaging
    And I should see "Student 2"
    And I select "Student 2" conversation in messaging
    And I click on "Hi!" "core_message > Message content"
    And I click on "Hello!" "core_message > Message content"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
#   Canceling deletion, so messages should be there
    And I should see "Cancel"
    And I click on "//button[@data-action='cancel-confirm']" "xpath_element"
    Then I should not see "Cancel"
    And I should see "Hi!"
    And I should see "Hello!" in the "Student 2" "core_message > Message conversation"
    And I should see "2" in the "[data-region='message-selected-court']" "css_element"

  Scenario: Check an empty favourite conversation is still favourite
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    And I log in as "student1"
    And I open messaging
    And I should see "Student 2"
    And I select "Student 2" conversation in the "favourites" conversations list
    And I click on "Hi!" "core_message > Message content"
    And I click on "Hello!" "core_message > Message content"
    And I click on "Are you free?" "core_message > Message content"
    And "Delete selected messages" "button" should exist
    When I click on "Delete selected messages" "button"
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-selected-messages']" "xpath_element"
    And I go back in "view-conversation" message drawer
    Then I should not see "Student 2" in the "//*[@data-region='message-drawer']//div[@data-region='view-overview-favourites']" "xpath_element"
    And I send "Hi!" message to "Student 2" user
    And I go back in "view-conversation" message drawer
    And I go back in "view-search" message drawer
    And I open the "Starred" conversations list
    And I should see "Student 2" in the "//*[@data-region='message-drawer']//div[@data-region='view-overview-favourites']" "xpath_element"
