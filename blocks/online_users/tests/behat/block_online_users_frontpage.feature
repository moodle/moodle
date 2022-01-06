@block @block_online_users
Feature: The online users block allow you to see who is currently online on frontpage
  There should be some commonality for the users to show up
  In order to enable the online users block on the frontpage
  As an admin
  I can add the online users block to the frontpage

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | student2 | C1 | student        |

  Scenario: View the online users block on the front page and see myself
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    When I add the "Online users" block
    Then I should see "Admin User" in the "Online users" "block"
    And I should see "1 online user" in the "Online users" "block"

  Scenario: View the online users block on the front page as a logged in user
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    And I add the "Online users" block
    And I log out
    And I log in as "student2"
    And I log out
    When I log in as "student1"
    And I am on site homepage
    Then I should not see "Admin User" in the "Online users" "block"
    And I should see "Other users (1)" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should see "3 online users" in the "Online users" "block"

  Scenario: View the online users block on the front page as a guest
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    And I add the "Online users" block
    And I log out
    And I log in as "student2"
    And I log out
    And I log in as "student1"
    And I log out
    When I log in as "guest"
    And I am on site homepage
    Then I should not see "Admin User" in the "Online users" "block"
    And I should not see "Student 1" in the "Online users" "block"
    And I should not see "Student 2" in the "Online users" "block"
    And I should see "3 online users" in the "Online users" "block"

  @javascript
  Scenario: Hide/show user's online status from/to other users in the online users block on front page
    Given the following config values are set as admin:
      | block_online_users_onlinestatushiding | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    And I add the "Online users" block
    And I log out
    When I log in as "student1"
    And I am on site homepage
    Then "Hide" "icon" should exist in the "#change-user-visibility" "css_element"
    When I click on "#change-user-visibility" "css_element"
    And I wait "1" seconds
    Then "Show" "icon" should exist in the "#change-user-visibility" "css_element"
    And I log out
    When I log in as "student2"
    And I am on site homepage
    Then I should see "2 online user" in the "Online users" "block"
    And I should not see "Admin" in the "Online users" "block"
    And I should see "Other users (1)" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should not see "Student 1" in the "Online users" "block"
    And I log out
    When I log in as "student1"
    And I am on site homepage
    Then "Show" "icon" should exist in the "#change-user-visibility" "css_element"
    When I click on "#change-user-visibility" "css_element"
    And I wait "1" seconds
    Then "Hide" "icon" should exist in the "#change-user-visibility" "css_element"
    And I log out
    When I log in as "student2"
    And I am on site homepage
    Then I should see "3 online users" in the "Online users" "block"
    And I should not see "Admin" in the "Online users" "block"
    And I should see "Other users (1)" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"

  @javascript
  Scenario: Hide/show icon is not visible in the online users block on front page when the setting is disabled
    Given the following config values are set as admin:
      | block_online_users_onlinestatushiding | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    And I add the "Online users" block
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And "Hide" "icon" should exist in the ".block.block_online_users" "css_element"
    And I log out
    And the following config values are set as admin:
      | block_online_users_onlinestatushiding | 0 |
    When I log in as "student1"
    Then I should see "Student 1" in the "Online users" "block"
    And "Hide" "icon" should not exist in the ".block.block_online_users" "css_element"

  @javascript
  Scenario: User is displayed in the online users block on front page when visibility setting is disabled,
            ignoring the previously set visibility state
    Given the following config values are set as admin:
      | block_online_users_onlinestatushiding | 1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" in current page administration
    And I add the "Online users" block
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And "Hide" "icon" should exist in the "#change-user-visibility" "css_element"
    And I click on "#change-user-visibility" "css_element"
    And I wait "1" seconds
    And "Show" "icon" should exist in the "#change-user-visibility" "css_element"
    And I log out
    And I log in as "student2"
    And I am on site homepage
    And I should see "2 online user" in the "Online users" "block"
    And I should not see "Admin" in the "Online users" "block"
    And I should see "Other users (1)" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should not see "Student 1" in the "Online users" "block"
    And I log out
    And the following config values are set as admin:
      | block_online_users_onlinestatushiding | 0 |
    And I log in as "student2"
    When I am on site homepage
    Then I should see "3 online users" in the "Online users" "block"
    And I should not see "Admin" in the "Online users" "block"
    And I should see "Other users (1)" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"
