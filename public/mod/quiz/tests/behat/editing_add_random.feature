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
      | activity   | name    | intro                                           | course | idnumber |
      | quiz       | Quiz 1  | Quiz 1 for testing the Add random question form | C1     | quiz1    |
      | qbank      | Qbank 1 | Question bank 1 for testing the Add menu        | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference    | name                 |
      | Activity module | quiz1        | Questions Category 1 |
      | Activity module | quiz1        | Questions Category 2 |
      | Activity module | qbank1       | Qbank questions      |
    And the following "question categories" exist:
      | contextlevel    | reference | name        | questioncategory     |
      | Activity module | quiz1     | Subcategory | Questions Category 1 |
    And the following "questions" exist:
      | questioncategory     | qtype | name                | user     | questiontext    |
      | Questions Category 1 | essay | question 1 name     | admin    | Question 1 text |
      | Questions Category 1 | essay | question 2 name     | teacher1 | Question 2 text |
      | Subcategory          | essay | question 3 name     | teacher1 | Question 3 text |
      | Subcategory          | essay | question 4 name     | teacher1 | Question 4 text |
      | Questions Category 1 | essay | "listen" & "answer" | teacher1 | Question 5 text |
      | Qbank questions      | essay | Qbank question 1    | teacher1 | Qbank question  |
    And the following "core_question > Tags" exist:
      | question            | tag      |
      | question 1 name     | foo      |
      | question 2 name     | bar      |
      | question 3 name     | foo      |
      | question 4 name     | bar      |
      | "listen" & "answer" | foo      |
      | Qbank question 1    | qbanktag |

  Scenario: Available tags are shown in the autocomplete tag field
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I add question bank filter "Tag"
    And I click on "Tag" "field"
    And I press the down key
    Then "foo" "autocomplete_suggestions" should exist
    And "bar" "autocomplete_suggestions" should exist

  Scenario: Questions can be filtered by tags
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Questions Category 1"
    And I apply question bank filter "Tag" with value "foo"
    And I wait until the page is ready
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    And I should not see "question 2 name"
    And I should not see "question 3 name"
    And I should not see "question 4 name"
    # Ensure tagged questions inside subcategories are also matched.
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I wait until the page is ready
    And I should see "question 1 name"
    And I should see "question 3 name"
    And I should see "\"listen\" & \"answer\""
    And I should not see "question 2 name"
    And I should not see "question 4 name"

  Scenario: Questions can be filtered by tags on a shared question bank
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "a random question"
    Then I click on "Switch bank" "button"
    And I click on "Qbank 1" "link" in the "Select question bank" "dialogue"
    And I apply question bank filter "Category" with value "Qbank questions"
    And I apply question bank filter "Tag" with value "qbanktag"
    And I click on "Apply filters" "button"
    And I wait until the page is ready
    And I should see "Qbank question 1"
    And I should not see "question 3 name"
    And I should not see "question 2 name"
    And I should not see "question 4 name"

  Scenario: A random question can be added to the quiz
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Questions Category 1"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    And I press "Add random question"
    And I should see "Random (Questions Category 1) based on filter condition with tags: foo" on quiz page "1"
    When I click on "Configure question" "link" in the "Random (Questions Category 1) based on filter condition with tags: foo" "list_item"
    Then I should see "Questions Category 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    And I click on "Cancel" "button" in the "Editing a random question" "dialogue"
    # Include subcategories.
    And I navigate to "Questions" in current page administration
    And I open the "Page 1" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Questions Category 1"
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    And I press "Add random question"
    And I should see "Random (Questions Category 1 and subcategories) based on filter condition with tags: foo" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Questions Category 1 and subcategories) based on filter condition with tags: foo" "list_item"
    And I should see "Questions Category 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    And I should see "question 3 name"

  Scenario: A random question from the quiz's top category can be added to the quiz
    Given the following "question categories" exist:
      | contextlevel    | reference  | name            |
      | Activity module | quiz1      | Quiz 1 category |
    And the following "questions" exist:
      | questioncategory | qtype | name                   | user     | questiontext           |
      | Quiz 1 category  | essay | quiz 1 question 1 name | teacher1 | Quiz 1 question 1 text |
      | Quiz 1 category  | essay | quiz 1 question 2 name | teacher1 | Quiz 1 question 2 text |
    And the following "core_question > Tags" exist:
      | question               | tag |
      | quiz 1 question 1 name | foo |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for Quiz 1"
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    When I press "Add random question"
    Then I should see "Random (Any category of this quiz) based on filter condition with tags: foo" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Any category of this quiz) based on filter condition with tags: foo" "list_item"
    And I should see "Top for Quiz 1"
    And I should see "foo"
    And I should see "quiz 1 question 1 name"
    And I should not see "quiz 1 question 2 name"

  Scenario: A random question from a top category, excluding subcategories, shows an indicator of being faulty
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for Quiz 1"
    And I set the field "Also show questions from subcategories" to "0"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    When I press "Add random question"
    Then I should see "Random (Faulty question) based on filter condition" on quiz page "1"

  Scenario: After closing and reopening the modal, it still works
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as teacher1
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I click on "Close" "button" in the "Add a random question at the end" "dialogue"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I should not see "question 3 name"
    And I set the field "Category" to "Subcategory"
    And I press "Apply filters"
    Then I should see "question 3 name"

  Scenario: A random question can be added to the quiz from a shared question bank
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I should see "Current bank: Quiz 1"
    And I apply question bank filter "Category" with value "Questions Category 1"
    And I should see "question 1 name"
    And I click on "Switch bank" "button"
    And I click on "Qbank 1" "link" in the "Select question bank" "dialogue"
    And I should see "Current bank: Qbank 1"
    And I should not see "question 1 name"
    And I apply question bank filter "Category" with value "Qbank questions"
    And I should see "Qbank question 1"
    When I apply question bank filter "Tag" with value "qbanktag"
    And I select "1" from the "randomcount" singleselect
    And I press "Add random question"
    Then I should see "Random (Qbank questions) based on filter condition with tags: qbanktag" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Qbank questions) based on filter condition with tags: qbank" "list_item"
    And I should see "Qbank questions"
    And I should see "qbanktag"
    And I should see "Qbank question 1"

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
    And "Help with Parent category" "icon" should exist in the "Random question using a new category" "fieldset"
    And I set the following fields to these values:
      | Name            | New Random category |
      | Parent category | Questions Category 1 |
    And I press "Create category and add random question"
    And I should see "Random (New Random category) based on filter condition" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (New Random category) based on filter condition" "list_item"
    Then I should see "New Random category"

  Scenario: See questions link applies all random question filters
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Questions Category 1"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    And I press "Add random question"
    And I should see "Random (Questions Category 1) based on filter condition with tags: foo" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Questions Category 1) based on filter condition with tags: foo" "list_item"
    And I should see "Questions Category 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    And I should not see "bar"
    And I should not see "question 2 name"
    When I am on the "Quiz 1" "mod_quiz > Edit" page
    And I click on "(See questions)" "link" in the "Random (Questions Category 1) based on filter condition with tags: foo" "list_item"
    Then I should see "Questions Category 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    And I should not see "bar"
    And I should not see "question 2 name"
