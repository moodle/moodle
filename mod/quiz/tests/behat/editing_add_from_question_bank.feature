@mod @mod_quiz @javascript
Feature: Adding questions to a quiz from the question bank
  In order to re-use questions
  As a teacher
  I want to add questions from the question bank

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
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name             | user     | questiontext     | idnumber |
      | Test questions   | essay     | question 01 name | admin    | Question 01 text |          |
      | Test questions   | essay     | question 02 name | teacher1 | Question 02 text | qidnum   |

  Scenario: The questions can be filtered by tag
    Given I am on the "question 01 name" "core_question > edit" page logged in as teacher1
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I choose "Edit question" action for "question 02 name" in the question bank
    And I set the following fields to these values:
      | Tags | bar |
    And I press "id_submitbutton"
    When I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    Then I should see "foo" in the "question 01 name" "table_row"
    And I should see "bar" in the "question 02 name" "table_row"
    And I should see "qidnum" in the "question 02 name" "table_row"
    When I apply question bank filter "Tag" with value "foo"
    And I should see "question 01 name" in the "categoryquestions" "table"
    And I should not see "question 02 name" in the "categoryquestions" "table"

  Scenario: The question modal can be paginated
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | My collection  |
    And 45 "questions" exist with the following data:
      | questioncategory | My collection             |
      | qtype            | essay                     |
      | name             | Feature question [count]  |
      | questiontext     | Write about topic [count] |
      | user             | teacher1                  |
    # Sadly, the above step generates questions which sort like FQ1, FQ11, FQ12, ..., FQ19, FQ2, FQ20, ...
    # so the expected paging behaviour is not immediately intuitive with 20 questions per page.
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as teacher1
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I should see "question 01 name" in the "categoryquestions" "table"
    And I should see "question 02 name" in the "categoryquestions" "table"
    And I should not see "Feature question" in the "categoryquestions" "table"
    And I set the field "Category" to "My collection"
    And I press "Apply filters"
    And I wait until the page is ready
    Then I should not see "question 01 name" in the "categoryquestions" "table"
    And I should see "Feature question 1" in the "categoryquestions" "table"
    And I should see "Feature question 27" in the "categoryquestions" "table"
    And I should not see "Feature question 28" in the "categoryquestions" "table"
    And I click on "2" "link" in the ".pagination" "css_element"
    And I wait until the page is ready
    And I should not see "Feature question 27" in the "categoryquestions" "table"
    And I should see "Feature question 28" in the "categoryquestions" "table"
    And I should see "Feature question 45" in the "categoryquestions" "table"
    And I should not see "Feature question 5"
    And I click on "3" "link" in the ".pagination" "css_element"
    And I wait until the page is ready
    And I should not see "Feature question 45" in the "categoryquestions" "table"
    And I should see "Feature question 5"
    And I should see "Feature question 9"

  Scenario: After closing and reopening the modal, it still works
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | My collection  |
    And the following "question" exists:
      | questioncategory | My collection     |
      | qtype            | essay             |
      | name             | Feature question  |
      | questiontext     | Write about topic |
      | user             | teacher1          |
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as teacher1
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Close" "button" in the "Add from the question bank at the end" "dialogue"
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I set the field "Category" to "My collection"
    And I press "Apply filters"
    Then I should see "Feature question"

  Scenario: Questions are added in the right place with multiple sections
    Given the following "questions" exist:
      | questioncategory | qtype | name             | questiontext     |
      | Test questions   | essay | question 03 name | question 03 text |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | question 01 name | 1    |
      | question 02 name | 2    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 2         | 0       |
    And I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    When I open the "Page 1" add to quiz menu
    And I follow "from question bank"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'question 03 name')]//input[@type='checkbox']" to "1"
    And I click on "Add selected questions to the quiz" "button"
    Then I should see "question 03 name" on quiz page "1"
    And I should see "question 01 name" before "question 03 name" on the edit quiz page

  Scenario: Add several selected questions from the question bank
    Given I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "question 01 name" on quiz page "1"
    And I should see "question 02 name" on quiz page "2"

  @javascript
  Scenario: Validate the sorting while adding questions from question bank
    Given the following "questions" exist:
      | questioncategory | qtype       | name              | questiontext          |
      | Test questions   | multichoice | question 03 name  | question 03 name text |
    And I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher1"
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Sort by Question ascending" "link"
    Then "question 01 name" "text" should appear before "question 02 name" "text"
    And I click on "Sort by Question descending" "link"
    And "question 03 name" "text" should appear before "question 01 name" "text"
    And I follow "Sort by Question type ascending"
    Then "question 01 name" "text" should appear before "question 03 name" "text"
    And I follow "Sort by Question type descending"
    Then "question 03 name" "text" should appear before "question 01 name" "text"

  Scenario: Shuffle option could be set before adding any question to the quiz
    Given the following "questions" exist:
      | questioncategory | qtype | name             | questiontext     |
      | Test questions   | essay | question 03 name | question 03 text |
    And I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    When I set the field "Shuffle" to "1"
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    Then I should see "question 01 name"
