@core @core_question
Feature: A teacher can move questions between categories in the question bank
  In order to organize my questions
  As a teacher
  I move questions between categories

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           |
      | Course       | C1        | Top              | top            |
      | Course       | C1        | top              | Default for C1 |
      | Course       | C1        | Default for C1   | Subcategory    |
      | Course       | C1        | top              | Used category  |
    And the following "questions" exist:
      | questioncategory | qtype | name                      | questiontext                  |
      | Used category    | essay | Test question to be moved | Write about whatever you want |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Move a question between categories via the question page
    When I navigate to "Question bank" in current page administration
    And I set the field "Select a category" to "Used category"
    And I click on "Test question to be moved" "checkbox" in the "Test question to be moved" "table_row"
    And I click on "With selected" "button"
    And I click on question bulk action "move"
    And I set the field "Question category" to "Subcategory"
    And I press "Move to"
    Then I should see "Test question to be moved"
    And the field "Select a category" matches value "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subcategory (1)"
    And the "Select a category" select box should contain "Used category"
    And the "Select a category" select box should not contain "Used category (1)"
