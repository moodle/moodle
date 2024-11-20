@mod @mod_survey @core_completion @javascript
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
    And I enable "survey" "mod" plugin
    And I log in as "teacher1"

  Scenario: Require survey view
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber    | template | completion | completionview | completionsubmit |
      | survey     | Test survey name       | C1     | survey1     |  5       | 2          | 1              | 0                |
    And I am on the "Test survey name" "survey activity" page
    # Teacher view.
    And "Test survey name" should have the "View" completion condition
    # Student view.
    When I am on the "Course 1" course page logged in as student1
    And the "View" completion condition of "Test survey name" is displayed as "todo"
    And I follow "Test survey name"
    And I am on "Course 1" course homepage
    Then the "View" completion condition of "Test survey name" is displayed as "done"

  Scenario: Require survey submission
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber    | template | completion | completionview | completionsubmit |
      | survey     | Test survey name       | C1     | survey1     | 5        | 2          | 1              | 1                |
    And I am on the "Test survey name" "survey activity" page
    # Teacher view.
    And "Test survey name" should have the "Submit answers" completion condition
    # Student view.
    When I am on the "Course 1" course page logged in as student1
    And the "Submit answers" completion condition of "Test survey name" is displayed as "todo"
    And I follow "Test survey name"
    And the "Submit answers" completion condition of "Test survey name" is displayed as "todo"
    And I press "Submit"
    And I am on "Course 1" course homepage
    And the "Submit answers" completion condition of "Test survey name" is displayed as "done"
    And I follow "Test survey name"
    And the "Submit answers" completion condition of "Test survey name" is displayed as "done"

  Scenario: Use manual completion
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber    | completion |
      | survey     | Test survey name       | C1     | survey1     | 1          |
    And I am on "Course 1" course homepage
    # Teacher view.
    And "Test survey name" should have the "Mark as done" completion condition
    # Student view.
    When I am on the "survey1" Activity page logged in as student1
    Then the manual completion button of "Test survey name" is displayed as "Mark as done"
    And I toggle the manual completion state of "Test survey name"
    And the manual completion button of "Test survey name" is displayed as "Done"
