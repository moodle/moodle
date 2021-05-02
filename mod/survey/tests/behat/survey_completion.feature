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

  Scenario: Require survey view
    Given the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | survey     | Test survey name       | Test survey description       | C1     | survey1     |
    And I am on "Course 1" course homepage
    And I follow "Test survey name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Survey type | Critical incidents |
      | Completion tracking | Show activity as complete when conditions are met |
      | completionview   | 1 |
      | completionsubmit | 0 |
    And I press "Save and return to course"
    And I follow "Test survey name"
    # Teacher view.
    And "Test survey name" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And the "View" completion condition of "Test survey name" is displayed as "todo"
    And I follow "Test survey name"
    And I am on "Course 1" course homepage
    Then the "View" completion condition of "Test survey name" is displayed as "done"

  Scenario: Require survey submission
    Given the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    |
      | survey     | Test survey name       | Test survey description       | C1     | survey1     |
    And I am on "Course 1" course homepage
    And I follow "Test survey name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Survey type | Critical incidents |
      | Completion tracking | Show activity as complete when conditions are met |
      | completionview   | 1 |
      | completionsubmit | 1 |
    And I press "Save and return to course"
    And I follow "Test survey name"
    # Teacher view.
    And "Test survey name" should have the "Submit answers" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And the "Submit answers" completion condition of "Test survey name" is displayed as "todo"
    And I follow "Test survey name"
    And the "Submit answers" completion condition of "Test survey name" is displayed as "todo"
    And I press "Click here to continue"
    And I am on "Course 1" course homepage
    And the "Submit answers" completion condition of "Test survey name" is displayed as "done"
    And I follow "Test survey name"
    And the "Submit answers" completion condition of "Test survey name" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | completion |
      | survey     | Test survey name       | Test survey description       | C1     | survey1     | 1          |
    And I am on "Course 1" course homepage
    # Teacher view.
    And the manual completion button for "Test survey name" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test survey name"
    Then the manual completion button of "Test survey name" is displayed as "Mark as done"
    And I toggle the manual completion state of "Test survey name"
    And the manual completion button of "Test survey name" is displayed as "Done"
