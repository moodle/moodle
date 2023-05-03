@mod @mod_quiz
Feature: Edit quiz page - remove multiple questions
  In order to change the layout of a quiz I built efficiently
  As a teacher
  I need to be able to delete many questions questions.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And I log in as "teacher1"

  @javascript
  Scenario: Delete selected question using select multiple items feature.
    Given the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext        |
      | Test questions   | description | Info       | Some instructions   |
      | Test questions   | truefalse   | Question A | This is question 01 |
      | Test questions   | truefalse   | Question B | This is question 02 |
      | Test questions   | truefalse   | Question C | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Info       | 1    |
      | Question A | 1    |
      | Question B | 1    |
      | Question C | 2    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    # Confirm the starting point.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Total of marks: 3.00"
    And I should see "Questions: 4"
    And I should see "This quiz is open"

    # Delete last question in last page. Page contains multiple questions. No reordering.
    When I click on "Select multiple items" "button"
    Then I click on "selectquestion-3" "checkbox"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"

    Then I should see "Info" on quiz page "1"
    And I should see "Question A" on quiz page "1"
    And I should not see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Total of marks: 2.00"
    And I should see "Questions: 3"

  @javascript
  Scenario: Delete first selected question using select multiple items feature.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext        |
      | Test questions   | truefalse | Question A | This is question 01 |
      | Test questions   | truefalse | Question B | This is question 02 |
      | Test questions   | truefalse | Question C | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 2    |
      | Question C | 2    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

  # Confirm the starting point.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "2"
    And I should see "Question C" on quiz page "2"
    And I should see "Total of marks: 3.00"
    And I should see "Questions: 3"
    And I should see "This quiz is open"

  # Delete first question in first page. Page contains multiple questions. No reordering.
    When I click on "Select multiple items" "button"
    Then I click on "selectquestion-1" "checkbox"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"

    Then I should not see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "1"
    And I should see "Total of marks: 2.00"
    And I should see "Questions: 2"

  @javascript
  Scenario: Can delete the last question in a quiz.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext        |
      | Test questions   | truefalse | Question A | This is question 01 |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    When I click on "Select multiple items" "button"
    And I click on "selectquestion-1" "checkbox"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then I should see "Questions: 0"

  @javascript
  Scenario: Delete all questions by checking select all.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext        |
      | Test questions   | truefalse | Question A | This is question 01 |
      | Test questions   | truefalse | Question B | This is question 02 |
      | Test questions   | truefalse | Question C | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 1    |
      | Question C | 2    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    # Confirm the starting point.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"
    And I should see "Total of marks: 3.00"
    And I should see "Questions: 3"
    And I should see "This quiz is open"

    # Delete all questions in page. Page contains multiple questions
    When I click on "Select multiple items" "button"
    Then I press "Select all"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"

    Then I should not see "Question A" on quiz page "1"
    And I should not see "Question B" on quiz page "1"
    And I should not see "Question C" on quiz page "2"
    And I should see "Total of marks: 0.00"
    And I should see "Questions: 0"

  @javascript
  Scenario: Deselect all questions by checking deselect all.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext        |
      | Test questions   | truefalse | Question A | This is question 01 |
      | Test questions   | truefalse | Question B | This is question 02 |
      | Test questions   | truefalse | Question C | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 1    |
      | Question C | 2    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

  # Confirm the starting point.
    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "1"
    And I should see "Question C" on quiz page "2"

  # Delete last question in last page. Page contains multiple questions
    When I click on "Select multiple items" "button"
    And I press "Select all"
    Then the field "selectquestion-3" matches value "1"

    When I press "Deselect all"
    Then the field "selectquestion-3" matches value "0"

  @javascript
  Scenario: Delete multiple questions from sections
    Given the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext    |
      | Test questions   | truefalse   | Question A | First question  |
      | Test questions   | truefalse   | Question B | Second question |
      | Test questions   | truefalse   | Question C | Third question  |
      | Test questions   | truefalse   | Question D | Fourth question |
      | Test questions   | truefalse   | Question E | Fifth question  |
      | Test questions   | truefalse   | Question F | Sixth question  |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 2    |
      | Question C | 3    |
      | Question D | 4    |
      | Question E | 5    |
      | Question F | 6    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 2         | 0       |
      | Section 3 | 4         | 0       |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    When I click on "Select multiple items" "button"
    And I click on "selectquestion-3" "checkbox"
    And I click on "selectquestion-5" "checkbox"
    And I click on "selectquestion-6" "checkbox"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"

    Then I should see "Question A" on quiz page "1"
    And I should see "Question B" on quiz page "2"
    And I should see "Question D" on quiz page "3"
    And I should not see "Question C"
    And I should not see "Question E"
    And I should not see "Question F"

  @javascript
  Scenario: Attempting to delete all questions of a sections
    Given the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext    |
      | Test questions   | truefalse   | Question A | First question  |
      | Test questions   | truefalse   | Question B | Second question |
      | Test questions   | truefalse   | Question C | Third question  |
      | Test questions   | truefalse   | Question D | Fourth question |
      | Test questions   | truefalse   | Question E | Fifth question  |
      | Test questions   | truefalse   | Question F | Sixth question  |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 2    |
      | Question C | 3    |
      | Question D | 4    |
      | Question E | 5    |
      | Question F | 6    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 2         | 0       |
      | Section 3 | 4         | 0       |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    When I click on "Select multiple items" "button"
    And I click on "selectquestion-2" "checkbox"
    And I click on "selectquestion-3" "checkbox"
    And I click on "Delete selected" "button"

    Then I should see "Cannot remove questions"

  @javascript
  Scenario: Delete multiple random questions from sections.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext    |
      | Test questions   | truefalse | Question A | First question  |
      | Test questions   | truefalse | Question B | Second question |
      | Test questions   | truefalse | Question C | Third question  |
      | Test questions   | truefalse | Question D | Fourth question |
      | Test questions   | truefalse | Question E | Fifth question  |
      | Test questions   | truefalse | Question F | Sixth question  |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I set the field "Number of random questions" to "3"
    And I press "Add random question"
    And I click on "Select multiple items" "button"
    And I click on "selectquestion-1" "checkbox"
    And I click on "selectquestion-2" "checkbox"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    # To make sure question is deleted completely.
    And I reload the page
    Then I should see "Random (Test questions)" on quiz page "1"
    And I should not see "Random (Test questions)" on quiz page "2"
    And I should not see "Random (Test questions)" on quiz page "3"
    And I should see "Total of marks: 1.00"
    And I should see "Questions: 1"

  @javascript
  Scenario: Delete all random questions by checking select all.
    Given the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext    |
      | Test questions   | truefalse | Question A | First question  |
      | Test questions   | truefalse | Question B | Second question |
      | Test questions   | truefalse | Question C | Third question  |
      | Test questions   | truefalse | Question D | Fourth question |
      | Test questions   | truefalse | Question E | Fifth question  |
      | Test questions   | truefalse | Question F | Sixth question  |
    And I am on the "Quiz 1" "mod_quiz > Edit" page

    # Delete all questions in page. Page contains multiple questions.
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I set the field "Number of random questions" to "3"
    And I press "Add random question"
    And I click on "Select multiple items" "button"
    And I press "Select all"
    And I click on "Delete selected" "button"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
     # To make sure question is deleted completely.
    And I reload the page
    Then I should not see "Random (Test questions)" on quiz page "1"
    And I should not see "Random (Test questions)" on quiz page "2"
    And I should not see "Random (Test questions)" on quiz page "3"
    And I should see "Total of marks: 0.00"
    And I should see "Questions: 0"
