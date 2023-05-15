@qbank @qbank_comment @javascript
Feature: A Teacher can comment in a question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
      | teacher2 | T2        | Teacher2 | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  @javascript
  Scenario: Add a comment in question
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Select a category" to "Test questions"
    And I should see "0" on the comments column
    When I click "0" on the row on the comments column
    And I add "Super test comment 01" comment to question
    And I click on "Add comment" "button" in the ".modal-dialog" "css_element"
    And I should see "Super test comment 01"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    Then I should see "1" on the comments column

  @javascript
  Scenario: Delete a comment from question
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Select a category" to "Test questions"
    And I should see "0" on the comments column
    When I click "0" on the row on the comments column
    And I add "Super test comment 01 to be deleted" comment to question
    And I click on "Add comment" "button" in the ".modal-dialog" "css_element"
    And I should see "Super test comment 01 to be deleted"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    Then I should see "1" on the comments column
    And I click "1" on the row on the comments column
    And I delete "Super test comment 01 to be deleted" comment from question
    And I should not see "Super test comment 01 to be deleted"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    But I should see "0" on the comments column

  @javascript
  Scenario: Preview question with comments
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Select a category" to "Test questions"
    And I choose "Preview" action for "First question" in the question bank
    And I click on "Comments" "link"
    Then I should see "Save comment"
    And I add "Super test comment 01" comment to question preview
    And I click on "Save comment" "link"
    And I wait "1" seconds
    Then I should see "Super test comment 01"
    And I click on "Close preview" "button"
    Then I should see "1" on the comments column
    And I choose "Preview" action for "First question" in the question bank
    And I click on "Comments" "link"
    And I delete "Super test comment 01" comment from question preview
    And I should not see "Super test comment 01"
    And I click on "Close preview" "button"
    Then I should see "0" on the comments column

  @javascript
  Scenario: Teacher with comment permissions for their own questions but not others questions
    Given the following "role capability" exists:
      | role                        | editingteacher |
      | moodle/question:commentmine | allow          |
      | moodle/question:commentall  | prevent        |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I set the field "Select a category" to "Test questions"
    And I choose "Preview" action for "First question" in the question bank
    Then I should not see "Save comment"
    And I click on "Close preview" "button"
    Then I click on "Create a new question ..." "button"
    And I set the field "item_qtype_essay" to "1"
    And I press "submitbutton"
    Then I should see "Adding an Essay question"
    And I set the field "Question name" to "Essay 01 new"
    And I set the field "Question text" to "Please write 200 words about Essay 01"
    And I press "id_submitbutton"
    Then I should see "Essay 01 new"
    And I choose "Preview" action for "Essay 01 new" in the question bank
    And I click on "Comments" "link"
    Then I should see "Save comment"
    And I log out
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher2"
    And I choose "Preview" action for "First question" in the question bank
    Then I should not see "Save comment"
    And I click on "Close preview" "button"
    And I choose "Preview" action for "Essay 01 new" in the question bank
    Then I should not see "Save comment"
    And I click on "Close preview" "button"

  @javascript
  Scenario: Comments added from the quiz page are visible
    Given I am on the "Test quiz" "mod_quiz > edit" page logged in as "teacher1"
    And I press "Add"
    And I follow "from question bank"
    And I click on "Select" "checkbox" in the "First question" "table_row"
    And I click on "Add selected questions to the quiz" "button"
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    And I press "Comments"
    And I set the field "content" to "Some new comment"
    And I click on "Save comment" "link"
    And I should see "Some new comment"
    And I switch to the main window
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I choose "Preview" action for "First question" in the question bank
    And I click on "Comments" "link"
    And I should see "Some new comment"
    And I should see "T1 Teacher1"
    And I delete "Some new comment" comment from question preview
    And I should not see "Some new comment"
    And I am on the "Test quiz" "mod_quiz > edit" page
    And I click on "Preview question" "link"
    And I switch to "questionpreview" window
    And I press "Comments"
    Then I should not see "Some new comment"
