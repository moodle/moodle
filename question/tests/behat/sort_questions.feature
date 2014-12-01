@core @core_question
Feature: The questions in the question bank can be sorted in various ways
  In order to see what questions I have
  As a teacher
  I want to view them in different orders

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
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
      | questioncategory | qtype     | name              | user     | questiontext    |
      | Test questions   | essay     | A question 1 name | admin    | Question 1 text |
      | Test questions   | essay     | B question 2 name | teacher1 | Question 2 text |
      | Test questions   | numerical | C question 3 name | teacher1 | Question 3 text |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Questions" node in "Course administration > Question bank"

  @javascript
  Scenario: The questions are sorted by type by default
    Then "A question 1 name" "checkbox" should appear before "C question 3 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted in reverse order by type
    When I follow "Sort by Question type descending"
    Then "C question 3 name" "checkbox" should appear before "A question 1 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted by name
    When I follow "Sort by Question ascending"
    Then "A question 1 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And "B question 2 name" "checkbox" should appear before "C question 3 name" "checkbox"

  @javascript
  Scenario: The questions can be sorted in reverse order by name
    When I follow "Sort by Question ascending"
    And I follow "Sort by Question descending"
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
