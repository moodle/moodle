@mod @mod_quiz
Feature: Quiz question versioning
  In order to manage question versions
  As a teacher
  I need to be able to choose which versions can be displayed in a quiz

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher  | Teacher   | 1        | teacher@example.com |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | quiz     | Quiz 1  | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
      | Test questions   | truefalse | Other question | Answer the first question |
    And quiz "Quiz 1" contains the following questions:
      | question          | page |
      | First question    | 1    |

  @javascript
  Scenario: Appropriate question version should be displayed when not edited
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    Then I should see "First question"
    And I should see "Answer the first question"
    And the field "version" matches value "Always latest"
    And the "version" select box should contain "v1 (latest)"
    # We check that the corresponding version is the appropriate one in preview
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    And I should see "Version 1 (latest)"
    And I should see "Answer the first question"
    And I click on "Submit and finish" "button"
    And I should see "You should have selected true."

  @javascript
  Scenario: Approriate question version should be displayed when edited
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    And I click on "Edit question First question" "link"
    # We edit the question with new informations to generate a second version
    And I set the following fields to these values:
      | Question name  | First question (v2)           |
      | Question text  | Answer the new first question |
      | Correct answer | False                         |
    And I press "id_submitbutton"
    And the field "version" matches value "Always latest"
    And the "version" select box should contain "v1"
    And I set the field "version" to "v2 (latest)"
    Then I should see "First question (v2)"
    And I should see "Answer the new first question"
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    # We check that the corresponding version is the appropriate one in preview
    # We also check that the new information is properly displayed
    And I should see "Version 2 (latest)"
    And I should see "Answer the new first question"

  @javascript
  Scenario: Appropriate question version displayed when later draft version exists
    # Edit the question in the question bank to add a new draft version.
    Given I am on the "First question" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name   | First question (v2)           |
      | Question text   | Answer the new first question |
      | Correct answer  | False                         |
      | Question status | Draft                         |
    And I press "id_submitbutton"
    When I am on the "Quiz 1" "mod_quiz > Edit" page
    Then I should see "First question"
    And I should see "Answer the first question"
    And the field "version" matches value "Always latest"
    And the "version" select box should contain "v1 (latest)"
    And the "version" select box should not contain "v2"
    And the "version" select box should not contain "v2 (latest)"
    And I am on the "Quiz 1" "mod_quiz > View" page
    And I press "Preview quiz"
    And I should see "Answer the first question"

  @javascript
  Scenario: Creating a new question should have always latest in the version selection
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    # Change the version of the existing question, to ensure it does not match later.
    And I set the field "version" to "v1 (latest)"
    And I open the "Page 1" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    And I set the following fields to these values:
      | Question name   | New essay                      |
      | Question text   | Write 200 words about quizzes. |
    And I press "id_submitbutton"
    And I should see "New essay" on quiz page "1"
    And the field "version" in the "New essay" "list_item" matches value "Always latest"

  @javascript
  Scenario: Adding a question from question bank should have always latest in the version selection
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    And I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I click on "Select" "checkbox" in the "Other question" "table_row"
    And I press "Add selected questions to the quiz"
    Then I should see "Other question" on quiz page "1"
    And the field "version" in the "Other question" "list_item" matches value "Always latest"

  @javascript
  Scenario: Adding a question where all available versions are drafts should display a helpful message.
    Given quiz "Quiz 1" contains the following questions:
      | question       | page |
      | First question | 1    |
    And I am on the "Quiz 1" "mod_quiz > Question bank" page logged in as teacher
    And I set the field "question_status_dropdown" in the "First question" "table_row" to "Draft"
    When I am on the "Quiz 1" "mod_quiz > Edit" page
    Then I should see "This question is in draft status. To use it in the quiz, go to the question bank and change the status to ready."

  @javascript
  Scenario: Previewing a question set to use always latest version will set the preview to always latest version
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    And the field "version" in the "First question" "list_item" matches value "Always latest"
    When I follow "Preview question"
    And I expand all fieldsets
    Then the field "Question version" matches value "Always latest"

  @javascript
  Scenario: Previewing a question set to use a specific version will set the preview to that version
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    And I set the field "version" to "v1 (latest)"
    When I follow "Preview question"
    And I expand all fieldsets
    Then the field "Question version" matches value "1"
