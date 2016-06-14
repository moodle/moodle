@mod @mod_quiz
Feature: Allow students to redo questions in a practice quiz, without starting a whole new attempt
  In order to practice particular skills I am struggling with
  As a student
  I need to be able to redo each question in a quiz as often as necessary without starting a whole new attempt, if my teacher allows it.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
      | teacher  | Teacher   | One      | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student  | C1     | student |
      | teacher  | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour | canredoquestions |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  | 1                |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    | 2       |
      | TF2      | 1    | 1       |
    And I log in as "student"
    And I follow "Course 1"

  @javascript
  Scenario: After completing a question, there is a redo question button that restarts the question
    When I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "First question" "question"
    And I click on "Check" "button" in the "First question" "question"
    And I press "Redo question"
    Then the state of "First question" question is shown as "Not complete"
    And I should see "Marked out of 2.00" in the "First question" "question"

  @javascript
  Scenario: The redo question button is visible but disabled for teachers
    When I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "First question" "question"
    And I click on "Check" "button" in the "First question" "question"
    And I log out
    And I log in as "teacher"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Attempts: 1"
    And I follow "Review attempt"
    Then the "Redo question" "button" should be disabled

  @javascript
  Scenario: The redo question buttons are no longer visible after the attempt is submitted.
    When I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "First question" "question"
    And I click on "Check" "button" in the "First question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    Then "Redo question" "button" should not exist

  @javascript @_switch_window
  Scenario: Teachers reviewing can see all the qestions attempted in a slot
    When I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "First question" "question"
    And I click on "Check" "button" in the "First question" "question"
    And I press "Redo question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I log out
    And I log in as "teacher"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Attempts: 1"
    And I follow "Review attempt"
    And I click on "1" "link" in the "First question" "question"
    And I switch to "reviewquestion" window
    Then the state of "First question" question is shown as "Incorrect"
    And I click on "1" "link" in the "First question" "question"
    And the state of "First question" question is shown as "Not complete"
    And I switch to the main window
    And the state of "First question" question is shown as "Not answered"
    And I should not see "Submit" in the ".history" "css_element"
    And I navigate to "Statistics" node in "Quiz administration > Results"
    And I follow "TF1"
    And "False" row "Frequency" column of "quizresponseanalysis" table should contain "100.00%"
    And "True" row "Frequency" column of "quizresponseanalysis" table should contain "0.00%"
    And "[No response]" row "Frequency" column of "quizresponseanalysis" table should contain "100.00%"

  @javascript
  Scenario: Redoing question 1 should save any changes to question 2 on the same page
    When I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "False" "radio" in the "First question" "question"
    And I click on "Check" "button" in the "First question" "question"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Redo question"
    And I click on "Check" "button" in the "Second question" "question"
    Then the state of "Second question" question is shown as "Correct"
