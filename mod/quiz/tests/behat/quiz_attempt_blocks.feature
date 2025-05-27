@mod @mod_quiz
Feature: Blocks can be displayed on quiz attempt pages
  In order to see blocks on quiz attempt pages
  As a teacher
  I need to be able to set blocks display settings for quiz

  Background:
    Given the following "users" exist:
      | username |
      | student  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |

  Scenario Outline: Blocks display on quiz attempt pages can be toggled
    Given the following "activities" exist:
      | activity | course | name   | idnumber | showblocks     |
      | quiz     | C1     | Quiz 1 | q1       | <blockdisplay> |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | q1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name |
      | Test questions   | truefalse | TF1  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And the following "blocks" exist:
      | blockname | contextlevel    | reference | pagetypepattern  | defaultregion |
      | comments  | Activity module | q1        | mod-quiz-attempt | side-pre      |
    When I am on the "Quiz 1" "quiz activity" page logged in as student
    And I press "Attempt quiz"
    Then "Comments" "block" <blockvisibility> exist

    Examples:
      | blockdisplay | blockvisibility |
      | 1            | should          |
      | 0            | should not      |
