@mod @mod_quiz

Feature: Moving a question to another category should not affect random questions in a quiz
  In order for a quiz with random questions to work as expected
  Teachers should be able to
  Move a question to a different category without affecting the category the random questions in the quiz reference to

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
    And the following "activities" exist:
      | activity   | name   | intro                                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add random question form | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           |
      | Course       | C1        | Top              | top            |
      | Course       | C1        | top              | Default for C1 |
      | Course       | C1        | Default for C1   | Subcategory    |
      | Course       | C1        | top              | Used category  |
    And the following "questions" exist:
      | questioncategory | qtype | name                      | questiontext                  |
      | Used category    | essay | Test question to be moved | Write about whatever you want |

  @javascript
  Scenario: Moving a question should not change the random question
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Used category"
    And I press "Add random question"
    And I should see "Random question based on filter condition" on quiz page "1"
    And I click on "Configure question" "link" in the "Random question based on filter condition" "list_item"
    And I should see "Used category"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration
    And I apply question bank filter "Category" with value "Used category"
    And I click on "Test question to be moved" "checkbox" in the "Test question to be moved" "table_row"
    And I click on "With selected" "button"
    And I click on question bulk action "move"
    And I set the field "Question category" to "Subcategory"
    And I press "Move to"
    Then I should see "Test question to be moved"
    And I should see "Subcategory (1)"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I should see "Random question based on filter condition" on quiz page "1"
    And I click on "Configure question" "link" in the "Random question based on filter condition" "list_item"
    And I should see "Used category"

  @javascript
  Scenario: Renaming a random question category should update the random question
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Used category"
    And I press "Add random question"
    And I should see "Random question based on filter condition" on quiz page "1"
    And I am on the "Course 1" "core_question > course question categories" page
    And I click on "Edit this category" "link" in the "Used category" "list_item"
    And I set the following fields to these values:
      | Name            | Used category new |
      | Category info   | I was edited      |
    And I press "Save changes"
    Then I should see "Used category new"
    And I should see "I was edited" in the "Used category new" "list_item"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I should see "Random question based on filter condition" on quiz page "1"
