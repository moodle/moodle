@block @block_timeline @javascript
Feature: The timeline block shows action events from site home activities
  In order to see everything that requires my action
  As a student
  I can see site home activities in the timeline block on my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference            |
      | student1 | student | Course       | Acceptance test site |
    And the following "activities" exist:
      | activity | course               | name              | duedate      |
      | assign   | C1                   | Course assignment | ##tomorrow## |

  Scenario: Timeline shows course activity events
    When I log in as "student1"
    Then "Course assignment" "link" should exist in the "Timeline" "block"

  Scenario: Timeline shows site home activity events that have no course
    Given the following "activities" exist:
      | activity | course               | name            | duedate      |
      | assign   | Acceptance test site | Site assignment | ##tomorrow## |
    When I log in as "student1"
    Then "Filter timeline by date" "button" should exist in the "Timeline" "block"
    And "Site assignment" "link" should exist in the "Timeline" "block"
    And "Course assignment" "link" should exist in the "Timeline" "block"
