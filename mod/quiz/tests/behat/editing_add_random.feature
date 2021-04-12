@mod @mod_quiz @javascript
Feature: Adding random questions to a quiz based on category and tags
  In order to have better assessment
  As a teacher
  I want to display questions that are randomly picked from the question bank

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name   | intro                                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add random question form | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name                 |
      | Course       | C1        | Questions Category 1 |
      | Course       | C1        | Questions Category 2 |
    And the following "questions" exist:
      | questioncategory     | qtype | name            | user     | questiontext    |
      | Questions Category 1 | essay | question 1 name | admin    | Question 1 text |
      | Questions Category 1 | essay | question 2 name | teacher1 | Question 2 text |
    And the following "core_question > Tags" exist:
      | question        | tag |
      | question 1 name | foo |
      | question 2 name | bar |

  Scenario: Available tags are shown in the autocomplete tag field
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I open the autocomplete suggestions list
    Then "foo" "autocomplete_suggestions" should exist
    And "bar" "autocomplete_suggestions" should exist

  Scenario: A random question can be added to the quiz
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I set the field "Tags" to "foo"
    And I press "Add random question"
    Then I should see "Random (Questions Category 1, tags: foo)" on quiz page "1"

  Scenario: Teacher without moodle/question:useall should not see the add a random question menu item
    Given the following "permission overrides" exist:
      | capability             | permission | role           | contextlevel | reference |
      | moodle/question:useall | Prevent    | editingteacher | Course       | C1        |
    And I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    When I open the "last" add to quiz menu
    Then I should not see "a random question"

  Scenario: A random question can be added to the quiz by creating a new category
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I follow "New category"
    And I set the following fields to these values:
      | Name            | New Random category |
      | Parent category |  Top for Quiz 1     |
    And I press "Create category and add random question"
    Then I should see "Random (New Random category)" on quiz page "1"
