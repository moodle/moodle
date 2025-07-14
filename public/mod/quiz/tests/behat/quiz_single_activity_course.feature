@mod @mod_quiz @format @format_singleactivity
Feature: Teacher can build quiz in a single activity format course
  In order to build a quiz in a single activity course format
  As a teacher
  I should be able to change course format

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student  | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype |
      | Course 1 | C1        | singleactivity | quiz         |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    # Create multiple question banks to have questions from different question banks.
    And the following "activities" exist:
      | activity | course | name            | idnumber |
      | quiz     | C1     | Quiz 1          | q1       |
      | qbank    | C1     | Question bank 1 | qbank1   |
      | qbank    | C1     | Question bank 2 | qbank2   |
    And the following "question categories" exist:
      | contextlevel    | reference | name              |
      | Activity module | q1        | Test questions    |
      | Activity module | qbank1    | Qbank questions 1 |
      | Activity module | qbank2    | Qbank questions 2 |
    And the following "questions" exist:
      | questioncategory  | qtype       | template    | name                    |
      | Test questions    | multichoice | one_of_four | MCQ1                    |
      | Test questions    | multichoice | one_of_four | MCQ2                    |
      | Qbank questions 1 | truefalse   |             | TFQ1                    |
      | Qbank questions 2 | truefalse   |             | TFQ2                    |
      | Test questions    | random      |             | Random (Test questions) |
    # Add questions from different question categories and question banks to the quiz.
    # Add at least 1 random question.
    And quiz "Quiz 1" contains the following questions:
      | question                | page |
      | MCQ1                    | 1    |
      | TFQ1                    | 1    |
      | TFQ2                    | 2    |
      | Random (Test questions) | 3    |

  @javascript
  Scenario: Student can preview and answer quiz in a single activity format course
    Given I am on the "Quiz 1" "quiz activity" page logged in as student
    # By attempting the quiz, it can be confirmed that the quiz questions have been added successfully.
    When I press "Attempt quiz"
    And I click on "One" "qtype_multichoice > Answer"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Next page"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Next page"
    And I click on "Three" "qtype_multichoice > Answer"
    And I press "Finish attempt ..."
    # Confirm that answers are successfully saved.
    And I should see "Answer saved"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    # Confirm that quiz is finished successfully.
    Then I should see "Finished" in the "Status" "table_row"
