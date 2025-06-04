@mod @mod_quiz
Feature: Edit quiz page - pagination
  In order to build a quiz laid out in pages the way I want
  As a teacher
  I need to be able to add and remove pages, and repaginate.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
      | student1 | S1        | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name   | course | idnumber | questionsperpage | navmethod  |
      | quiz       | Quiz 1 | C1     | quiz1    | 0                | sequential |
    And the following "questions" exist:
      | questioncategory   | qtype     | name | questiontext    |
      | Default for Quiz 1 | truefalse | 1    | Question1 text  |
      | Default for Quiz 1 | truefalse | 2    | Question2 text  |
      | Default for Quiz 1 | truefalse | 3    | Question3 text  |
      | Default for Quiz 1 | truefalse | 4    | Question4 text  |
      | Default for Quiz 1 | truefalse | 5    | Question5 text  |
      | Default for Quiz 1 | truefalse | 6    | Question6 text  |
      | Default for Quiz 1 | truefalse | 7    | Question7 text  |
      | Default for Quiz 1 | truefalse | 8    | Question8 text  |
      | Default for Quiz 1 | truefalse | 9    | Question9 text  |
      | Default for Quiz 1 | truefalse | 10   | Question10 text |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | 1        | 1    |
      | 2        | 1    |
      | 3        | 1    |
      | 4        | 1    |
      | 5        | 1    |
      | 6        | 1    |
      | 7        | 1    |
      | 8        | 1    |
      | 9        | 1    |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as teacher1

  @javascript
  Scenario: Repaginate questions using "add page break" or "Remove page break" icons
    # Start repaginating.
    When I click on the "Add" page break icon after question "Question1 text"
    # Confirm that only Question 1 is on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    # Confirm that Question 2 ~ Question 9 are on quiz page 2.
    And I should see "Question2 text" on quiz page "2"
    And I should see "Question3 text" on quiz page "2"
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "2"
    And I should see "Question6 text" on quiz page "2"
    And I should see "Question7 text" on quiz page "2"
    And I should see "Question8 text" on quiz page "2"
    And I should see "Question9 text" on quiz page "2"

    When I click on the "Remove" page break icon after question "Question1 text"
    # Confirm that all questions are on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question4 text" on quiz page "1"
    And I should see "Question5 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    And I should see "Question7 text" on quiz page "1"
    And I should see "Question8 text" on quiz page "1"
    And I should see "Question9 text" on quiz page "1"
    # Confirm that there is no second page.
    And I should not see "Page 2" in the "region-main" "region"

    # Add a question.
    And I open the "last" add to quiz menu
    And I choose "from question bank" in the open action menu
    When I click on "Add to quiz" "link" in the "Question10 text" "table_row"
    # Confirm that all questions are on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question4 text" on quiz page "1"
    And I should see "Question5 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    And I should see "Question7 text" on quiz page "1"
    And I should see "Question8 text" on quiz page "1"
    And I should see "Question9 text" on quiz page "1"
    And I should see "Question10 text" on quiz page "1"
    # Confirm that there is no second page.
    And I should not see "Page 2" in the "region-main" "region"

    When I click on the "Add" page break icon after question "Question2 text"
    # Confirm that Question 1 and Question 2 are on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    # Confirm that Question 3 ~ Question 10 are on quiz page 2.
    And I should see "Question3 text" on quiz page "2"
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "2"
    And I should see "Question6 text" on quiz page "2"
    And I should see "Question7 text" on quiz page "2"
    And I should see "Question8 text" on quiz page "2"
    And I should see "Question9 text" on quiz page "2"
    And I should see "Question10 text" on quiz page "2"
    # Confirm that there is no third page.
    And I should not see "Page 3" in the "region-main" "region"

    When I click on the "Add" page break icon after question "Question1 text"
    # Confirm that only Question 1 is on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    # Confirm that only Question 2 is on quiz page 2.
    And I should see "Question2 text" on quiz page "2"
    # Confirm that Question 3 ~ Question 10 are on quiz page 3.
    And I should see "Question3 text" on quiz page "3"
    And I should see "Question4 text" on quiz page "3"
    And I should see "Question5 text" on quiz page "3"
    And I should see "Question6 text" on quiz page "3"
    And I should see "Question7 text" on quiz page "3"
    And I should see "Question8 text" on quiz page "3"
    And I should see "Question9 text" on quiz page "3"
    And I should see "Question10 text" on quiz page "3"

    When I click on the "Remove" page break icon after question "Question2 text"
    # Confirm that only Question 1 is on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    # Confirm that Question 2 ~ Question 10 are on quiz page 2.
    And I should see "Question2 text" on quiz page "2"
    And I should see "Question3 text" on quiz page "2"
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "2"
    And I should see "Question6 text" on quiz page "2"
    And I should see "Question7 text" on quiz page "2"
    And I should see "Question8 text" on quiz page "2"
    And I should see "Question9 text" on quiz page "2"
    And I should see "Question10 text" on quiz page "2"
    # Confirm that there is no third page.
    And I should not see "Page 3" in the "region-main" "region"

    When I click on the "Remove" page break icon after question "Question1 text"
    # Confirm that all questions are on quiz page 1.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question4 text" on quiz page "1"
    And I should see "Question5 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    And I should see "Question7 text" on quiz page "1"
    And I should see "Question8 text" on quiz page "1"
    And I should see "Question9 text" on quiz page "1"
    And I should see "Question10 text" on quiz page "1"
    # Confirm that there is no second page.
    And I should not see "Page 2" in the "region-main" "region"

    # Repaginate to 1 question per page.
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "1"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    # Confirm there is only 1 question per page - maximum of 10 pages.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "2"
    And I should see "Question3 text" on quiz page "3"
    And I should see "Question4 text" on quiz page "4"
    And I should see "Question5 text" on quiz page "5"
    And I should see "Question6 text" on quiz page "6"
    And I should see "Question7 text" on quiz page "7"
    And I should see "Question8 text" on quiz page "8"
    And I should see "Question9 text" on quiz page "9"
    And I should see "Question10 text" on quiz page "10"
    # Confirm that there is no eleventh page.
    And I should not see "Page 11" in the "region-main" "region"

    # Repaginate to 2 questions per page.
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "2"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    # Confirm there are only 2 questions per page - maximum of 5 pages.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "2"
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "3"
    And I should see "Question6 text" on quiz page "3"
    And I should see "Question7 text" on quiz page "4"
    And I should see "Question8 text" on quiz page "4"
    And I should see "Question9 text" on quiz page "5"
    And I should see "Question10 text" on quiz page "5"
    # Confirm that there is no sixth page.
    And I should not see "Page 6" in the "region-main" "region"

    When I click on the "Add" page break icon after question "Question9 text"
    # Confirm there are only 2 questions per page until Question 8.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "2"
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "3"
    And I should see "Question6 text" on quiz page "3"
    And I should see "Question7 text" on quiz page "4"
    And I should see "Question8 text" on quiz page "4"
    # Confirm only Question 9 is on quiz page 5.
    And I should see "Question9 text" on quiz page "5"
    # Confirm Question 10 is now on quiz page 6.
    And I should see "Question10 text" on quiz page "6"

    # Repaginate with unlimited questions per page (All questions on Page 1).
    When I press "Repaginate"
    And I set the field "menuquestionsperpage" to "Unlimited"
    And I click on "Go" "button" in the "Repaginate" "dialogue"
    # Confirm all questions are on page 1.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question4 text" on quiz page "1"
    And I should see "Question5 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    And I should see "Question7 text" on quiz page "1"
    And I should see "Question8 text" on quiz page "1"
    And I should see "Question9 text" on quiz page "1"
    And I should see "Question10 text" on quiz page "1"
    # Confirm that there is no second page.
    And I should not see "Page 2" in the "region-main" "region"

  @javascript
  Scenario: Teacher can repaginate questions using the move icon
    # Add page breaks.
    Given I click on the "Add" page break icon after question "Question3 text"
    And I click on the "Add" page break icon after question "Question7 text"
    # Move Question 6 after Question 3 using the move icon.
    When I move "Question6 text" to "After Question 3" in the quiz by clicking the move icon
    # Confirm that Question 6 moved to page 1 after Question 3.
    Then I should see "Question1 text" on quiz page "1"
    And I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    # Confirm that Question 4, 5 and 7 remain on page 2.
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "2"
    And I should see "Question7 text" on quiz page "2"
    # Confirm that Question 8 and 9 remain on page 3.
    And I should see "Question8 text" on quiz page "3"
    And I should see "Question9 text" on quiz page "3"
    # Move Question 7 and 1 after Question 9 using the move icon.
    When I move "Question7 text" to "After Question 9" in the quiz by clicking the move icon
    And I move "Question1 text" to "After Question 9" in the quiz by clicking the move icon
    # Confirm that Question 2, 3 and 6 remain on page 1.
    Then I should see "Question2 text" on quiz page "1"
    And I should see "Question3 text" on quiz page "1"
    And I should see "Question6 text" on quiz page "1"
    # Confirm that Question 4 and 5 remain on page 2.
    And I should see "Question4 text" on quiz page "2"
    And I should see "Question5 text" on quiz page "2"
    # Confirm that Question 8 and 9 remain on page 3.
    And I should see "Question8 text" on quiz page "3"
    And I should see "Question9 text" on quiz page "3"
    # Confirm that Question 7 and 1 moved to page 3 after Question 9.
    And I should see "Question7 text" on quiz page "3"
    And I should see "Question1 text" on quiz page "3"

  @javascript
  Scenario: Quiz questions are displayed on specified page when previewed and attempted
    # Add page breaks.
    Given I click on the "Add" page break icon after question "Question3 text"
    And I click on the "Add" page break icon after question "Question7 text"
    # Move Question 1 after Question 9 .
    And I move "Question1 text" to "After Question 9" in the quiz by clicking the move icon
    And I am on the "Quiz 1" "quiz activity" page
    When I press "Preview quiz"
    # Confirm the questions displayed per page when quiz is previewed.
    Then I should see "Question2 text"
    And I should see "Question3 text"
    And I press "Next page"
    And I should see "Question4 text"
    And I should see "Question5 text"
    And I should see "Question6 text"
    And I should see "Question7 text"
    And I press "Next page"
    And I should see "Question8 text"
    And I should see "Question9 text"
    And I should see "Question1 text"
    When I am on the "Quiz 1" "quiz activity" page logged in as student1
    # Confirm the questions displayed per page when quiz is attempted.
    And I press "Attempt quiz"
    Then I should see "Question2 text"
    And I should see "Question3 text"
    And I press "Next page"
    And I should see "Question4 text"
    And I should see "Question5 text"
    And I should see "Question6 text"
    And I should see "Question7 text"
    And I press "Next page"
    And I should see "Question8 text"
    And I should see "Question9 text"
    And I should see "Question1 text"
