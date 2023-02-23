@core @core_message @javascript
Feature: Mute and unmute conversations
  In order to manage my conversations
  As a user
  I need to be able to mute and unmute conversations

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
      | user     | group | course |
      | student1 | G1    | C1     |
      | student2 | G1    | C1     |
    And the following config values are set as admin:
      | messaging | 1 |
    And the following "private messages" exist:
      | user     | contact  | message |
      | student1 | student2 | Hi!     |

  Scenario: Mute a group conversation
    Given I log in as "student1"
    When I open messaging
    And I open the "Group" conversations list
    Then "Group 1" "core_message > Message" should exist
    And "muted" "icon_container" in the "Group 1" "core_message > Message" should not be visible
    And I select "Group 1" conversation in messaging
    And "muted" "icon_container" in the "Group 1" "core_message > Message header" should not be visible
    And I open contact menu
    And I click on "Mute" "link" in the "conversation-actions-menu" "region"
    And "muted" "icon_container" in the "Group 1" "core_message > Message header" should be visible
    And I go back in "view-conversation" message drawer
    And "muted" "icon_container" in the "Group 1" "core_message > Message" should be visible

  Scenario: Mute a private conversation
    When I log in as "student1"
    And I open messaging
    Then I should see "Private"
    And I open the "Private" conversations list
    And I should see "Student 2"
    And "muted" "icon_container" in the "Student 2" "core_message > Message" should not be visible
    And I select "Student 2" conversation in messaging
    And "muted" "icon_container" in the "[data-action='view-contact']" "css_element" should not be visible
    And I open contact menu
    And I click on "Mute" "link" in the "conversation-actions-menu" "region"
    And "muted" "icon_container" in the "[data-action='view-contact']" "css_element" should be visible
    And I go back in "view-conversation" message drawer
    And "muted" "icon_container" in the "Student 2" "core_message > Message" should be visible

  Scenario: Unmute a group conversation
    Given the following "muted group conversations" exist:
      | user     | group | course |
      | student1 | G1    | C1     |
    When I log in as "student1"
    And I open messaging
    And I open the "Group" conversations list
    Then "Group 1" "core_message > Message" should exist
    And "muted" "icon_container" in the "Group 1" "core_message > Message" should be visible
    And I select "Group 1" conversation in messaging
    And "muted" "icon_container" in the "Group 1" "core_message > Message header" should be visible
    And I open contact menu
    And I click on "Unmute" "link" in the "conversation-actions-menu" "region"
    And "muted" "icon_container" in the "Group 1" "core_message > Message header" should not be visible
    And I go back in "view-conversation" message drawer
    And "muted" "icon_container" in the "Group 1" "core_message > Message" should not be visible

  Scenario: Unmute a private conversation
    Given the following "muted private conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    When I log in as "student1"
    And I open messaging
    Then I should see "Private"
    And I open the "Private" conversations list
    And I should see "Student 2"
    And "muted" "icon_container" in the "Student 2" "core_message > Message" should be visible
    And I select "Student 2" conversation in messaging
    And "muted" "icon_container" in the "[data-action='view-contact']" "css_element" should be visible
    And I open contact menu
    And I click on "Unmute" "link" in the "conversation-actions-menu" "region"
    And "muted" "icon_container" in the "[data-action='view-contact']" "css_element" should not be visible
    And I go back in "view-conversation" message drawer
    And "muted" "icon_container" in the "Student 2" "core_message > Message" should not be visible
