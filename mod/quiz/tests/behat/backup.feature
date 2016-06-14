@mod @mod_quiz
Feature: Backup and restore of quizzes
  In order to reuse my quizzes
  As a teacher
  I need to be able to back them up and restore them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And I log in as "admin"

  @javascript
  Scenario: Duplicate a quiz with two questions
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | For testing backup | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | First question  |
      | Test questions   | truefalse   | TF2  | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
    And I am on site homepage
    When I follow "Course 1"
    And I turn editing mode on
    And I duplicate "Quiz 1" activity editing the new copy with:
      | Name | Quiz 2 |
    And I follow "Quiz 2"
    And I follow "Edit quiz"
    Then I should see "TF1"
    And I should see "TF2"

  @javascript @_file_upload
  Scenario: Restore a Moodle 2.8 quiz backup
    And I am on site homepage
    When I follow "Course 1"
    And I navigate to "Restore" node in "Course administration"
    And I press "Manage backup files"
    And I upload "mod/quiz/tests/fixtures/moodle_28_quiz.mbz" file to "Files" filemanager
    And I press "Save changes"
    And I restore "moodle_28_quiz.mbz" backup into "Course 1" course using this options:
    And I follow "Restored Moodle 2.8 quiz"
    And I follow "Edit quiz"
    Then I should see "TF1"
    And I should see "TF2"
