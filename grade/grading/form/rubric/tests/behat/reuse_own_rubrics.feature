@gradingform @gradingform_rubric
Feature: Reuse my rubrics in other activities
  In order to save time creating duplicated grading forms
  As a teacher
  I need to reuse rubrics that I created previously

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
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment 1 name |
      | Description | Test assignment 1 description |
      | Grading method | Rubric |
    And I go to "Test assignment 1 name" advanced grading definition page
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Assignment 1 description |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 11 | Level 12 | 12 | Level 3 | 13 |
      | Criterion 2 | Level 21 | 21 | Level 22 | 22 | Level 3 | 23 |
      | Criterion 3 | Level 31 | 31 | Level 32 | 32 |         |    |
    And I press "Save rubric and make it ready"
    And I follow "Course 1"
    When I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment 2 name |
      | Description | Test assignment 2 description |
      | Grading method | Rubric |
    And I set "Test assignment 2 name" activity to use "Assignment 1 rubric" grading form
    Then I should see "Ready for use"
    And I should see "Criterion 1"
    And I should see "Criterion 2"
    And I should see "Criterion 3"
    And I follow "Course 1"
    And I go to "Test assignment 1 name" advanced grading definition page
    And I should see "Criterion 1"
    And I should see "Criterion 2"
    And I should see "Criterion 3"
    And I press "Cancel"

  @javascript
  Scenario: A teacher can reuse one of his/her previously created rubrics, with Javascript enabled

  Scenario: A teacher can reuse one of his/her previously created rubrics, with Javascript disabled
