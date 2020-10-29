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
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Question bank > Questions" in current page administration
    And I choose "Edit question" action for "question 01 name" in the question bank
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I choose "Edit question" action for "question 02 name" in the question bank
    And I set the following fields to these values:
      | Tags | bar |
    And I press "id_submitbutton"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    Then I should see "foo" in the "question 01 name" "table_row"
    And I should see "bar" in the "question 02 name" "table_row"
    And I should see "qidnum" in the "question 02 name" "table_row"
    And I set the field "Filter by tags..." to "foo"
    And I press key "13" in the field "Filter by tags..."
    And I should see "question 01 name" in the "categoryquestions" "table"
    And I should not see "question 02 name" in the "categoryquestions" "table"

  Scenario: The question modal can be paginated
    Given the following "questions" exist:
      | questioncategory | qtype     | name             | user     | questiontext     |
      | Test questions   | essay     | question 03 name | teacher1 | Question 03 text |
      | Test questions   | essay     | question 04 name | teacher1 | Question 04 text |
      | Test questions   | essay     | question 05 name | teacher1 | Question 05 text |
      | Test questions   | essay     | question 06 name | teacher1 | Question 06 text |
      | Test questions   | essay     | question 07 name | teacher1 | Question 07 text |
      | Test questions   | essay     | question 08 name | teacher1 | Question 08 text |
      | Test questions   | essay     | question 09 name | teacher1 | Question 09 text |
      | Test questions   | essay     | question 10 name | teacher1 | Question 10 text |
      | Test questions   | essay     | question 11 name | teacher1 | Question 11 text |
      | Test questions   | essay     | question 12 name | teacher1 | Question 12 text |
      | Test questions   | essay     | question 13 name | teacher1 | Question 13 text |
      | Test questions   | essay     | question 14 name | teacher1 | Question 14 text |
      | Test questions   | essay     | question 15 name | teacher1 | Question 15 text |
      | Test questions   | essay     | question 16 name | teacher1 | Question 16 text |
      | Test questions   | essay     | question 17 name | teacher1 | Question 17 text |
      | Test questions   | essay     | question 18 name | teacher1 | Question 18 text |
      | Test questions   | essay     | question 19 name | teacher1 | Question 19 text |
      | Test questions   | essay     | question 20 name | teacher1 | Question 20 text |
      | Test questions   | essay     | question 21 name | teacher1 | Question 21 text |
      | Test questions   | essay     | question 22 name | teacher1 | Question 22 text |
    And I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "2" "link" in the ".pagination" "css_element"
    Then I should see "question 21 name" in the "categoryquestions" "table"
    And I should see "question 22 name" in the "categoryquestions" "table"
    And I should not see "question 01 name" in the "categoryquestions" "table"
    And I click on "Show all 22" "link" in the ".pagingbottom" "css_element"
    And I should see "question 01 name" in the "categoryquestions" "table"
    And I should see "question 22 name" in the "categoryquestions" "table"

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
