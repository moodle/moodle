@mod @mod_forum @app @javascript
Feature: Test basic usage in app
  In order to participate in the forum while using the mobile app
  As a student
  I need basic forum functionality to work

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity   | name            | intro       | course | idnumber | groupmode |
      | forum      | Test forum name | Test forum  | C1     | forum    | 0         |

  Scenario: Student starts a discussion
    When I enter the app
    And I log in as "student1"
    And I press "Course 1" near "Course overview" in the app
    And I press "Test forum name" in the app
    And I press "Add a new discussion topic" in the app
    And I set the field "Subject" to "My happy subject" in the app
    And I set the field "Message" to "An awesome message" in the app
    And I press "Post to forum" in the app
    Then I should see "My happy subject"
    And I should see "An awesome message"

  Scenario: Student posts a reply
    When I enter the app
    And I log in as "student1"
    And I press "Course 1" near "Course overview" in the app
    And I press "Test forum name" in the app
    And I press "Add a new discussion topic" in the app
    And I set the field "Subject" to "DiscussionSubject" in the app
    And I set the field "Message" to "DiscussionMessage" in the app
    And I press "Post to forum" in the app
    And I press "DiscussionSubject" in the app
    And I press "Reply" in the app
    And I set the field "Message" to "ReplyMessage" in the app
    And I press "Post to forum" in the app
    Then I should see "DiscussionMessage"
    And I should see "ReplyMessage"

  Scenario: Test that 'open in browser' works for forum
    When I enter the app
    And I change viewport size to "360x640"
    And I log in as "student1"
    And I press "Course 1" near "Course overview" in the app
    And I press "Test forum name" in the app
    And I press the page menu button in the app
    And I press "Open in browser" in the app
    And I switch to the browser tab opened by the app
    And I log in as "student1"
    Then I should see "Test forum name"
    And I should see "Add a new discussion topic"
    And I close the browser tab opened by the app
    And I press the back button in the app
