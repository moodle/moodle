@mod @mod_quiz
Feature: Setup multiple grades for a quiz
  In order to assess multiple things in one quiz
  As a teacher
  I need to be able to create multiple quiz grade items.

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name    | course | idnumber |
      | quiz       | Quiz 1  | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext        |
      | Test questions   | description | Info       | Some information    |
      | Test questions   | truefalse   | Question A | This is question 01 |
      | Test questions   | truefalse   | Question B | This is question 02 |
      | Test questions   | truefalse   | Question C | This is question 03 |

  @javascript
  Scenario: Navigation to, and display of, grading setup
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name              |
      | Quiz 1 | Intuition         |
      | Quiz 1 | Intelligence      |
      | Quiz 1 | Unused grade item |
    And quiz "Quiz 1" contains the following questions:
      | question   | page | grade item   |
      | Info       | 1    |              |
      | Question A | 1    | Intuition    |
      | Question B | 1    | Intelligence |
      | Question C | 2    | Intuition    |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher

    Then I should see "Grade items"

    And "Delete" "icon" should not exist in the "Intuition" "table_row"
    And "Intuition" row "Total of marks" column of "mod_quiz-grade-item-list" table should contain "2.00"
    And "Delete" "icon" should not exist in the "Intelligence" "table_row"
    And "Intelligence" row "Total of marks" column of "mod_quiz-grade-item-list" table should contain "1.00"
    And "Delete" "icon" should exist in the "Unused grade item" "table_row"
    And "Unused grade item" row "Total of marks" column of "mod_quiz-grade-item-list" table should contain "-"

    And the field "Question A" matches value "Intuition"
    And "1" row "Marks" column of "mod_quiz-slot-list" table should contain "1.00"
    And the field "Question B" matches value "Intelligence"
    And "2" row "Marks" column of "mod_quiz-slot-list" table should contain "1.00"
    And the field "Question C" matches value "Intuition"
    And "3" row "Marks" column of "mod_quiz-slot-list" table should contain "1.00"

    And I should not see "Info"

  @javascript
  Scenario: A grade item can be created and renamed
    Given quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I should see "Create grade items within your quiz. Allocate questions or quiz sections to these grade items to break down grade results into different areas."
    And I press "Add grade item"
    Then "New grade item 1" "table_row" should exist
    And I press "Add grade item"
    Then "New grade item 2" "table_row" should exist
    And I click on "Edit" "link" in the "New grade item 1" "table_row"
    And I set the field "New name for grade item" to "Intelligence"
    And I press enter
    And I should not see "New grade item 1"
    And "Intelligence" "table_row" should exist

  @javascript
  Scenario: Editing the name of a grade item can be cancelled
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name      |
      | Quiz 1 | Intuition |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I click on "Edit" "link" in the "Intuition" "table_row"
    And I set the field "New name for grade item" to "Intelligence"
    And I press the escape key
    And I should not see "Intelligence"
    And "Intuition" "table_row" should exist

  @javascript
  Scenario: Unused grade items can be deleted
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name              |
      | Quiz 1 | Unused grade item |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I follow "Delete grade item Unused grade item"
    Then I should not see "Unused grade item"
    And I should see "Create grade items within your quiz. Allocate questions or quiz sections to these grade items to break down grade results into different areas."

  @javascript
  Scenario: Grade item for a slot can be changed
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name      |
      | Quiz 1 | Intuition |
    And quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And "Delete" "icon" should exist in the "Intuition" "table_row"
    And I set the field "Question A" to "Intuition"
    Then  "Delete" "icon" should not exist in the "Intuition" "table_row"
    And the field "Question A" matches value "Intuition"
    And I set the field "Question A" to "[none]"
    And "Delete" "icon" should exist in the "Intuition" "table_row"
    And the field "Question A" matches value "[none]"

  @javascript
  Scenario: All setup can be reset
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name              |
      | Quiz 1 | Intuition         |
      | Quiz 1 | Intelligence      |
      | Quiz 1 | Unused grade item |
    And quiz "Quiz 1" contains the following questions:
      | question   | page | grade item   |
      | Question A | 1    | Intuition    |
      | Question B | 1    | Intelligence |
      | Question C | 2    | Intuition    |

    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I press "Reset setup"
    And I click on "Reset" "button" in the "Reset grade items setup?" "dialogue"

    Then I should see "Create grade items within your quiz. Allocate questions or quiz sections to these grade items to break down grade results into different areas."
    And the field "Question A" matches value "[none]"
    And the field "Question B" matches value "[none]"
    And the field "Question C" matches value "[none]"
    And I should not see "Reset grade items setup"

  @javascript
  Scenario: Reset all can be cancelled
    Given the following "mod_quiz > grade items" exist:
      | quiz   | name              |
      | Quiz 1 | Intuition         |
    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I press "Reset setup"
    And I click on "Cancel" "button" in the "Reset grade items setup?" "dialogue"
    Then I should see "Intuition"

  @javascript
  Scenario: Automatically set up one grade item per section
    Given quiz "Quiz 1" contains the following questions:
      | question   | page |
      | Question A | 1    |
      | Question B | 1    |
      | Question C | 2    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Reading   | 1         | 0       |
      | Listening | 3         | 0       |

    When I am on the "Quiz 1" "mod_quiz > multiple grades setup" page logged in as teacher
    And I press "Set up a grade for each section"

    Then "Reading" "table_row" should exist in the "mod_quiz-grade-item-list" "table"
    And "Listening" "table_row" should exist in the "mod_quiz-grade-item-list" "table"
    And the field "Question A" matches value "Reading"
    And the field "Question B" matches value "Reading"
    And the field "Question C" matches value "Listening"
