@core @core_message
Feature: Users can search their message history
  In order to read old messages
  As a user
  I need to search in my messages history

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One | one@asd.com |
      | user2 | User | Two | two@asd.com |
    And I log in as "user1"
    When I send "Give me your biscuits" message to "User Two" user
    And I navigate to "Messages" node in "My profile"
    And I set the field "Search people and messages" to "your biscuits"
    And I press "Search people and messages"
    Then I should see "User Two"
    And I click on "context" "link" in the "User Two" "table_row"
    And I should see "Give me your biscuits"

  @javascript
  Scenario: Search message history with Javascript enabled

  Scenario: Search message history with Javascript disabled
