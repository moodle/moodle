@core @core_message @javascript
Feature: Manage preferences
  In order to control whether I'm contactable
  As a user
  I need to be able to update my messaging preferences

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
    And the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
    And the following config values are set as admin:
      | messaging | 1 |
      | messagingallusers | 1 |

  Scenario: Allow send me a message whe you are a contact and the prefrence is my contacts only
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts only')]]" "xpath_element"
    And I log out
    Then I log in as "student2"
    And I open messaging
    And I send "Hi!" message to "Student 1" user
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: Not allowed to send a message if you are not contact to the sender or you are not in the same course
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts and anyone in my courses')]]" "xpath_element"
    And I log out
    Then I log in as "student4"
    And I open messaging
    And I select "Student 1" user in messaging
    And I should see "You are unable to message this user" in the "//*[@data-region='content-messages-footer-unable-to-message']" "xpath_element"

  Scenario: Allow send me a message whe you are a contact and the prefrence is my contacts only
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts and anyone in my courses')]]" "xpath_element"
    And I log out
    Then I log in as "student3"
    And I open messaging
    And I send "Hi!" message to "Student 1" user
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: Allowed to send a message if you are not contact to the sender and  you are not in the same course
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'Anyone on the site')]]" "xpath_element"
    And I log out
    Then I log in as "student4"
    And I open messaging
    And I send "Hi!" message to "Student 1" user
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: Allow send a message using Enter button
    Given I log in as "student1"
    And I open messaging
    And I select "Student 2" user in messaging
    And I set the field with xpath "//textarea[@data-region='send-message-txt']" to "Hi!"
    And I press key "13" in "//textarea[@data-region='send-message-txt']" "xpath_element"
    Then I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: No allow to send a messade using Enter button
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'Use enter to send')]]" "xpath_element"
    And I go back in "view-settings" message drawer
    Then I select "Student 2" user in messaging
    And I set the field with xpath "//textarea[@data-region='send-message-txt']" to "Hi!"
    And I press key "13" in "//textarea[@data-region='send-message-txt']" "xpath_element"
    And I should not see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"
    And I press "Send message"
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"
