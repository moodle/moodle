@core @core_message @javascript
Feature: Star and unstar conversations
  In order to manage a course group in a course
  As a user
  I need to be able to star and unstar conversations

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
    And the following config values are set as admin:
      | messaging | 1 |

  Scenario: Star a group conversation
    Given I log in as "student1"
    Then I open messaging
    And I open the "Group" conversations list
    And "Group 1" "group_message" should exist
    And I select "Group 1" conversation in messaging
    And I open contact menu
    And I click on "Star" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should see "Group 1" in the "favourites" "group_message_list_area"
    And I open the "Group" conversations list
    And I should not see "Group 1" in the "group-messages" "group_message_list_area"

  Scenario: Unstar a group conversation
    Given I log in as "student1"
    Then I open messaging
    And I open the "Group" conversations list
    And "Group 1" "group_message" should exist
    And I select "Group 1" conversation in messaging
    And I open contact menu
    And I click on "Star" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should see "Group 1" in the "favourites" "group_message_list_area"
    And I select "Group 1" conversation in messaging
    And I open contact menu
    And I click on "Unstar" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should not see "Group 1" in the "favourites" "group_message_list_area"
    And I open the "Group" conversations list
    And I should see "Group 1" in the "group-messages" "group_message_list_area"

  Scenario: Star a private conversation
    Given the following "private messages" exist:
      | user     | contact  | message |
      | student1 | student2 | Hi!     |
    Then I log in as "student1"
    And I open messaging
    And I open the "Private" conversations list
    And "Student 2" "group_message" should exist
    And I select "Student 2" conversation in messaging
    And I open contact menu
    And I click on "Star" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should see "Student 2" in the "favourites" "group_message_list_area"
    And I open the "Private" conversations list
    And I should not see "Student 2" in the "messages" "group_message_list_area"

  Scenario: Unstar a private conversation
    Given the following "private messages" exist:
      | user     | contact  | message |
      | student1 | student2 | Hi!     |
    Given the following "favourite conversations" exist:
      | user     | contact  |
      | student1 | student2 |
    Then I log in as "student1"
    And I open messaging
    And I should see "Student 2" in the "favourites" "group_message_list_area"
    And I select "Student 2" conversation in messaging
    And I open contact menu
    And I click on "Unstar" "link" in the "//div[@data-region='header-container']" "xpath_element"
    And I go back in "view-conversation" message drawer
    And I open the "Starred" conversations list
    And I should not see "Group 1" in the "favourites" "group_message_list_area"
    And I open the "Private" conversations list
    And I should see "Student 2" in the "messages" "group_message_list_area"