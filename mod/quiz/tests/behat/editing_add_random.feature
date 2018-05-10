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
      | contextlevel | reference | name                |
      | Course       | C1        | Questions Category 1|
      | Course       | C1        | Questions Category 2|
    And the following "questions" exist:
      | questioncategory     | qtype | name            | user     | questiontext    |
      | Questions Category 1 | essay | question 1 name | admin    | Question 1 text |
      | Questions Category 1 | essay | question 2 name | teacher1 | Question 2 text |

  Scenario: Available tags are shown in the autocomplete tag field
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Questions" node in "Course administration > Question bank"
    And I click on "Edit" "link" in the "question 1 name" "table_row"
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I click on "Manage tags" "link" in the "question 2 name" "table_row"
    And I set the following fields to these values:
      | Tags | bar |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Edit quiz" in current page administration
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I open the autocomplete suggestions list
    Then "foo" "autocomplete_suggestions" should exist
    And "bar" "autocomplete_suggestions" should exist