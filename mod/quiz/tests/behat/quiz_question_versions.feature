@mod @mod_quiz
Feature: Quiz question versioning
  In order to manage question versions
  As a teacher
  I need to be able to choose which versions can be displayed in a quiz

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              | answer 1 |
      | Test questions   | truefalse | First question | Answer the first question | True     |
    And quiz "Quiz 1" contains the following questions:
      | question          | page |
      | First question    | 1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Approriate question version should be displayed when not edited
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I should see "First question"
    And I should see "Answer the first question"
    And I should see "v1 (latest)"
    # We check that the corresponding version is the appropriate one in preview
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    And I should see "Version 1 (latest)"
    And I should see "Answer the first question"
    And I press "Display options"
    And I set the following fields to these values:
      | id_feedback        | Not shown |
      | id_generalfeedback | Not shown |
      | id_rightanswer     | Shown     |
    And I press "id_saveupdate"
    And I click on "finish" "button"
    And I should see "The correct answer is 'True'."

  @javascript
  Scenario: Approriate question version should be displayed when edited
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I click on "Edit question First question" "link"
    # We edit the question with new informations to generate a second version
    And I set the following fields to these values:
      | id_name          | Second question                  |
      | id_questiontext  | This is the second question text |
      | id_correctanswer | False                            |
    And I press "id_submitbutton"
    And I set the field "version" to "v2"
    And I should see "Second question"
    And I should see "This is the second question text"
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    # We check that the corresponding version is the appropriate one in preview
    # We also check that the new informations are properly displayed
    And I should see "Version 2 (latest)"
    And I should see "This is the second question text"
    And I press "Display options"
    And I set the following fields to these values:
      | id_feedback        | Not shown |
      | id_generalfeedback | Not shown |
      | id_rightanswer     | Shown     |
    And I press "id_saveupdate"
    And I click on "finish" "button"
    Then I should see "The correct answer is 'False'."

  @javascript
  Scenario: Creating a new question should have always latest in the version selection
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I click on "Add" "link"
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 01 new"
    And I set the field "Question text" to "Please write 200 words about Essay 01"
    And I press "id_submitbutton"
    And I should see "Essay 01 new" on quiz page "1"
    And I should see "Always latest" on quiz page "1"

  @javascript
  Scenario: Adding a question from question bank should have always latest in the version selection
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I click on "Add" "link"
    And I follow "from question bank"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    And I should see "First question" on quiz page "1"
    And I should see "Always latest" on quiz page "1"
