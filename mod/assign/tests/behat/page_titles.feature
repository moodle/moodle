@mod @mod_assign
Feature: In an assignment, page titles are informative
  In order to know I am viewing the correct page
  The page titles need to reflect the current assignment and action

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | name                 | intro                       | assignsubmission_onlinetext_enabled |
      | assign   | C1     | History of ants      | Tell me the history of ants | 1                                   |

  Scenario: I view an assignment as a student and take an action
    When I am on the "History of ants" Activity page logged in as student1
    Then the page title should contain "C1: History of ants"
    And I press "Add submission"
    And the page title should contain "C1: History of ants - Edit submission"

  Scenario: I view an assignment as a teacher and take an action
    When I am on the "History of ants" Activity page logged in as teacher1
    Then the page title should contain "C1: History of ants"
    And I follow "View all submissions"
    And the page title should contain "C1: History of ants - Grading"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And the page title should contain "C1: History of ants - Grading"
