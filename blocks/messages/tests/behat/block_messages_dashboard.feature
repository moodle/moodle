@block @block_messages
Feature: The messages block allows users to list new messages on the dashboard
  In order to enable the messages block on the dashboard
  As a user
  I can add the messages block to a my dashboard and view my messages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |

  Scenario: View the block by a user with messaging disabled.
    Given the following config values are set as admin:
      | messaging       | 0 |
    And I log in as "teacher1"
    And I press "Customise this page"
    When I add the "Messages" block
    Then I should see "Messaging is disabled on this site" in the "Messages" "block"

  Scenario: View the block by a user who does not have any messages.
    Given I log in as "teacher1"
    And I press "Customise this page"
    When I add the "Messages" block
    Then I should see "No messages waiting" in the "Messages" "block"

  Scenario: View the block by a user who has messages.
    Given I log in as "student1"
    And I follow "Messages" in the user menu
    And I send "This is message 1" message to "Teacher 1" user
    And I send "This is message 2" message to "Teacher 1" user
    And I log out
    When I log in as "teacher1"
    And I press "Customise this page"
    And I add the "Messages" block
    Then I should see "Student 1" in the "Messages" "block"

  Scenario: Use the block to send a message to a user.
    Given I log in as "teacher1"
    And I press "Customise this page"
    And I add the "Messages" block
    And I follow "Messages"
    And I send "This is message 1" message to "Student 1" user
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "Messages" block
    Then I should see "Teacher 1" in the "Messages" "block"
