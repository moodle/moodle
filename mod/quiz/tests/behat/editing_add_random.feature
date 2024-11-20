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
    And the following "question categories" exist:
      | contextlevel | reference | name        | questioncategory     |
      | Course       | C1        | Subcategory | Questions Category 1 |
    And the following "questions" exist:
      | questioncategory     | qtype | name                | user     | questiontext    |
      | Questions Category 1 | essay | question 1 name     | admin    | Question 1 text |
      | Questions Category 1 | essay | question 2 name     | teacher1 | Question 2 text |
      | Subcategory          | essay | question 3 name     | teacher1 | Question 3 text |
      | Subcategory          | essay | question 4 name     | teacher1 | Question 4 text |
      | Questions Category 1 | essay | "listen" & "answer" | teacher1 | Question 5 text |
    And the following "core_question > Tags" exist:
      | question            | tag |
      | question 1 name     | foo |
      | question 2 name     | bar |
      | question 3 name     | foo |
      | question 4 name     | bar |
      | "listen" & "answer" | foo |

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

  Scenario: A random question can be added to the quiz
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    And I press "Add random question"
    And I should see "Random (Questions Category 1) based on filter condition with tags: foo" on quiz page "1"
    When I click on "Configure question" "link" in the "Random (Questions Category 1) based on filter condition with tags: foo" "list_item"
    Then I should see "Questions Category 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""
    # Include subcategories.
    And I navigate to "Questions" in current page administration
    And I open the "Page 1" add to quiz menu
    And I follow "a random question"
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

  Scenario: A random question from the course's top category can be added to the quiz
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for Course 1"
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    When I press "Add random question"
    Then I should see "Random (Any category in this course) based on filter condition with tags: foo" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Any category in this course) based on filter condition with tags: foo" "list_item"
    And I should see "Top for Course 1"
    And I should see "foo"
    And I should see "question 1 name"
    And I should see "\"listen\" & \"answer\""

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

  Scenario: A random question from the course category's top category can be added to the quiz.
    Given the following "system role assigns" exist:
      | user     | role           | contextlevel |
      | teacher1 | editingteacher | Category     |
    And the following "categories" exist:
      | name       | category | idnumber |
      | Category 1 | 0        | CAT1     |
    And the following "question categories" exist:
      | contextlevel | reference | name                   |
      | Category     | CAT1      | Default for Category 1 |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    # Create a question in the 'Default for Category 1' category.
    And I press "Create a new question ..."
    And I set the field "item_qtype_essay" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Category" to "Default for Category 1"
    And I set the field "Question name" to "default for category 1 question 1 name"
    And I set the field "Question text" to "Default for Category 1 question 1 text"
    And I press "id_submitbutton"
    # Create a second question in the 'Default for Category 1' category.
    And I press "Create a new question ..."
    And I set the field "item_qtype_essay" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Category" to "Default for Category 1"
    And I set the field "Question name" to "default for category 1 question 2 name"
    And I set the field "Question text" to "Default for Category 1 question 2 text"
    And I press "id_submitbutton"
    # Add a tag to the second question.
    And I choose "Manage tags" action for "default for category 1 question 2 name" in the question bank
    And I set the field "Tags" to "bar"
    And I click on "Save changes" "button" in the "Question tags" "dialogue"
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for Category 1"
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "bar"
    And I select "1" from the "randomcount" singleselect
    When I press "Add random question"
    Then I should see "Random (Any category inside course category Category 1) based on filter condition with tags: bar" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Any category inside course category Category 1) based on filter condition with tags: bar" "list_item"
    And I should see "Top for Category 1"
    And I should see "bar"
    And I should see "default for category 1 question 2 name"
    And I should not see "default for category 1 question 1 name"

  Scenario: A random question from the system's top category can be added to the quiz
    Given the following "system role assigns" exist:
      | user     | role           | contextlevel |
      | teacher1 | editingteacher | System       |
    And the following "question categories" exist:
      | contextlevel | reference | name            |
      | System       |           | System category |
    And the following "questions" exist:
      | questioncategory | qtype | name                   | user  | questiontext           |
      | System category  | essay | system question 1 name | admin | System question 1 text |
      | System category  | essay | system question 2 name | admin | System question 2 text |
    And the following "core_question > Tags" exist:
      | question               | tag |
      | system question 1 name | foo |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "admin"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for System"
    And I set the field "Also show questions from subcategories" to "1"
    And I click on "Apply filters" "button"
    And I apply question bank filter "Tag" with value "foo"
    And I select "1" from the "randomcount" singleselect
    When I press "Add random question"
    Then I should see "Random (Any system-level category) based on filter condition with tags: foo" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (Any system-level category) based on filter condition with tags: foo" "list_item"
    And I should see "Top for System"
    And I should see "foo"
    And I should see "system question 1 name"
    And I should not see "system question 2 name"

  Scenario: A random question from a top category, excluding subcategories, shows an indicator of being faulty
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I apply question bank filter "Category" with value "Top for Course 1"
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
      | Parent category |  Default for Quiz 1 |
    And I press "Create category and add random question"
    And I should see "Random (New Random category) based on filter condition" on quiz page "1"
    And I click on "Configure question" "link" in the "Random (New Random category) based on filter condition" "list_item"
    Then I should see "New Random category"
