@mod @mod_quiz @quiz @quiz_overview
Feature: Grades report for a quiz with multiple grade items
  In to get an overview of quiz attempt grade
  As a teacher
  I need the Grades report to show all grade items

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  |
      | student  | Lorna     | Lott      |
      | teacher  | Mark      | Allwright |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
      | teacher | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity | name      | course |
      | quiz     | Test quiz | C1     |

  @javascript
  Scenario: Quiz grades report with multiple grade items
    Given the following "questions" exist:
      | questioncategory | qtype     | name      | questiontext       |
      | Test questions   | truefalse | Reading   | Can you read this? |
      | Test questions   | truefalse | Listening | Can you hear this? |
    And the following "mod_quiz > grade items" exist:
      | quiz   | name      |
      | Test quiz | Reading   |
      | Test quiz | Listening |
    And quiz "Test quiz" contains the following questions:
      | question  | page | grade item |
      | Reading   | 1    | Reading    |
      | Listening | 1    | Listening  |
    And user "student" has attempted "Test quiz" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    When I am on the "Test quiz" "mod_quiz > Grades report" page logged in as teacher
    Then "Lorna LottReview attempt" row "Grade/100.00" column of "attempts" table should contain "50.00"
    And "Lorna LottReview attempt" row "Q. 1/50.00" column of "attempts" table should contain "0.00"
    And "Lorna LottReview attempt" row "Q. 2/50.00" column of "attempts" table should contain "0.00"
    And "Lorna LottReview attempt" row "Reading/1.00" column of "attempts" table should contain "1.00"
    And "Lorna LottReview attempt" row "Listening/1.00" column of "attempts" table should contain "0.00"
    # Main thing to check here is that sorting does not give a fatal error
    And I click on "//a[@aria-label='Sort by Reading/1.00 ascending']" "xpath_element" in the "attempts" "table"
    And "Lorna LottReview attempt" row "Listening/1.00" column of "attempts" table should contain "0.00"
