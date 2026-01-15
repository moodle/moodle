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
    And I apply question bank filter "Category" with value "Test questions 1"
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should not see question bulk action "move"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Bulk move questions" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions 1"
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I should see question bulk action "move"

  @javascript
  Scenario: Selecting a shared question bank limits the available categories to those belonging to the selected bank.
    Given I log in as "teacher1"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions 1"
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
    And the field "selectcategory" matches value "Default for Question bank 1"
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And "Test questions 2 (1)" "autocomplete_suggestions" should exist
    And "Test questions 3" "autocomplete_suggestions" should not exist
    And "Test questions 4" "autocomplete_suggestions" should not exist
    And "Test questions 5" "autocomplete_suggestions" should exist

  @javascript
  Scenario: Move a question from one bank category to another.
    Given I log in as "teacher1"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions 1"
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And I click on "Test questions 6 (1)" item in the autocomplete list
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
    And I apply question bank filter "Category" with value "Test questions"
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
    # No questions are highlighted when bulk-moved.
    And the "class" attribute of "Question 1" "table_row" should not contain "highlight"
    And the "class" attribute of "Question 2" "table_row" should not contain "highlight"

  @javascript
  Scenario: Move a question to a different bank in a specified category
    Given the following "question categories" exist:
      | contextlevel    | reference | name              |
      | Activity module | qbank2    | Test questions 3b |
    And the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext              |
      | Test questions   | truefalse   | Question 1 | Answer the first question |
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    And I apply question bank filter "Category" with value "Test questions"
    And I click on "Question 1" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-banks" "css_element"
    And I click on "C2 - Question bank 2" item in the autocomplete list
    And I open the autocomplete suggestions list in the ".search-categories" "css_element"
    And I click on "Test questions 3b" item in the autocomplete list
    And I click on "Move questions" "button"
    Then I should see "Are you sure you want to move these questions?"
    And I click on "Confirm" "button"
    # Confirm that selected questions are moved to selected category while unselected questions are not moved.
    Then I should see "Questions successfully moved"
    And I should see "Test questions 3b"
    And I should see "Question 1"
    And I should not see "Question 2"
    And I should not see "Question 3"

  @javascript
  Scenario: Unable to bulk move questions from history page
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Test questions 1"
    And I choose "History" action for "First question" in the question bank
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should see question bulk action "deleteselected"
    And I should not see question bulk action "move"

  @javascript
  Scenario: Questions can be moved to a different bank, if the user has the correct capability
    Given the following "role" exists:
      | name      | Question adder |
      | shortname | adder          |
    And the following "role capability" exists:
      | role                | adder |
      | moodle/question:add | allow |
    And the following "course" exists:
      | fullname  | Course 4 |
      | shortname | C4       |
      | category  | 0        |
    And the following "course enrolment" exists:
      | course | C4       |
      | user   | teacher1 |
      | role   | adder    |
    And the following "activity" exists:
      | activity | qbank           |
      | course   | C4              |
      | name     | Question bank 4 |
      | idnumber | qbank4          |
    And the following "question category" exists:
      | contextlevel | Activity module  |
      | reference    | qbank4           |
      | name         | Test questions 7 |
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Test questions 1"
    And I press "Create a new question ..."
    And I set the field "item_qtype_truefalse" to "1"
    # Manually create a new question so additional parameters are included in the URL, and we can test they are handled correctly
    # during the move operation.
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the following fields to these values:
      | Question name | Seventh question |
      | Question text | test             |
    And I press "id_submitbutton"
    And I click on "Seventh question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And the field "searchbanks" matches value "C1 - Test quiz"
    And the field "selectcategory" matches value "Test questions 1 (2)"
    And I open the autocomplete suggestions list in the ".search-banks" "css_element"
    And I should see "C1 - Question bank 1" in the ".search-banks .form-autocomplete-suggestions" "css_element"
    And I should see "C2 - Question bank 2" in the ".search-banks .form-autocomplete-suggestions" "css_element"
    And I should see "C4 - Question bank 4" in the ".search-banks .form-autocomplete-suggestions" "css_element"
    And I should not see "C3 - Question bank 3" in the ".search-banks .form-autocomplete-suggestions" "css_element"
    And I click on "C1 - Question bank 1" item in the autocomplete list
    And I open the autocomplete suggestions list in the ".question_category_selector" "css_element"
    And I click on "Test questions 2 (1)" item in the autocomplete list
    And I click on "Move questions" "button"
    And I should see "Are you sure you want to move these questions?"
    When I click on "Confirm" "button"
    Then I should see "Questions successfully moved"
    # The move dialogue should default to the new bank and category.
    And I click on "Seventh question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And the field "searchbanks" matches value "C1 - Question bank 1"
    And the field "selectcategory" matches value "Test questions 2 (2)"
    # The moved question should be highlighted
    And the "class" attribute of "Seventh question" "table_row" should contain "highlight"

  @javascript
  Scenario: A new question bank is available in the move dialogue immediately
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And the following "user preferences" exist:
      | user     | preference | value    |
      | teacher1 | htmleditor | textarea |
    And I navigate to "Question banks" in current page administration
    And I press "Add"
    And I set the field "Question bank name" to "New question bank"
    And I press "Save and return to question bank list"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions 1"
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I open the autocomplete suggestions list in the ".search-banks" "css_element"
    Then "New question bank" "autocomplete_suggestions" should exist

  @javascript
  Scenario: Clicking the dummy option for showing the reached limit in course does not throw an error
    Given the following "activities" exist:
      | activity | name                      | course | idnumber  |
      | qbank    | Question bank Course 1 1  | C1     | qbankc101 |
      | qbank    | Question bank Course 1 2  | C1     | qbankc102 |
      | qbank    | Question bank Course 1 3  | C1     | qbankc103 |
      | qbank    | Question bank Course 1 4  | C1     | qbankc104 |
      | qbank    | Question bank Course 1 5  | C1     | qbankc105 |
      | qbank    | Question bank Course 1 6  | C1     | qbankc106 |
      | qbank    | Question bank Course 1 7  | C1     | qbankc107 |
      | qbank    | Question bank Course 1 8  | C1     | qbankc108 |
      | qbank    | Question bank Course 1 9  | C1     | qbankc109 |
      | qbank    | Question bank Course 1 10 | C1     | qbankc110 |
      | qbank    | Question bank Course 2 1  | C2     | qbankc201 |
      | qbank    | Question bank Course 2 2  | C2     | qbankc202 |
      | qbank    | Question bank Course 2 3  | C2     | qbankc203 |
      | qbank    | Question bank Course 2 4  | C2     | qbankc204 |
      | qbank    | Question bank Course 2 5  | C2     | qbankc205 |
      | qbank    | Question bank Course 2 6  | C2     | qbankc206 |
      | qbank    | Question bank Course 2 7  | C2     | qbankc207 |
      | qbank    | Question bank Course 2 8  | C2     | qbankc208 |
      | qbank    | Question bank Course 2 9  | C2     | qbankc209 |
      | qbank    | Question bank Course 2 10 | C2     | qbankc210 |
      | qbank    | Question bank Course 3 1  | C3     | qbankc301 |
      | qbank    | Question bank Course 3 2  | C3     | qbankc302 |
      | qbank    | Question bank Course 3 2  | C3     | qbankc303 |
      | qbank    | Question bank Course 3 4  | C3     | qbankc304 |
      | qbank    | Question bank Course 3 5  | C3     | qbankc305 |
      | qbank    | Question bank Course 3 6  | C3     | qbankc306 |
      | qbank    | Question bank Course 3 7  | C3     | qbankc307 |
      | qbank    | Question bank Course 3 8  | C3     | qbankc308 |
      | qbank    | Question bank Course 3 9  | C3     | qbankc309 |
      | qbank    | Question bank Course 3 10 | C3     | qbankc310 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C3     | editingteacher |
    When I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to "Question banks" in current page administration
    And I follow "Question bank 1"
    And I select "Categories" from the "Question bank tertiary navigation" singleselect
    And I follow "Test questions 5"
    And I click on "Fifth question" "checkbox"
    And I click on "With selected" "button"
    And I click on "move" "button"
    And I wait until the page is ready
    And "Question bank" "autocomplete_selection" should exist
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I type "Question bank Course"
    Then I click on "More than 20 results. You need to refine your search." item in the autocomplete list
    And "More than 20 results. You need to refine your search." "autocomplete_selection" should exist
    And I should see "You must select a question bank before you can select a category."
