@mod @mod_lesson
Feature: In a lesson activity, students can review the answers they gave to questions
  To review questions of a lesson
  As a student
  I need to complete a lesson answering all of the questions.

  Scenario: Student answers questions and then reviews them.
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
    And the following "activity" exists:
      | activity    | lesson           |
      | name        | Test lesson name |
      | course      | C1               |
      | idnumber    | lesson1          |
      # Display ongoing score = Yes
      | ongoing     | 1                |
      # Slideshow = Yes
      | slideshow   | 1                |
      # Maximum number of answers = 10
      | maxanswers  | 10               |
      # Allow student review = Yes
      | modattempts | 1                |
      # Maximum number of attempts per question
      | maxattempts | 3                |
      # Custom scoring = No
      | custom      | 0                |
      # Re-takes allowed = Yes
      | retake      | 1                |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | numeric   | Hardest question ever | 1 + 1?                    |
      | Test lesson name | truefalse | Next question         | Paper is made from trees. |
    And the following "mod_lesson > answers" exist:
      | page                  | answer | response           | jumpto    | score |
      | Hardest question ever | 2      | Correct answer 1   | Next page | 1     |
      | Hardest question ever | 1      | Incorrect answer 1 | This page | 0     |
      | Next question         | True   | Correct answer 2   | Next page | 1     |
      | Next question         | False  | Incorrect answer 2 | This page | 0     |
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "You have answered 0 correctly out of 0 attempts."
    And I should see "1 + 1?"
    And I wait "1" seconds
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "You have answered 0 correctly out of 1 attempts."
    And I press "Continue"
    And I should see "1 + 1?"
    And I wait "1" seconds
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "You have answered 1 correctly out of 2 attempts."
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I wait "1" seconds
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I should see "Paper is made from trees."
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 2"
    And I should see "Number of correct answers: 2"
    And I should see "Your score is 2 (out of 3)."
    When I follow "Review lesson"
    Then I should see "You have answered 2 correctly out of 3 attempts."
    And I should see "1 + 1?"
    And I press "Next page"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I should see "1 + 1?"
    And I should see "Correct answer 1"
    And I press "Continue"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I should see "Paper is made from trees."
    And I press "Next page"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I should see "Paper is made from trees."
    And I should see "Correct answer 2"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
