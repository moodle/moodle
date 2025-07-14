@core @core_question @qbank_filter @javascript
Feature: A teacher can pagimate through question bank questions
  In order to paginate questions
  As a teacher
  I must be able to paginate

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
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | questioncategory | name           |
      | Activity module | qbank1    | Top              | Used category  |
    Given 100 "questions" exist with the following data:
      | questioncategory | Used category                 |
      | qtype            | essay                         |
      | name             | Tests question [count]        |
      | questiontext     | Write about whatever you want |
    And the following "questions" exist:
      | questioncategory | qtype | name                  | questiontext                  |
      | Used category    | essay | Not on first page     | Write about whatever you want |

  Scenario: Questions can be paginated
    Given I am on the "Qbank 1" "core_question > question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Top for Qbank 1"
    And I follow "Sort by Question name ascending"
    And I follow "Sort by Question name descending"
    And I should see "Tests question 1"
    And I should not see "Not on first page"
    And I click on "2" "link" in the ".pagination" "css_element"
    And I should not see "Tests question 1"
    And I should see "Not on first page"
