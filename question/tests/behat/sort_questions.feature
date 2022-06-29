@core @core_question
Feature: The questions in the question bank can be sorted in various ways
  In order to see what questions I have
  As a teacher
  I want to view them in different orders

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
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name              | user     | questiontext    | idnumber    |
      | Test questions   | essay     | A question 1 name | admin    | Question 1 text | numidnum</a |
      | Test questions   | essay     | B question 2 name | teacher1 | Question 2 text |             |
      | Test questions   | numerical | C question 3 name | teacher1 | Question 3 text | numidnum</c |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: The questions are sorted by type by default
    Then "A question 1 name" "checkbox" should appear before "C question 3 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted by idnumber
    When I change the window size to "large"
    And I follow "Sort by ID number ascending"
    Then "C question 3 name" "checkbox" should appear after "A question 1 name" "checkbox"
    And I should see "numidnum</c" in the "C question 3 name" "table_row"
    And I follow "Sort by ID number descending"
    And "C question 3 name" "checkbox" should appear before "A question 1 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted in reverse order by type
    When I follow "Sort by Question type descending"
    Then "C question 3 name" "checkbox" should appear before "A question 1 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted by name
    When I follow "Sort by Question name ascending"
    Then "A question 1 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And "B question 2 name" "checkbox" should appear before "C question 3 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted in reverse order by name
    When I follow "Sort by Question name ascending"
    And I follow "Sort by Question name descending"
    Then "C question 3 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And "B question 2 name" "checkbox" should appear before "A question 1 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted by creator name
    When I follow "Sort by First name ascending"
    Then "A question 1 name" "checkbox" should appear before "B question 2 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted in reverse order by creator name
    When I follow "Sort by First name ascending"
    And I follow "Sort by First name descending"
    Then "B question 2 name" "checkbox" should appear before "A question 1 name" "checkbox"

  @javascript
  Scenario: The question text can be shown in the list of questions
    When I click on "Show question text in the question list" "checkbox"
    Then I should see "Question 1 text"
    And I should see "Question 2 text"
    And I should see "Question 3 text"
