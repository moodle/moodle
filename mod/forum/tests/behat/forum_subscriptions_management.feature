@mod @mod_forum
Feature: A teacher can control the subscription to a forum
  In order to change individual user's subscriptions
  As a course administrator
  I can change subscription setting for my users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher  | Teacher   | Tom      | teacher@example.com   |
      | student1 | Student   | 1        | student.1@example.com |
      | student2 | Student   | 2        | student.2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |

  Scenario: A teacher can change toggle subscription editing on and off
    Given the following "activity" exists:
      | activity         | forum                  |
      | course           | C1                     |
      | idnumber         | f01                    |
      | name             | Test forum name        |
    When I am on the "Test forum name" "forum activity" page logged in as teacher
    And I navigate to "Subscriptions" in current page administration
    Then I select "Manage subscribers" from the "Subscribers" singleselect
    And "Add" "button" should exist
    And "Remove" "button" should exist
    And I select "View subscribers" from the "Subscribers" singleselect
    And the "Subscribers" select box should contain "View subscribers"

  @javascript
  Scenario Outline: Toggle forum subscription mode via settings navigation
    Given the following "activity" exists:
      | course         | C1            |
      | activity       | forum         |
      | name           | Test forum    |
      | idnumber       | F1            |
      | type           | general       |
      | forcesubscribe | <initialmode> |
    When I am on the "Test forum" "forum activity" page logged in as "teacher"
    And I navigate to "Subscriptions" in current page administration
    And I select "<updatedmode>" from the "Subscription mode" singleselect
    Then I should see "<updatedmodeconfirmed>"
    Examples:
      | initialmode | updatedmode           | updatedmodeconfirmed                     |
      | 1           | Optional subscription | Everyone can now choose to be subscribed |
      | 0           | Forced subscription   | Everyone is now subscribed to this forum |
      | 0           | Auto subscription     | Everyone is now subscribed to this forum |
      | 0           | Subscription disabled | Subscriptions are now disallowed         |
