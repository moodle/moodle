@mod_quiz @quizaccess @quizaccess_seb
Feature: View the activity header when Safe Exam Browser is required
  In order to correctly identify the quiz when Safe Exam Browser is required
  As a student
  I need to be able to see the quiz information in the activity header

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext |
      | Test questions   | truefalse | TF1  | TF question  |
    And the following "activity" exists:
      | activity                    | quiz                  |
      | course                      | C1                    |
      | idnumber                    | 00001                 |
      | name                        | Test quiz name        |
      | intro                       | Test quiz description |
      | seb_requiresafeexambrowser  | 1                     |
      | grade                       | 10                    |
    And quiz "Test quiz name" contains the following questions:
      | question | page |
      | TF1      | 1    |

  Scenario: Quiz description is displayed when Safe Exam Browser is required
    When I am on the "Test quiz name" "quiz activity" page logged in as student1
    Then I should see "Launch Safe Exam Browser"
    And "Test quiz name" "heading" should exist
    And I should see "Test quiz description"
