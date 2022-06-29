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
    And the following "activities" exist:
      | activity   | name                      | intro                           | course | section | idnumber |
      | assign     | Test assignment 1 name    | Test assignment 1 description   | C1     | 1       | assign1  |
      | assign     | Test assignment 2 name    | Test assignment 2 description   | C1     | 1       | assign1  |
    And I am on the "Test assignment 1 name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Grading method | Rubric |
    And I press "Save and return to course"
    And I go to "Test assignment 1 name" advanced grading definition page
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Assignment 1 description |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 11 | Level 12 | 12 | Level 3 | 13 |
      | Criterion 2 | Level 21 | 21 | Level 22 | 22 | Level 3 | 23 |
      | Criterion 3 | Level 31 | 31 | Level 32 | 32 |         |    |
    And I press "Save rubric and make it ready"
    And I am on the "Test assignment 2 name" "assign activity editing" page
    And I set the following fields to these values:
      | Grading method | Rubric |
    And I press "Save and return to course"
    And I set "Test assignment 2 name" activity to use "Assignment 1 rubric" grading form
    Then I should see "Ready for use"
    And I should see "Criterion 1"
    And I should see "Criterion 2"
    And I should see "Criterion 3"
    And I am on "Course 1" course homepage
    And I go to "Test assignment 1 name" advanced grading definition page
    And I should see "Criterion 1"
    And I should see "Criterion 2"
    And I should see "Criterion 3"

  @javascript
  Scenario: A teacher can reuse one of his/her previously created rubrics, with Javascript enabled

  Scenario: A teacher can reuse one of his/her previously created rubrics, with Javascript disabled
