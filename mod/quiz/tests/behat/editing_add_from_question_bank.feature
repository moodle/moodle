@core @core_question
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
      | questioncategory | qtype     | name              | user     | questiontext    |
      | Test questions   | essay     | question 1 name | admin    | Question 1 text |
      | Test questions   | essay     | question 2 name | teacher1 | Question 2 text |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration
    And I click on "Edit" "link" in the "question 1 name" "table_row"
    And I set the following fields to these values:
      | Tags | foo |
    And I press "id_submitbutton"
    And I click on "Edit" "link" in the "question 2 name" "table_row"
    And I set the following fields to these values:
      | Tags | bar |
    And I press "id_submitbutton"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I navigate to "Edit quiz" in current page administration
    And I open the "last" add to quiz menu
    And I follow "from question bank"

  @javascript
  Scenario: The questions can be filtered by tag
    When I set the field "Filter by tags..." to "foo"
    And I press key "13" in the field "Filter by tags..."
    Then I should see "question 1 name" in the "categoryquestions" "table"
    And I should not see "question 2 name" in the "categoryquestions" "table"
