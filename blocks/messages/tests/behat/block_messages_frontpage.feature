@block @block_messages
Feature: The messages block allows users to list new messages on the frontpage
  In order to enable the messages block on the frontpage
  As an admin
  I can add the messages block to a the frontpage and view my messages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "Messages" block
    And I log out

  Scenario: View the block by a user with messaging disabled.
    Given the following config values are set as admin:
      | messaging       | 0 |
    And I log in as "admin"
    And I am on site homepage
    When I navigate to "Turn editing on" node in "Front page settings"
    And I should see "Messaging is disabled on this site" in the "Messages" "block"
    Then I navigate to "Turn editing off" node in "Front page settings"
    And I should not see "Messaging is disabled on this site"

  Scenario: View the block by a user who does not have any messages.
    Given I log in as "teacher1"
    When I am on site homepage
    Then I should see "No messages" in the "Messages" "block"

  Scenario: Try to view the block as a guest user.
    Given I log in as "guest"
    When I am on site homepage
    Then I should not see "Messages"

  @javascript
  Scenario: View the block by a user who has messages.
    Given I log in as "student1"
    And I follow "Messages" in the user menu
    And I send "This is message 1" message to "Teacher 1" user
    And I send "This is message 2" message to "Teacher 1" user
    And I log out
    When I log in as "teacher1"
    And I am on site homepage
    Then I should see "Student 1" in the "Messages" "block"

  @javascript
  Scenario: Use the block to send a message to a user.
    Given I log in as "teacher1"
    And I am on site homepage
    And I click on "//a[normalize-space(.) = 'Messages']" "xpath_element" in the "Messages" "block"
    And I send "This is message 1" message to "Student 1" user
    And I log out
    When I log in as "student1"
    And I am on site homepage
    Then I should see "Teacher 1" in the "Messages" "block"
