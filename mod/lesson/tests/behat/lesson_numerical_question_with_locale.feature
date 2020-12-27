@mod @mod_lesson
Feature: In a lesson activity, I need to edit pages in the lesson taking into account locale settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | Allow student review | Yes |
    And I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Hardest question ever |
      | Page contents | 1 + 1? |
      | id_answer_editor_0 | 2#87 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 2#1:2#8 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I log out

  Scenario: Edit a numerical question with the locale specific variables
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test lesson name"
    And I click on "Edit" "link" in the "region-main" "region"
    And I follow "Hardest question ever"
    Then I should see "2#87"
    And I should see "2#1:2#8"
    And I log out

  Scenario: View the detailed page of lesson
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test lesson name"
    And I click on "Edit" "link" in the "region-main" "region"
    And I click on "Expanded" "link" in the "region-main" "region"
    Then I should see "2#87"
    And I should see "2#1:2#8"
    And I log out

  Scenario: Attempt the lesson successfully as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#87 |
    And I press "Submit"
    Then I should see "Correct answer"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
    And I log out

  Scenario: Attempt the lesson unsuccessfully as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#7 |
    And I press "Submit"
    Then I should not see "Correct answer"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
    And I log out

  Scenario: Attempt the lesson successfully as a student and review
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#87 |
    And I press "Submit"
    Then I should see "Correct answer"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
    And I follow "Review lesson"
    Then I should see "1 + 1?"
    And the following fields match these values:
      | Your answer | 2#87 |
    And I log out

  Scenario: Edit lesson question page with updated locale setting and wrong answer
    Given I log in as "teacher1"
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | ,     |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test lesson name"
    Then I click on "Edit" "link" in the "region-main" "region"
    And I follow "Hardest question ever"
    Then I should see "2,87"
    And I should see "2,1:2,8"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2,7 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I should not see "Correct answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
