@core @core_message @javascript
Feature: Manage contacts
  In order to communicate with fellow users
  As a user
  I need to be able to add and remove them from my contacts

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
      | user2    | User      | 2        | user2@example.com    |
      | user3    | User      | 3        | user3@example.com    |
    And I log in as "user1"
    And I view the "User 2" contact in the message area
    And I click on "Add contact" "link"
    And I view the "User 3" contact in the message area
    And I click on "Add contact" "link"
    And I log out

  Scenario: Add contact shows in contacts tab
    When I log in as "user1"
    And I follow "Messages" in the user menu
    And I click on "contacts-view" "message_area_action"
    Then I should see "User 2" in the "contacts" "message_area_region_content"
    And I should see "User 3" in the "contacts" "message_area_region_content"

  Scenario: Remove contact
    When I log in as "user1"
    And I view the "User 3" contact in the message area
    And I click on "Remove contact" "link"
    And I reload the page
    And I click on "contacts-view" "message_area_action"
    Then I should see "User 2" in the "contacts" "message_area_region_content"
    And I should not see "User 3" in the "contacts" "message_area_region_content"
