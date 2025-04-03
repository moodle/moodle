@core @core_message @javascript
Feature: Unread messages
  In order to know how many unread messages I have
  As a user
  I need to be able to view an unread message

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
      | name      | course | idnumber | enablemessaging |
      | New group | C1     | NG       | 1               |
    And the following "group members" exist:
      | user     | group |
      | student1 | NG |
      | student2 | NG |
    And the following config values are set as admin:
      | messaging        | 1 |
      | messagingminpoll | 1 |

  Scenario: Unread messages for group conversation
    Given I log in as "student1"
    When I open messaging
    And I open the "Group" conversations list
    Then "New group" "core_message > Message" should exist
    And I select "New group" conversation in messaging
    And I send "Hi!" message in the message area
    And I should see "Hi!" in the "New group" "core_message > Message conversation"
    And I should see "##today##%d %B##" in the "New group" "core_message > Message conversation"
    And I log out
    And I log in as "student2"
    And I should see "1" in the "Toggle messaging drawer" "button"
    And I open messaging
    And I should see "1" in the "Group" "core_message > Message tab"
    And "New group" "core_message > Message" should exist
    And I should see "1" in the "New group" "core_message > Message"
    And I select "New group" conversation in messaging
    And I should see "Hi!" in the "New group" "core_message > Message conversation"
    And I should not see "1" in the "Toggle messaging drawer" "button"
    And I should not see "1" in the "Group" "core_message > Message tab"
    And I should not see "1" in the "New group" "core_message > Message"

  Scenario: Unread messages for private conversation
    Given the following "private messages" exist:
      | user     | contact  | message               |
      | student1 | student2 | Hi!                   |
      | student2 | student1 | What do you need?     |
    When I log in as "student1"
    Then I should see "1" in the "Toggle messaging drawer" "button"
    And I open messaging
    And I should see "1" in the "Private" "core_message > Message tab"
    And "Student 2" "core_message > Message" should exist
    And I should see "1" in the "Student 2" "core_message > Message"
    And I select "Student 2" conversation in messaging
    And I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should not see "1" in the "Toggle messaging drawer" "button"
    And I should not see "1" in the "Private" "core_message > Message tab"
    And I should not see "1" in the "Student 2" "core_message > Message"

  Scenario: Unread messages for starred conversation
    Given the following "private messages" exist:
      | user     | contact  | message               |
      | student1 | student2 | Hi!                   |
      | student2 | student1 | What do you need?     |
    And the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    When I log in as "student1"
    Then I should see "1" in the "Toggle messaging drawer" "button"
    And I open messaging
    And I should see "1" in the "Starred" "core_message > Message tab"
    And "Student 2" "core_message > Message" should exist
    And I should see "1" in the "Student 2" "core_message > Message"
    And I select "Student 2" conversation in messaging
    And I should see "Hi!" in the "Student 2" "core_message > Message conversation"
    And I should not see "1" in the "Toggle messaging drawer" "button"
    And I should not see "1" in the "Starred" "core_message > Message tab"
    And I should not see "1" in the "Student 2" "core_message > Message"
