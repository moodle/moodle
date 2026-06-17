@mod @mod_choice
Feature: Display the course linear navigation in the choice pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in choice pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name        | intro                   | course | idnumber | allowupdate | option                       |
      | choice   | Choice1 | Test choice description | C1     | choice1  | 1           | Option 1, Option 2, Option 3 |

  @javascript
  Scenario: As a student I should see the course linear navigation in choice pages that allow it
    Given I am on the "Choice1" "choice activity" page logged in as "student"
    Then the course linear navigation should be visible
    And I choose "Option 1" from "Choice1" choice activity
    And I should see "Your choice has been saved"
    And the course linear navigation should be visible
    And I follow "Remove my choice"
    And the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in choice pages that allow it
    Given I am on the "Choice1" "choice activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    And I choose "Option 1" from "Choice1" choice activity
    And I should see "Your choice has been saved"
    And the course linear navigation should be visible
    And I follow "Remove my choice"
    And the course linear navigation should be visible
    And I follow "Responses"
    And the course linear navigation should not be visible
