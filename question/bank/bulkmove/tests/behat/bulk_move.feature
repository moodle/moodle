@qbank @qbank_bulkmove
Feature: Use the qbank plugin manager page for bulkmove
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
      | Course 3 | C3        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity    | name            | course | idnumber  |
      | quiz        | Test quiz       | C1     | quiz1     |
      | qbank       | Question bank 1 | C1     | qbank1    |
      | qbank       | Question bank 2 | C2     | qbank2    |
      | qbank       | Question bank 3 | C3     | qbank3    |
    And the following "question categories" exist:
      | contextlevel    | reference  | name              |
      | Activity module | quiz1      | Test questions 1  |
      | Activity module | qbank1     | Test questions 2  |
      | Activity module | qbank2     | Test questions 3  |
      | Activity module | qbank3     | Test questions 4  |
      | Activity module | qbank1     | Test questions 5  |
      | Activity module | quiz1      | Test questions 6  |
      | Course          | C1         | Test questions    |
      | Course          | C1         | Moved questions   |
    And the following "questions" exist:
      | questioncategory   | qtype     | name            | questiontext               |
      | Test questions 1   | truefalse | First question  | Answer the first question  |
      | Test questions 2   | truefalse | Second question | Answer the second question |
      | Test questions 3   | truefalse | Third question  | Answer the third question  |
      | Test questions 4   | truefalse | Fourth question | Answer the fourth question |
      | Test questions 5   | truefalse | Fifth question  | Answer the fifth question  |
      | Test questions 6   | truefalse | Sixth question  | Answer the sixth question  |

  @javascript
  Scenario: Enable/disable bulk move questions bulk action from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Bulk move questions"
    And I click on "Disable" "link" in the "Bulk move questions" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should not see question bulk action "move"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Bulk move questions" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I should see question bulk action "move"

  @javascript
  Scenario: Selecting a shared question bank limits the available categories to those belonging to the selected bank.
    Given I log in as "teacher1"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And "Test questions 1" "autocomplete_suggestions" should exist
    And "Test questions 2" "autocomplete_suggestions" should not exist
    And "Test questions 3" "autocomplete_suggestions" should not exist
    And "Test questions 4" "autocomplete_suggestions" should not exist
    And "Test questions 5" "autocomplete_suggestions" should not exist
    And "Test questions 6" "autocomplete_suggestions" should exist
    When I open the autocomplete suggestions list in the ".search-banks" "css_element"
    Then I should not see "C3 - Question bank 3" in the ".search-banks" "css_element"
    And I click on "C1 - Question bank 1" item in the autocomplete list
    Then I should not see "Test questions 1" in the ".search-categories .form-autocomplete-selection" "css_element"
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And "Test questions 2" "autocomplete_suggestions" should exist
    And "Test questions 3" "autocomplete_suggestions" should not exist
    And "Test questions 4" "autocomplete_suggestions" should not exist
    And "Test questions 5" "autocomplete_suggestions" should exist

  @javascript
  Scenario: Move a question from one bank category to another.
    Given I log in as "teacher1"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And I click on "Test questions 6" item in the autocomplete list
    And I click on "Move questions" "button"
    Then I should see "Are you sure you want to move these questions?"
    And I click on "Confirm" "button"
    And I wait until the page is ready
    Then I should see "Questions successfully moved"

  @javascript
  Scenario: Questions can be bulk moved from the question bank
    Given the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext              |
      | Test questions   | truefalse   | Question 1 | Answer the first question |
      | Test questions   | missingtype | Question 2 | Write something           |
      | Test questions   | essay       | Question 3 | frog                      |
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    # Select questions to be moved.
    And I click on "Question 1" "checkbox"
    And I click on "Question 2" "checkbox"
    And I click on "With selected" "button"
    When I press "Move to"
    # Select a different category to move the questions into.
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And I click on "Moved questions" item in the autocomplete list
    And I press "Move questions"
    And I click on "Confirm" "button"
    # Confirm that selected questions are moved to selected category while unselected questions are not moved.
    Then I should see "Moved questions"
    And I should see "Question 1"
    And I should see "Question 2"
    And I should not see "Question 3"

  @javascript
  Scenario: Unable to bulk move questions from history page
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I choose "History" action for "First question" in the question bank
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should see question bulk action "deleteselected"
    And I should not see question bulk action "move"
