@qbank @qbank_deletequestion
Feature: Use the qbank plugin manager page for deletequestion
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name             |
      | Activity module | quiz1     | Test questions   |
    And the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext               | tags |
      | Test questions   | truefalse | Question 1 | Answer the first question  | foo  |
      | Test questions   | truefalse | Question 2 | Answer the second question |      |
      | Test questions   | truefalse | Question 3 | Answer the third question  |      |

  @javascript
  Scenario: Enable/disable delete question column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    Then the "Delete" action should not exist for the "Question 1" question in the question bank
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And the "Delete" action should exist for the "Question 1" question in the question bank

  @javascript
  Scenario: Enable/disable delete questions bulk action from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I click on "With selected" "button"
    Then I should not see question bulk action "deleteselected"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I click on "With selected" "button"
    And I should see question bulk action "deleteselected"

  @javascript
  Scenario: I should not see the deleted questions in the base view
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Delete" action for "Question 1" in the question bank
    And I should see "This will delete the following question and all its versions:"
    And I click on "Delete" "button" in the "Delete question?" "dialogue"
    And I choose "Delete" action for "Question 2" in the question bank
    And I click on "Delete" "button" in the "Delete question?" "dialogue"
    Then I should not see "Question 1"
    And I should not see "Question 2"
    And I should see "Question 3"

  @javascript
  Scenario: I should be able to delete a question when filtered using tags
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I apply question bank filter "Tag" with value "foo"
    And I should see "Question 1"
    And I should not see "Question 2"
    And I should not see "Question 3"
    When I choose "Delete" action for "Question 1" in the question bank
    And I click on "Delete" "button" in the "Delete question?" "dialogue"
    Then I should not see "Question 1"
    And I should not see "Question 2"
    And I should not see "Question 3"

  @javascript
  Scenario: Questions can be bulk deleted from the question bank
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Test questions"
    # Select questions to be deleted.
    And I click on "Question 1" "checkbox"
    And I click on "Question 2" "checkbox"
    And I click on "With selected" "button"
    When I press "Delete"
    # Confirm that delete confirmation message is displayed.
    Then I should see "This will delete the following questions and all their versions:"
    # Confirm that selected questions are listed on the confirmation dialog.
    And I should see "Question 1 v1" in the "Delete questions?" "dialogue"
    And I should see "Question 2 v1" in the "Delete questions?" "dialogue"
    # Delete selected questions.
    And I click on "Delete" "button" in the "Delete questions?" "dialogue"
    # Confirm that selected questions are deleted while unselected questions still exist.
    And I should not see "Question 1"
    And I should not see "Question 2"
    And I should see "Question 3"

  @javascript
  Scenario: Specific question versions can be deleted individually
    Given the following "core_question > updated questions" exist:
      | questioncategory | question   | name       |
      | Test questions   | Question 1 | Question 1 |
      | Test questions   | Question 1 | Question 1 |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "v3" in the "Question 1" "table_row"
    When I choose "History" action for "Question 1" in the question bank
    And "Question 1" "heading" should exist
    And I choose "Delete" action for "v2" in the question bank
    And I should see "This will delete selected versions of the following question" in the "Delete selected version?" "dialogue"
    And I should see "Question 1 v2" in the "Delete selected version?" "dialogue"
    And I should not see "Question 1 v1" in the "Delete selected version?" "dialogue"
    And I should not see "Question 1 v3" in the "Delete selected version?" "dialogue"
    And I click on "Delete" "button" in the "Delete selected version?" "dialogue"
    Then "Question 1" "heading" should exist
    And "v2" "table_row" should not exist
    And "v1" "table_row" should exist
    And "v3" "table_row" should exist

  @javascript
  Scenario: Specific question versions can be deleted in bulk
    Given the following "core_question > updated questions" exist:
      | questioncategory | question   | name         |
      | Test questions   | Question 1 | Question 1 A |
      | Test questions   | Question 1 | Question 1 B |
      | Test questions   | Question 1 | Question 1 C |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "v4" in the "Question 1" "table_row"
    When I choose "History" action for "Question 1" in the question bank
    And "Question 1" "heading" should exist
    And I click on "Question 1 A" "checkbox"
    And I click on "Question 1 C" "checkbox"
    And I click on "With selected" "button"
    When I press "Delete"
    And I should see "This will delete selected versions of the following question" in the "Delete selected versions?" "dialogue"
    And I should see "Question 1 A v2" in the "Delete selected versions?" "dialogue"
    And I should see "Question 1 C v4" in the "Delete selected versions?" "dialogue"
    And I should not see "Question 1 v1" in the "Delete selected versions?" "dialogue"
    And I should not see "Question 1 B v3" in the "Delete selected versions?" "dialogue"
    And I click on "Delete" "button" in the "Delete selected versions?" "dialogue"
    Then I should see "v3" in the "Question 1" "table_row"
    And I choose "History" action for "Question 1" in the question bank
    And "v2" "table_row" should not exist
    And "v4" "table_row" should not exist
    And "v1" "table_row" should exist
    And "v3" "table_row" should exist

  @javascript
  Scenario: In-use questions are indicated in the confirmation dialogue and hidden when deleted.
    Given quiz "Test quiz" contains the following questions:
      | question   | page |
      | Question 2 | 1    |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "Delete" action for "Question 2" in the question bank
    And I should see "This will delete the following question and all its versions:" in the "Delete question?" "dialogue"
    And I should see "* Denotes questions which can't be deleted because they are in use" in the "Delete question?" "dialogue"
    And I should see "* Question 2" in the "Delete question?" "dialogue"
    And I click on "Delete" "button" in the "Delete question?" "dialogue"
    Then I should see "Question 1"
    And I should not see "Question 2"
    And I should see "Question 3"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"

  @javascript
  Scenario: In-use questions are indicated in the bulk confirmation dialogue and hidden when deleted.
    Given quiz "Test quiz" contains the following questions:
      | question   | page |
      | Question 2 | 1    |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I click on "Question 2" "checkbox"
    And I click on "Question 3" "checkbox"
    And I click on "With selected" "button"
    When I press "Delete"
    And I should see "This will delete the following questions and all their versions:" in the "Delete questions?" "dialogue"
    And I should see "* Denotes questions which can't be deleted because they are in use" in the "Delete questions?" "dialogue"
    And I should see "* Question 2" in the "Delete questions?" "dialogue"
    And I should see "Question 3" in the "Delete questions?" "dialogue"
    And I should not see "* Question 3" in the "Delete questions?" "dialogue"
    And I click on "Delete" "button" in the "Delete questions?" "dialogue"
    Then I should see "Question 1"
    And I should not see "Question 2"
    And I should not see "Question 3"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I should see "Question 1"
    And I should see "Question 2"
    And I should not see "Question 3"

  @javascript
  Scenario: In-use versions are indicated in the confirmation dialogue and hidden when deleted.
    Given the following "core_question > updated questions" exist:
      | questioncategory | question   | name       |
      | Test questions   | Question 1 | Question 1 |
    And quiz "Test quiz" contains the following questions:
      | question   | page |
      | Question 1 | 1    |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "History" action for "Question 1" in the question bank
    And I choose "Delete" action for "v2" in the question bank
    And I should see "This will delete selected versions of the following question" in the "Delete selected version?" "dialogue"
    And I should see "* Denotes questions which can't be deleted because they are in use" in the "Delete selected version?" "dialogue"
    And I should see "* Question 1 v2" in the "Delete selected version?" "dialogue"
    And I click on "Delete" "button" in the "Delete selected version?" "dialogue"
    Then "v2" "table_row" should not exist
    And "v1" "table_row" should exist
    And I follow "Close"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I choose "History" action for "Question 1" in the question bank
    And I should see "Hidden" in the "v2" "table_row"
    And "v1" "table_row" should exist

  @javascript
  Scenario: In-use versions are indicated in the bulk confirmation dialogue and hidden when deleted.
    Given the following "core_question > updated questions" exist:
      | questioncategory | question   | name         |
      | Test questions   | Question 1 | Question 1 A |
      | Test questions   | Question 1 | Question 1 B |
    And quiz "Test quiz" contains the following questions:
      | question   | page |
      | Question 1 | 1    |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I choose "History" action for "Question 1" in the question bank
    And I click on "Question 1 A" "checkbox"
    And I click on "Question 1 B" "checkbox"
    And I click on "With selected" "button"
    When I press "Delete"
    And I should see "This will delete selected versions of the following question" in the "Delete selected versions?" "dialogue"
    And I should see "* Denotes questions which can't be deleted because they are in use" in the "Delete selected versions?" "dialogue"
    And I should see "* Question 1 B v3" in the "Delete selected versions?" "dialogue"
    And I should see "Question 1 A v2" in the "Delete selected versions?" "dialogue"
    And I should not see "* Question 1 A v2" in the "Delete selected versions?" "dialogue"
    And I click on "Delete" "button" in the "Delete selected versions?" "dialogue"
    Then "v1" "table_row" should exist
    And "v2" "table_row" should not exist
    And "v3" "table_row" should not exist
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I choose "History" action for "Question 1" in the question bank
    And I should see "Hidden" in the "v3" "table_row"
    And "v1" "table_row" should exist
    And "v2" "table_row" should not exist
