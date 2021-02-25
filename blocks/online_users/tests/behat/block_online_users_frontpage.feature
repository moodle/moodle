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
    And I navigate to "Turn editing on" node in "Front page settings"
    When I add the "Online users" block
    Then I should see "Admin User" in the "Online users" "block"
    And I should see "1 online user" in the "Online users" "block"

  Scenario: View the online users block on the front page as a logged in user
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "Online users" block
    And I log out
    And I log in as "student2"
    And I log out
    When I log in as "student1"
    And I am on site homepage
    Then I should not see "Admin User" in the "Online users" "block"
    And I should see "Other Users (1)" in the "Online users" "block"
    And I should see "Student 1" in the "Online users" "block"
    And I should see "Student 2" in the "Online users" "block"
    And I should see "3 online users" in the "Online users" "block"

  Scenario: View the online users block on the front page as a guest
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
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
