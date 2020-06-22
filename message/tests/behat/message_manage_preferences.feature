@core @core_message @javascript
Feature: Manage preferences
  In order to control whether I'm contactable
  As a user
  I need to be able to update my messaging preferences

  Background:
    # Note: This course is using separate groups mode.
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
      | student5 | Student   | 5        | student5@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
      | student4 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student4 | G1 |
    And the following "message contacts" exist:
      | user     | contact |
      | student1 | student2 |
    And the following config values are set as admin:
      | messaging         | 1 |
      | messagingallusers | 1 |
      | messagingminpoll  | 1 |

  # Recipient has 'My contacts only' set.
  Scenario: Allow sending a message when you are a contact
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts only')]]" "xpath_element"
    And I log out
    Then I log in as "student2"
    And I open messaging
    And I send "Hi!" message to "Student 1" user
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  # Recipient has 'My contacts and anyone in my courses' set.
  Scenario: Disallow sending a message if you are neither contacts with the recipient nor do you share a course
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts and anyone in my courses')]]" "xpath_element"
    And I log out
    Then I log in as "student5"
    And I open messaging
    And I search for "Student 1" in messaging
    And I should see "No results"

  # Recipient has 'My contacts and anyone in my courses' set.
  Scenario: Allow sending a message if you share a group in a shared course
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts and anyone in my courses')]]" "xpath_element"
    And I log out
    Then I log in as "student4"
    And I open messaging
    And I send "Hi!" message to "Student 1" user
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  # Recipient has 'My contacts and anyone in my courses' set.
  Scenario: Disallow sending a message if you are neither a contact, nor are in the same group in a shared course
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'My contacts and anyone in my courses')]]" "xpath_element"
    And I log out
    Then I log in as "student3"
    And I open messaging
    And I search for "Student 1" in messaging
    And I should see "No results"

  # Recipient has 'Anyone on the site' set. Only users whose profiles are visible can be found via the search.
  Scenario: Disallow sending a message if you are neither a contact nor do you share a course with the user.
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'Anyone on the site')]]" "xpath_element"
    And I log out
    Then I log in as "student5"
    And I open messaging
    And I search for "Student 1" in messaging
    And I should see "No results"

  Scenario: Sending a message when 'User enter to send' is enabled
    Given I log in as "student1"
    And I open messaging
    And I select "Student 2" user in messaging
    And I set the field with xpath "//textarea[@data-region='send-message-txt']" to "Hi!"
    And I press the enter key
    Then I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: Sending a message after 'Use enter to send' is disabled
    Given I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    When I click on "//label[text()[contains(.,'Use enter to send')]]" "xpath_element"
    And I go back in "view-settings" message drawer
    Then I select "Student 2" user in messaging
    And I set the field with xpath "//textarea[@data-region='send-message-txt']" to "Hi!"
    And I press the enter key
    And I should not see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"
    And I press "Send message"
    And I should see "Hi!" in the "//*[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"
