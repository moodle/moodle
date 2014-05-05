@gradingform @gradingform_rubric
Feature: Publish rubrics as templates
  In order to save time to teachers
  As a manager
  I need to publish rubrics and make them available to all teachers

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | manager1 | Manager | 1 | manager1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "activities" exist:
      | activity | course | idnumber | name                   | intro | advancedgradingmethod_submissions |
      | assign   | C1     | A1       | Test assignment 1 name | TA1   | rubric                            |
      | assign   | C1     | A2       | Test assignment 2 name | TA2   | rubric                            |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "system role assigns" exist:
      | user | role | contextlevel | reference |
      | manager1 | manager | System | |
    And I log in as "manager1"
    And I follow "Course 1"
    And I go to "Test assignment 1 name" advanced grading definition page
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Assignment 1 description |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 11 | Level 12 | 12 |
      | Criterion 2 | Level 21 | 21 | Level 22 | 22 |
    And I press "Save rubric and make it ready"
    When I publish "Test assignment 1 name" grading form definition as a public template
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I set "Test assignment 2 name" activity to use "Assignment 1 rubric" grading form
    Then I should see "Advanced grading: Test assignment 2 name (Submissions)"
    And I should see "Criterion 1"
    And I should see "Assignment 1 description"
    And I go to "Test assignment 2 name" advanced grading definition page
    And I should see "Current rubric status"

  @javascript
  Scenario: Create a rubric template and reuse it as a teacher, with Javascript enabled
    Then the field "Description" matches value "Assignment 1 description"
    And I should see "Criterion 1"
    And I press "Cancel"

  Scenario: Create a rubric template and reuse it as a teacher, with Javascript disabled
    Then the field "Description" matches value "Assignment 1 description"
    # Trying to avoid pointing by id or name as the code internals may change.
    And "//table[@class='criteria']//textarea[text()='Criterion 1']" "xpath_element" should exist
    And I press "Cancel"
