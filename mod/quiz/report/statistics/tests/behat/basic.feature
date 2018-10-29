@mod @mod_quiz @quiz @quiz_statistics
Feature: Basic use of the Statistics report
  In order to see how my students are progressing
  As a teacher
  I need to see all their quiz responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext        |
      | Test questions   | truefalse | Question A | This is question 01 |
      | Test questions   | truefalse | Question B | This is question 02 |
      | Test questions   | truefalse | Question C | This is question 03 |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 1    |
      | Question C | 2    |

  @javascript
  Scenario: Report works when there are no attempts
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Results > Statistics" in current page administration
    Then I should see "No attempts have been made at this quiz, or all attempts have questions that need manual grading."
    And I should not see "Statistics for question positions"
    And "Show chart data" "link" should not exist
    When user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
      |   3  | False    |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | True     |
      |   3  | True     |
    And user "student3" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | False    |
      |   2  | False    |
      |   3  | False    |
    And I press "Show report"
    Then I should not see "No attempts have been made at this quiz, or all attempts have questions that need manual grading."
    And "Show chart data" "link" should exist

    # Question A statistics breakdown.
    And "1" row "Question name" column of "questionstatistics" table should contain "Question A"
    And "1" row "Attempts" column of "questionstatistics" table should contain "3"
    And "1" row "Facility index" column of "questionstatistics" table should contain "66.67%"
    And "1" row "Standard deviation" column of "questionstatistics" table should contain "57.74%"
    And "1" row "Random guess score" column of "questionstatistics" table should contain "50.00%"
    And "1" row "Intended weight" column of "questionstatistics" table should contain "33.33%"
    And "1" row "Effective weight" column of "questionstatistics" table should contain "30.90%"
    And "1" row "Discrimination index" column of "questionstatistics" table should contain "50.00%"

    # Question B statistics breakdown.
    And "2" row "Question name" column of "questionstatistics" table should contain "Question B"
    And "2" row "Attempts" column of "questionstatistics" table should contain "3"
    And "2" row "Facility index" column of "questionstatistics" table should contain "33.33%"
    And "2" row "Standard deviation" column of "questionstatistics" table should contain "57.74%"
    And "2" row "Random guess score" column of "questionstatistics" table should contain "50.00%"
    And "2" row "Intended weight" column of "questionstatistics" table should contain "33.33%"
    And "2" row "Effective weight" column of "questionstatistics" table should contain "34.55%"
    And "2" row "Discrimination index" column of "questionstatistics" table should contain "86.60%"

    # Question C statistics breakdown.
    And "3" row "Question name" column of "questionstatistics" table should contain "Question C"
    And "3" row "Attempts" column of "questionstatistics" table should contain "3"
    And "3" row "Facility index" column of "questionstatistics" table should contain "33.33%"
    And "3" row "Standard deviation" column of "questionstatistics" table should contain "57.74%"
    And "3" row "Random guess score" column of "questionstatistics" table should contain "50.00%"
    And "3" row "Intended weight" column of "questionstatistics" table should contain "33.33%"
    And "3" row "Effective weight" column of "questionstatistics" table should contain "34.55%"
    And "3" row "Discrimination index" column of "questionstatistics" table should contain "86.60%"
