@mod @mod_lesson
Feature: In a lesson activity, if custom scoring is not enabled, student should see
  some informations at the end of lesson: questions answered, correct answers, grade, score

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "scales" exist:
      | name       | scale                          |
      | Test Scale | Disappointing, Good, Excellent |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name             | course | idnumber  |
      | lesson     | Test lesson name | C1     | lesson1   |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title                 | content             |
      | Test lesson name | content | First page name       | First page contents |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?              |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response         | jumpto    | score |
      | First page name       | Next page |                  | Next page | 0     |
      | Hardest question ever | 2         | Correct answer   | Next page | 1     |
      | Hardest question ever | 1         | Incorrect answer | This page | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Maximum grade  | 75 |
      | Custom scoring | No    |
    And I press "Save and display"

  Scenario: Informations at end of lesson if custom scoring not enabled
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    When I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 1"
    And I should see "Number of correct answers: 0"
    And I should see "Your score is 0 (out of 1)."
    And I should see "Your current grade is 0.0 out of 75"

  Scenario: Informations at end of lesson if custom scoring not enabled with custom decimal separator
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    When I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 1"
    And I should see "Number of correct answers: 0"
    And I should see "Your score is 0 (out of 1)."
    And I should see "Your current grade is 0#0 out of 75"

  Scenario: Current grade is displayed at end of lesson when grade type is set to scale
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the field "grade[modgrade_type]" to "Scale"
    And I set the field "Scale" to "Test Scale"
    And I press "Save and return to course"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I press "Continue"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
    And I should see "Your current grade is Excellent"

  Scenario: Verify lesson summary with grade type set to none
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    # Since by default the grade type is point, change it to None.
    And I set the field "grade[modgrade_type]" to "None"
    And I press "Save and return to course"
    # Answer the question incorrectly.
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I press "Next page"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I press "Continue"
    # Confirm the information displayed at the end of lesson when grade type is set to None.
    Then I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 1"
    And I should see "Number of correct answers: 0"
    And I should see "Your score is 0 (out of 1)."
    And I should not see "Your current grade is 0.0 out of 75"
