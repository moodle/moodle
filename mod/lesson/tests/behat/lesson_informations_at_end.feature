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
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Maximum grade  | 75 |
      | Custom scoring | No    |
    And I press "Save and display"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Hardest question ever |
      | Page contents | 1 + 1? |
      | id_answer_editor_0 | 2 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I am on "Course 1" course homepage with editing mode on
    And I duplicate "Test lesson name" activity
    And I wait until section "1" is available
    And I am on the "Test lesson name (copy)" "lesson activity editing" page
    And I set the field "Name" to "Test lesson name 2"
    And I set the field "grade[modgrade_type]" to "Scale"
    And I set the field "Scale" to "Test Scale"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"

  Scenario: Informations at end of lesson if custom scoring not enabled
    Given I am on the "Test lesson name" "lesson activity" page
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
    And I am on the "Test lesson name" "lesson activity" page
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
    Given I am on the "Test lesson name 2" "lesson activity" page
    When I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I press "Continue"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
    And I should see "Your current grade is Excellent"
