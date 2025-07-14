@gradingform @gradingform_guide
Feature: Teacher can delete marking guide
  As a teacher,
  I should be able to delete a marking guide

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name        | Marking guide 1 |
    And I define the following marking guide:
      | Criterion name    | Description for students            | Description for markers             | Maximum score |
      | Criterion 1       | Criterion 1 description for student | Criterion 1 description for markers | 100           |
    And I press "Save marking guide and make it ready"

  @javascript
  Scenario: Delete a marking guide
    Given I am on the "Assign 1" "assign activity" page
    And I go to "Student One" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Criterion 1 | 70 | Well done! |
    And I press "Save changes"
    And I go to "Assign 1" advanced grading page
    When I click on "Delete the currently defined form" "link"
    Then I should see "You are going to delete the grading form 'Marking guide 1' and all the associated information from 'Assign 1 (Submissions)'"
    And I press "Cancel"
    # Confirm that marking guide was not deleted if Cancel is pressed
    And I should see "Marking guide 1 Ready for use"
    And I should see "Criterion 1"
    And I click on "Delete the currently defined form" "link"
    And I press "Continue"
    # Confirm that marking guide was deleted successfully if Continue is pressed
    And I should see "Please note: the advanced grading form is not ready at the moment. Simple grading method will be used until the form has a valid status."
    And I should not see "Marking guide 1 Ready for use"
    And I should not see "Criterion 1"
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-         | -2-                  | -3- |
      | Student One | student1@example.com | 70  |
