@mod @mod_workshop
Feature: Workshop grading strategy selection
  In order to verify that the assessment form is displayed correctly
  As a teacher
  I need to choose one of the four workshop grading strategies

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Mary      | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name       |
      | workshop | C1     | Workshop 1 |
    And I am on the "Workshop 1" "workshop activity editing" page logged in as teacher1

  Scenario: Choose accumulative grading as grading strategy
    Given I set the following fields to these values:
      | strategy | accumulative |
    And I press "Save and display"
    When I click on "Assessment form" "link"
    Then I should see "Accumulative grading"
    And I should see "Description"
    And I should see "Best possible grade / Scale to use"
    And I should see "Weight"
    And I set the following fields to these values:
      | id_description__idx_0_editor | Aspect 1 |
      | id_description__idx_1_editor | Aspect 2 |
    And I press "Save and preview"
    And I should see "Assessment form"
    And I should see "Aspect 1"
    And I should see "Grade for Aspect 1"
    And I should see "Comment for Aspect 1"
    And I should see "Aspect 2"
    And I should see "Grade for Aspect 2"
    And I should see "Comment for Aspect 2"
    And I should see "Overall feedback"
    And I should see "Feedback for the author"
    And I press "Back to editing form"
    And I should see "Accumulative grading"

  Scenario: Choose comments as grading strategy
    Given I set the following fields to these values:
      | strategy | comments |
    And I press "Save and display"
    When I click on "Assessment form" "link"
    Then I should see "Comments"
    And I should see "Description"
    And I set the following fields to these values:
      | id_description__idx_0_editor | Aspect 1 |
      | id_description__idx_1_editor | Aspect 2 |
    And I press "Save and preview"
    And I should see "Assessment form"
    And I should see "Aspect 1"
    And I should see "Comment for Aspect 1"
    And I should see "Aspect 2"
    And I should see "Comment for Aspect 2"
    And I should see "Overall feedback"
    And I should see "Feedback for the author"
    And I press "Back to editing form"
    And I should see "Comments"

  Scenario: Choose number of errors as grading strategy
    Given I set the following fields to these values:
      | strategy | numerrors |
    And I press "Save and display"
    When I click on "Assessment form" "link"
    Then I should see "Number of errors"
    And I should see "Description"
    And I should see "Word for the error"
    And I should see "Word for the success"
    And I should see "Weight"
    And I set the following fields to these values:
      | id_description__idx_0_editor | Assertion 1 |
      | id_description__idx_1_editor | Assertion 2 |
    And I press "Save and preview"
    And I should see "Assessment form"
    And I should see "Assertion 1"
    And I should see "Your assessment for Assertion 1"
    And I should see "Comment for Assertion 1"
    And I should see "Assertion 2"
    And I should see "Your assessment for Assertion 2"
    And I should see "Comment for Assertion 2"
    And I should see "Overall feedback"
    And I should see "Feedback for the author"
    And I press "Back to editing form"
    And I should see "Number of errors"

  Scenario: Choose rubric as grading strategy
    Given I set the following fields to these values:
      | strategy | rubric |
    And I press "Save and display"
    When I click on "Assessment form" "link"
    Then I should see "Rubric"
    And I should see "Description"
    And I should see "Level grade and definition"
    And I set the following fields to these values:
      | id_description__idx_0_editor | Criterion 1 |
      | definition__idx_0__idy_0     | One zero    |
      | id_description__idx_1_editor | Criterion 2 |
      | definition__idx_1__idy_0     | Two zero    |
    And I press "Save and preview"
    And I should see "Assessment form"
    And I should see "Criterion 1"
    And I should see "One zero"
    And I should see "Criterion 2"
    And I should see "Two zero"
    And I should see "Overall feedback"
    And I should see "Feedback for the author"
    And I press "Back to editing form"
    And I should see "Rubric"
