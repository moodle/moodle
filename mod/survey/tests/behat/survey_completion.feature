@mod @mod_survey @core_completion
Feature: A teacher can use activity completion to track a student progress
  In order to use activity completion
  As a teacher
  I need to set survey activities and enable activity completion

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Require survey view
    Given the following "activities" exist:
      | activity | course | name             | template | completion | completionview | completionsubmit |
      | survey   | C1     | Test survey name | 5        | 2          | 1              | 0                |
    When I am on the "Course 1" course page logged in as student1
    And the "Test survey name" "survey" activity with "auto" completion should be marked as not complete
    And I follow "Test survey name"
    And I am on "Course 1" course homepage
    Then the "Test survey name" "survey" activity with "auto" completion should be marked as complete

  Scenario: Require survey submission
    Given the following "activities" exist:
      | activity | course | name             | template | completion | completionsubmit |
      | survey   | C1     | Test survey name | 5        | 2          | 1                |
    When I am on the "Course 1" course page logged in as student1
    And the "Test survey name" "survey" activity with "auto" completion should be marked as not complete
    And I follow "Test survey name"
    And I press "Click here to continue"
    And I am on "Course 1" course homepage
    Then the "Test survey name" "survey" activity with "auto" completion should be marked as complete
