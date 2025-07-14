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
    And the following "activities" exist:
      | activity   | name                   | course | idnumber    | modattempts |
      | lesson     | Test lesson name       | C1     | lesson1     | 1           |
    And the following "mod_lesson > page" exist:
      | lesson           | qtype   | title                 | content  |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?   |
    And the following "mod_lesson > answers" exist:
      | page                  | answer  | response         | jumpto        | score |
      | Hardest question ever | 2.87    | Correct answer   | End of lesson | 1     |
      | Hardest question ever | 2.1:2.8 | Incorrect answer | This page     | 0     |

  Scenario: Create a numerical question with the locale specific variables
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber    | modattempts |
      | lesson     | Empty lesson name      | C1     | lesson2     | 1           |
    And I am on the "Empty lesson name" "lesson activity" page logged in as teacher1
    When I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Second question |
      | Page contents | 2 + 2? |
      | id_answer_editor_0 | 4#87 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 4#1:4#8 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
      | id_score_1 | 0 |
    And I press "Save page"
    And I follow "Second question"
    Then I should see "4#87"
    And I should see "4#1:4#8"

  Scenario: Edit a numerical question with the locale specific variables
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I edit the lesson
    And I follow "Hardest question ever"
    Then I should see "2#87"
    And I should see "2#1:2#8"

  Scenario: View the detailed page of lesson
    Given I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I edit the lesson
    And I select edit type "Expanded"
    Then I should see "2#87"
    And I should see "2#1:2#8"

  Scenario: Attempt the lesson successfully as a student
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#87 |
    And I press "Submit"
    Then I should see "Correct answer"
    And I should see "2#87"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."

  Scenario: Attempt the lesson unsuccessfully as a student
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#7 |
    And I press "Submit"
    Then I should not see "Correct answer"
    And I should see "2#7"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."

  Scenario: Attempt the lesson successfully as a student and review
    Given I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2#87 |
    And I press "Submit"
    Then I should see "Correct answer"
    And I should see "2#87"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
    And I follow "Review lesson"
    Then I should see "1 + 1?"
    And the following fields match these values:
      | Your answer | 2#87 |

  Scenario: Edit lesson question page with updated locale setting and wrong answer
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | ,     |
    And I log in as "teacher1"
    When I am on the "Test lesson name" "lesson activity" page
    And I edit the lesson
    And I follow "Hardest question ever"
    Then I should see "2,87"
    And I should see "2,1:2,8"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2,7 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I should see "2,7"
    And I should not see "Correct answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
