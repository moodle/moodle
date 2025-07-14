@core @core_message @javascript
Feature: Capability test for 'moodle/site:messageanyuser'
  In order to test that the 'moodle/site:messageanyuser' works as expected
  As a user with or without the capability 'moodle/site:messageanyuser'
  I should either be able to message anyone regardless of their messaging preferences, or not

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | teacher |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following config values are set as admin:
      | messaging         | 1 |
      | messagingallusers | 1 |
    And I log in as "student1"
    And I open messaging
    And I open messaging settings preferences
    And I click on "//label[text()[contains(.,'My contacts only')]]" "xpath_element"
    And I log out

  Scenario: Allow a message to be sent as the user has the correct capabilities
    Given I log in as "teacher1"
    And I open messaging
    When I send "Hi!" message to "Student 1" user
    Then I should see "Hi!" in the "//div[@data-region='message-drawer']//div[@data-region='content-message-container']" "xpath_element"

  Scenario: Do not allow a message to be sent as the user does not have the correct capabilities
    Given I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Participants"
    And I follow "Student 1"
    When I click on "Message" "link" in the ".header-button-group" "css_element"
    Then I should see "Student 1 is not in your contacts"
