@block @block_completionstatus
Feature: Enable Block Completion in a course
  In order to view the completion block in a course
  As a teacher
  I can add completion block to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Add the block to a the course where completion is disabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | No |
    And I press "Save and display"
    When I add the "Course completion status" block
    Then I should see "Completion is not enabled for this course" in the "Course completion status" "block"

  Scenario: Add the block to a the course where completion is not set
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Course completion status" block
    Then I should see "No completion criteria set for this course" in the "Course completion status" "block"

  Scenario: Add the block to a course with criteria and view as an untracked role.
    Given the following "activities" exist:
      | activity | course | idnumber | name           | intro                 |
      | page     | C1     | page1    | Test page name | Test page description |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test page name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Add requirements         | 1                  |
      | View the activity | 1 |
    And I press "Save and return to course"
    When I add the "Course completion status" block
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test page name | 1 |
    And I press "Save changes"
    Then I should see "You are currently not being tracked by completion in this course" in the "Course completion status" "block"
