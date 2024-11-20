@mod @mod_quiz
Feature: Preview a quiz as a teacher
  In order to verify my quizzes are ready for my students
  As a teacher
  I need to be able to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | One      | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And user "teacher" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |

  @javascript
  Scenario: Review the quiz attempt
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher"
    And I follow "Review"
    Then I should see "25.00 out of 100.00"
    And I should see "v1 (latest)" in the "Question 1" "question"
    And I follow "Finish review"
    And "Review" "link" in the "Attempt 1" "list_item" should be visible

  @javascript
  Scenario: Review the quiz attempt with custom decimal separator
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher"
    And I follow "Review"
    Then I should see "1#00/4#00"
    And I should see "25#00 out of 100#00"
    And I should see "Mark 1#00 out of 1#00"
    And I follow "Finish review"
    And "Review" "link" in the "Attempt 1" "list_item" should be visible

  Scenario: Preview the quiz
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "teacher"
    When I press "Preview quiz"
    Then I should see "Question 1"
    And I should see "v1 (latest)" in the "Question 1" "question"
    And "Start a new preview" "button" should exist

  Scenario: Teachers should see a notice if the quiz is not available to students
    Given the following "activities" exist:
      | activity   | name   | course | timeclose     |
      | quiz       | Quiz 2 | C1     | ##yesterday## |
    And quiz "Quiz 2" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    When I am on the "Quiz 2" "mod_quiz > View" page logged in as "admin"
    And I should see "This quiz is currently not available."
    And I press "Preview quiz"
    Then I should see "if this were a real attempt, you would be blocked" in the ".alert-warning" "css_element"

  Scenario: Admins should be able to preview a quiz
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "admin"
    When I press "Preview quiz"
    Then I should see "Question 1"
    And "Start a new preview" "button" should exist

  @javascript
  Scenario: Teacher responses should be cleared after updating the question too much in the preview.
    Given the following "activities" exist:
      | activity | name   | course |
      | quiz     | Quiz 3 | C1     |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | questiontext |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four  |
    And quiz "Quiz 3" contains the following questions:
      | question         | page |
      | Multi-choice-002 | 1    |
    When I am on the "Quiz 3" "mod_quiz > View" page logged in as "teacher"
    And I press "Preview quiz"
    And I should see "one_of_four"
    And I should see "v1 (latest)"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Finish attempt ..."
    And I press "Return to attempt"
    And I click on "Edit question" "link" in the "Question 1" "question"
    And I set the field "Question text" to "one_of_four version 2"
    And I set the field "Choice 4" to ""
    And I press "id_submitbutton"
    Then I should see "one_of_four version 2"
    And I should see "v2 (latest)"
    And I should see "One"
    And I should see "Two"
    And I should see "Three"
    And I should not see "Four"
    And "input[type=checkbox][name$=choice0]:checked" "css_element" should not exist
    And "input[type=checkbox][name$=choice1]:checked" "css_element" should not exist
    And "input[type=checkbox][name$=choice2]:checked" "css_element" should not exist
