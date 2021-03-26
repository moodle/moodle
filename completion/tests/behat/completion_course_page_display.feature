@core @core_completion
Feature: Show activity completion status or activity completion configuration on the course page
  In order to understand the configuration or status of an activity's completion
  As a user
  I need to see the appropriate completion information for each activity in the course homepage

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | First    | teacher1@example.com |
      | teacher2 | Teacher   | Second   | teacher2@example.com |
      | student1 | Student   | First    | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And the following "activities" exist:
      | activity | course | idnumber | name            | intro                  | completion | completionview | completionexpected |
      | forum    | C1     | forum1   | Test forum name | Test forum description | 1          | 0              | 0                  |
    And the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | completion | completionview | completionexpected |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 2          | 1              | 0                  |
    And the following "activities" exist:
      | activity | course | idnumber | name           | intro                 | completion | completionview | completionexpected |
      | quiz     | C1     | quiz1    | Test quiz name | Test quiz description | 0          | 0              | 0                  |
    And I log out

  Scenario: Show completion status to students
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Your progress"
    And the manual completion button of "Test forum name" is displayed as "Mark as done"
    And the "View" completion condition of "Test assignment name" is displayed as "todo"
    And there should be no completion information shown for "Test quiz name"

  Scenario: Show completion configuration to editing teachers
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then I should not see "Your progress"
    And the manual completion button for "Test forum name" should be disabled
    And "Test assignment name" should have the "View" completion condition
    And there should be no completion information shown for "Test quiz name"
    And I am on "Course 1" course homepage with editing mode on
    And I should not see "Your progress"
    And the manual completion button for "Test forum name" should be disabled
    And "Test assignment name" should have the "View" completion condition
    And there should be no completion information shown for "Test quiz name"

  Scenario: Show completion configuration to non-editing teachers
    Given I log in as "teacher2"
    And I am on "Course 1" course homepage
    Then I should not see "Your progress"
    And the manual completion button for "Test forum name" should be disabled
    And "Test assignment name" should have the "View" completion condition
    And there should be no completion information shown for "Test quiz name"
