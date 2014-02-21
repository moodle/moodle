@core @core_question
Feature: A teacher can sort questions in the question bank
  In order to order the question bank's questions
  As a teacher
  I need to sort the questions list using different sort orders

  @javascript
  Scenario: Sort using question name, question type and created by sort order links
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I follow "Course 1"
    And I add a "Essay" question filling the form with:
      | Question name | A question 1 name |
      | Question text | A question 1 text |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "Essay" question filling the form with:
      | Question name | B question 2 name |
      | Question text | B question 2 text |
    And I add a "Numerical" question filling the form with:
      | Question name | C question 3 name |
      | Question text | C question 3 text |
      | answer[0] | 2 |
      | fraction[0] | 100% |
      | answer[1] | 1 |
      | fraction[1] | None |
    When I follow "Sort by Question ascending"
    Then "A question 1 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And "B question 2 name" "checkbox" should appear before "C question 3 name" "checkbox"
    And I follow "Sort by Question descending"
    And "C question 3 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And "B question 2 name" "checkbox" should appear before "A question 1 name" "checkbox"
    And I follow "Sort by Question type ascending"
    And "A question 1 name" "checkbox" should appear before "C question 3 name" "checkbox"
    And I follow "Sort by Question type descending"
    And "C question 3 name" "checkbox" should appear before "A question 1 name" "checkbox"
    And I follow "Sort by First name ascending"
    And "A question 1 name" "checkbox" should appear before "B question 2 name" "checkbox"
    And I follow "Sort by First name descending"
    And "B question 2 name" "checkbox" should appear before "A question 1 name" "checkbox"
    And I click on "Show question text in the question list" "checkbox"
    And I should see "A question 1 text"
    And I should see "B question 2 text"
    And I should see "C question 3 text"
