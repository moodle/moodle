@core @core_message @javascript
Feature: Message delete conversations
  In order to communicate with fellow users
  As a user
  I need to be able to delete conversations

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | name | shortname |
      | course1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following config values are set as admin:
      | messaging         | 1 |
      | messagingallusers | 1 |
      | messagingminpoll  | 1 |
    And the following "private messages" exist:
      | user     | contact  | message               |
      | student1 | student2 | Hi!                   |
      | student2 | student1 | What do you need?     |

  Scenario: Delete a private conversation
    And I log in as "student2"
    And I open messaging
    And I select "Student 1" conversation in the "messages" conversations list
    And I open contact menu
    And I click on "Delete conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
#   Confirm deletion, so conversation should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-conversation']" "xpath_element"
    And I should not see "Delete"
    And I should not see "Hi!" in the "Student 1" "core_message > Message conversation"
    And I should not see "What do you need?" in the "Student 1" "core_message > Message conversation"
    And I should not see "##today##%d %B##" in the "Student 1" "core_message > Message conversation"
#   Check user is deleting private conversation only for them
    And I log out
    And I log in as "student1"
    And I open messaging
    And I select "Student 2" conversation in the "messages" conversations list
    And I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should see "What do you need?" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "Student 2" "core_message > Message conversation"

  Scenario: Cancel deleting a private conversation
    Given I log in as "student1"
    And I open messaging
    And I select "Student 2" conversation in the "messages" conversations list
    And I open contact menu
    And I click on "Delete conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
#   Cancel deletion, so conversation should be there
    And I should see "Cancel"
    And I click on "//button[@data-action='cancel-confirm']" "xpath_element"
    And I should not see "Cancel"
    And I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "Student 2" "core_message > Message conversation"

  Scenario: Delete a starred conversation
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    And I log in as "student1"
    And I open messaging
    And I select "Student 2" conversation in the "favourites" conversations list
    And I open contact menu
    And I click on "Delete conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
#   Confirm deletion, so conversation should not be there
    And I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-conversation']" "xpath_element"
    And I should not see "Delete"
    And I should not see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should not see "What do you need?" in the "Student 2" "core_message > Message conversation"
    And I should not see "##today##%d %B##" in the "Student 2" "core_message > Message conversation"
#   Check user is deleting private conversation only for them
    And I log out
    And I log in as "student2"
    And I open messaging
    And I select "Student 1" conversation in the "messages" conversations list
    And I should see "Hi!" in the "Student 1" "core_message > Message conversation"
    And I should see "What do you need?" in the "Student 1" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "Student 1" "core_message > Message conversation"

  Scenario: Cancel deleting a starred conversation
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    When I log in as "student1"
    And I open messaging
    And I select "Student 2" conversation in the "favourites" conversations list
    Then I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "Student 2" "core_message > Message conversation"
    And I open contact menu
    And I click on "Delete conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
#   Cancel deletion, so conversation should be there
    And I should see "Cancel"
    And I click on "//button[@data-action='cancel-confirm']" "xpath_element"
    And I should not see "Cancel"
    And I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "Student 2" "core_message > Message conversation"

  Scenario: Check a deleted starred conversation is still starred
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    When I log in as "student1"
    And I open messaging
    And I select "Student 2" conversation in the "favourites" conversations list
    And I open contact menu
    And I click on "Delete conversation" "link" in the "//div[@data-region='header-container']" "xpath_element"
    Then I should see "Delete"
    And I click on "//button[@data-action='confirm-delete-conversation']" "xpath_element"
    And I should not see "Delete"
    And I should not see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I go back in "view-conversation" message drawer
    And I should not see "Student 2" in the "favourites" "core_message > Message list area"
    And the following "private messages" exist:
      | user     | contact  | message       |
      | student1 | student2 | Hi!           |
    And I open messaging
    And I should see "Student 2" in the "favourites" "core_message > Message list area"
