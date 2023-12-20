@mod @mod_chat @javascript
Feature: View past chat sessions
  In order for students to view past chat sessions
  As a teacher
  I need to be able to change the mod/chat:readlog permission for students

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario Outline: View past chat sessions
    # Display or hide past chat session based on example data
    Given the following "activities" exist:
      | activity | course | name   | studentlogs       |
      | chat     | C1     | Chat 1 | <studentlogvalue> |
    And I am on the "Chat 1" "chat activity" page logged in as teacher1
    # Display or hide past chat session by default based on mod/chat:readlog setting
    And the following "role capability" exists:
      | role             | student          |
      | mod/chat:readlog | <readpermission> |
    # Enter chat activity to create a session
    And I click on "Enter the chat" "link"
    # Close chat window
    When I close all opened windows
    And I reload the page
    # Confirm that past chat sessions is always visible for teacher
    Then I should see "Past sessions"
    # Confirm past chat sessions visibility for student based on settings
    And I am on the "Chat 1" "chat activity" page logged in as student1
    And I <sessionvisibility> see "Past sessions"

    # Regardless of studentlogvalue value if readpermission is allowed, then Past sessions will be visible for students
    Examples:
      | studentlogvalue | readpermission | sessionvisibility |
      | 0               | allow          | should            |
      | 1               | allow          | should            |
      | 0               | prohibit       | should not        |
      | 1               | prohibit       | should            |
