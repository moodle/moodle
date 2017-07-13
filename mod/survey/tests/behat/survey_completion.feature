@mod @mod_survey
Feature: A teacher can use activity completion to track a student progress
  In order to use activity completion
  As a teacher
  I need to set survey activities and enable activity completion

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Require survey view
    Given I add a "Survey" to section "1" and I fill the form with:
      | Name | Test survey name |
      | Survey type | Critical incidents |
      | Description | Test survey description |
      | Completion tracking | Show activity as complete when conditions are met |
      | id_completionview   | 1 |
      | id_completionsubmit | 0 |
    And I turn editing mode off
    And the "Test survey name" "survey" activity with "auto" completion should be marked as not complete
    When I follow "Test survey name"
    And I am on "Course 1" course homepage
    Then the "Test survey name" "survey" activity with "auto" completion should be marked as complete

  Scenario: Require survey submission
    Given I add a "Survey" to section "1" and I fill the form with:
      | Name | Test survey name |
      | Survey type | Critical incidents |
      | Description | Test survey description |
      | Completion tracking | Show activity as complete when conditions are met |
      | id_completionsubmit | 1 |
    And I turn editing mode off
    And the "Test survey name" "survey" activity with "auto" completion should be marked as not complete
    When I follow "Test survey name"
    And I press "Click here to continue"
    And I am on "Course 1" course homepage
    Then the "Test survey name" "survey" activity with "auto" completion should be marked as complete
