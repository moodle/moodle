@mod @mod_survey
Feature: A teacher can set three types of survey activity
  In order to use verified survey instruments
  As a teacher
  I need to set survey activities and select which survey type suits my needs

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"

  Scenario: Switching between the three survey types
    Given the following "activities" exist:
      | activity | course | name             |
      | survey   | C1     | Test survey name |
    When I am on the "Test survey name" "survey activity" page
    Then I should see "Attitudes Towards Thinking and Learning"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Survey type | Critical incidents |
    And I press "Save and display"
    And I should see "At what moment in class were you most engaged as a learner?"
    And I follow "Edit settings"
    And I set the following fields to these values:
      | Survey type | COLLES (Preferred and Actual) |
    And I press "Save and display"
    And I should see "In this online unit..."
    And I should see "my learning focuses on issues that interest me."

  @javascript
  Scenario: Survey activity is created via UI
    Given I am on the "Course 1" course page
    And I turn editing mode on
    And I add a "Survey" to section "1"
    And I set the following fields to these values:
      | Name        | Test survey name        |
      | Description | Test survey description |
      | Survey type | Critical incidents      |
    When I press "Save and return to course"
    Then I should see "Test survey name"
