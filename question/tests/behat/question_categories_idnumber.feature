@core @core_question
Feature: A teacher can put questions with idnumbers in categories in the question bank
  In order to organize my questions
  As a teacher
  I move questions between categories (now with idnumbers)

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: A question can only have a unique idnumber within a category
    When the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           | idnumber |
      | Course       | C1        | Top              | top            |          |
      | Course       | C1        | top              | Used category  | c1used   |
    And the following "questions" exist:
      | questioncategory | qtype | name            | questiontext                  | idnumber |
      | Used category    | essay | Test question 1 | Write about whatever you want | q1       |
      | Used category    | essay | Test question 2 | Write about whatever you want | q2       |
    And I am on the "Test question 2" "core_question > edit" page
    And I set the field "ID number" to "q1"
    And I press "submitbutton"
    # This is the standard form warning reminding the user that the idnumber needs to be unique for a category.
    Then I should see "This ID number is already in use"

  Scenario: A question can be edited and saved without changing the idnumber
    When the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           | idnumber |
      | Course       | C1        | Top              | top            |          |
      | Course       | C1        | top              | Used category  | c1used   |
    And the following "questions" exist:
      | questioncategory | qtype | name            | questiontext                  | idnumber |
      | Used category    | essay | Test question 1 | Write about whatever you want | q1       |
    And I am on the "Test question 1" "core_question > edit" page
    And I press "Save changes"
    Then I should not see "This ID number is already in use"

  Scenario: Question idnumber conflicts found when saving to the same category.
    When the following "question categories" exist:
      | contextlevel | reference | questioncategory | name       |
      | Course       | C1        | Top              | top        |
      | Course       | C1        | top              | Category 1 |
      | Course       | C1        | top              | Category 2 |
    And the following "questions" exist:
      | questioncategory | qtype | name             | questiontext                  | idnumber |
      | Category 1       | essay | Question to edit | Write about whatever you want | q1       |
      | Category 1       | essay | Other question   | Write about whatever you want | q2       |
    And I am on the "Question to edit" "core_question > edit" page
    And I set the field "ID number" to "q2"
    And I press "Save changes"
    Then I should see "This ID number is already in use"

  @javascript
  Scenario: Moving a question between categories can force a change to the idnumber
    And the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           | idnumber |
      | Course       | C1        | Top              | top            |          |
      | Course       | C1        | top              | Subcategory    | c1sub    |
      | Course       | C1        | top              | Used category  | c1used   |
    And the following "questions" exist:
      | questioncategory | qtype | name            | questiontext                  | idnumber |
      | Used category    | essay | Test question 1 | Write about whatever you want | q1       |
      | Used category    | essay | Test question 2 | Write about whatever you want | q2       |
      | Subcategory      | essay | Test question 3 | Write about whatever you want | q3       |
    When I am on the "Test question 3" "core_question > edit" page
    # The q1 idnumber is allowed for this question while it is in the Subcategory.
    And I set the field "ID number" to "q1"
    And I press "submitbutton"
    # Javascript is required for the next step.
    And I click on "Test question 3" "checkbox" in the "Test question 3" "table_row"
    And I click on "With selected" "button"
    And I click on question bulk action "move"
    And I set the field "Question category" to "Subcategory"
    And I press "Move to"
    And I choose "Edit question" action for "Test question 3" in the question bank
    # The question just moved into this category needs to have a unique idnumber, so a number is appended.
    Then the field "ID number" matches value "q1_1"
