@qtype @qtype_shortanswer
Feature: Test duplicating a quiz containing a Short answer question
  As a teacher
  In order re-use my courses containing Short answer questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name            | template |
      | Test questions   | shortanswer | shortanswer-001 | frogtoad |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | shortanswer-001 | 1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Backup and restore a course containing a Short answer question
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    And I navigate to "Question bank" in current page administration
    And I choose "Edit question" action for "shortanswer-001" in the question bank
    Then the following fields match these values:
      | Question name        | shortanswer-001                                   |
      | Question text        | Name an amphibian: __________                     |
      | General feedback     | Generalfeedback: frog or toad would have been OK. |
      | Default mark         | 1                                                 |
      | Case sensitivity     | No, case is unimportant                           |
      | id_answer_0          | frog                                              |
      | id_fraction_0        | 100%                                              |
      | id_feedback_0        | Frog is a very good answer.                       |
      | id_answer_1          | toad                                              |
      | id_fraction_1        | 80%                                               |
      | id_feedback_1        | Toad is an OK good answer.                        |
      | id_answer_2          | *                                                 |
      | id_fraction_2        | None                                              |
      | id_feedback_2        | That is a bad answer.                             |
