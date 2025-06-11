@core @core_question @qbank_filter @javascript
Feature: A teacher can show all of the questions on the question bank and override pagination
  In order to see all the questions in the question bank
  As a teacher
  I must be able to toggle between show all and show paginated

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | questioncategory | name           |
      | Course       | C1        | Top              | Test Category  |
    Given 201 "questions" exist with the following data:
      | questioncategory | Test Category                 |
      | qtype            | truefalse                     |
      | name             | Question #[count]              |
      | questiontext     | True or false?                |

  Scenario: Use "Show All" button on the question bank when there are no questions
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Default for C1"
    Then I should not see "Question #"
    And I click on "Show all" "button"
    Then I should not see "Question #"
    And I click on "Show 100 per page" "button"
    Then I should not see "Question #"

  Scenario: Question bank shows paginated questions by default
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Top for Course 1"
    Then I should see "100" occurrences of "Question #" in the "div#questionscontainer" "css_element"
    And I should see "1" in the ".pagination" "css_element"
    And I should see "2" in the ".pagination" "css_element"
    And I should see "3" in the ".pagination" "css_element"

  Scenario: Toggle "Show all" shows all questions
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Top for Course 1"
    And I click on "Show all" "button"
    Then I should see "201" occurrences of "Question #" in the "div#questionscontainer" "css_element"
    And ".pagination" "css_element" should not exist
    And I should see "Show 100 per page"

  Scenario: Toggle "Show all" back to paginated shows paginated questions
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    When I apply question bank filter "Category" with value "Top for Course 1"
    And I click on "Show all" "button"
    And I click on "Show 100 per page" "button"
    Then I should see "100" occurrences of "Question #" in the "div#questionscontainer" "css_element"
    And I should see "1" in the ".pagination" "css_element"
    And I should see "2" in the ".pagination" "css_element"
    And I should see "3" in the ".pagination" "css_element"
    And I should see "Show all"

  Scenario: Show all questions on the question bank when adding questions to a quiz
    Given I am on the "Test quiz" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    And I apply question bank filter "Category" with value "Top for Course 1"
    Then I should see "20" occurrences of "Question #" in the "div#questionscontainer" "css_element"
    When I click on "Show all" "button"
    Then I should see "201" occurrences of "Question #" in the "div#questionscontainer" "css_element"
    When I click on "Select all" "checkbox"
    And I click on "Add selected questions to the quiz" "button"
    Then I should see "Questions: 201"
