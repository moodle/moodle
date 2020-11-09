@mod @mod_forum @javascript
Feature: View discussion while grading in a forum
  In order to grade efficiently
  As a teacher
  I want to be able to see the full discussion the student was taking part in.

  Background:
    # Student 1 needs to be created first or they will not be the first user on the grading screen.
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | student1 | Student   | 1        | student.1@example.com |
      | student2 | Student   | 2        | student.2@example.com |
      | teacher  | Teacher   | Tom      | teacher@example.com   |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher  | C1     | editingteacher |
    And the following "activity" exists:
      | activity    | forum                      |
      | name        | Gradable forum             |
      | intro       | Standard forum description |
      | course      | C1                         |
      | idnumber    | forum1                     |
      | grade_forum | 100                        |
      | scale       | 100                        |
    # If there is more than one pots for Student 1 the test will not be able to select the
    # correct View discussion link, as there is no selector for thier container.
    And the following forum discussions exist in course "Course 1":
      | forum          | user     | name     | message                          |
      | Gradable forum | student1 | My topic | This is the thing I posted about |
    And the following forum replies exist in course "Course 1":
      | forum          | user     | discussion | message    |
      | Gradable forum | student2 | My topic   | I disagree |

  Scenario: Viewing a discussion
    Given I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Gradable forum"
    And I press "Grade users"
    When I press "View discussion"
    Then I should see "I disagree" in the "My topic" "dialogue"
    And I click on "Cancel" "button" in the "My topic" "dialogue"
    And I should not see "I disagree"

  Scenario: Viewing a discussion while grading is fullscreen
    Given I log in as "teacher"
    And I am on "Course 1" course homepage
    And I follow "Gradable forum"
    And I press "Grade users"
    # Uses the aria-label for the menu in in the grading interface.
    And I press "Actions for the grader interface"
    And I press "Toggle full screen"
    When I press "View discussion"
    Then I should see "I disagree" in the "My topic" "dialogue"
    And I click on "Cancel" "button" in the "My topic" "dialogue"
    And I should not see "I disagree"
