@mod @mod_quiz
Feature: Edit quiz page - section headings
  In order to build a quiz laid out in sections the way I want
  As a teacher
  I need to be able to add, edit and remove section headings as well as shuffle
  questions within a section.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And I log in as "teacher1"

  @javascript
  Scenario: We have a quiz with one default section
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    Then I should see "Shuffle"

  @javascript
  Scenario: Modify the default section headings
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I change quiz section heading "" to "This is section one"
    Then I should see "This is section one"

 @javascript
  Scenario: Modify section headings
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
      | Test questions   | truefalse   | TF4  | This is question 04 |
      | Test questions   | truefalse   | TF5  | This is question 05 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      |           | 1         | 0       |
      | Heading 2 | 2         | 0       |
      | Heading 3 | 3         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I change quiz section heading "" to "This is section one"
    And I change quiz section heading "Heading 2" to "This is section two"
    Then I should see "This is section one"
    And I should see "This is section two"

  @javascript
  Scenario: Set section headings to blanks
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
      | Test questions   | truefalse   | TF4  | This is question 04 |
      | Test questions   | truefalse   | TF5  | This is question 05 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 2         | 0       |
      | Heading 3 | 3         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    When I change quiz section heading "Heading 1" to ""
    Then I should not see "Heading 1"
    And I should see "Heading 2"
    And I should see "Heading 3"

    And I change quiz section heading "Heading 2" to ""
    And I should not see "Heading 1"
    And I should not see "Heading 2"
    And I should see "Heading 3"

    And I change quiz section heading "Heading 3" to ""
    And I should not see "Heading 1"
    And I should not see "Heading 2"
    And I should not see "Heading 3"

  @javascript
  Scenario: Remove a section
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 2         | 0       |
      | Heading 3 | 3         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I follow "Remove heading 'Heading 2'"
    And I should see "Are you sure you want to remove the 'Heading 2' section heading?"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    Then I should see "Heading 1"
    And I should not see "Heading 2"
    And I should see "Heading 3"

  @javascript
  Scenario: The edit-icon tool-tips are updated when a section is edited
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 2         | 0       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I change quiz section heading "Heading 2" to "Edited heading"
    Then I should see "Edited heading"
    And "Edit heading 'Edited heading'" "link" should be visible
    And "Remove heading 'Edited heading'" "link" should be visible

  @javascript
  Scenario: Moving a question up from section 3 to the first section.
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
      | Test questions   | truefalse   | TF4  | This is question 04 |
      | Test questions   | truefalse   | TF5  | This is question 05 |
      | Test questions   | truefalse   | TF6  | This is question 06 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 4    |
      | TF5      | 5    |
      | TF6      | 6    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 3         | 0       |
      | Heading 3 | 5         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I move "TF5" to "After Question 2" in the quiz by clicking the move icon
    Then I should see "TF5" on quiz page "2"

  @javascript
  Scenario: moving a question down from the first section to the second section.
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
      | Test questions   | truefalse   | TF4  | This is question 04 |
      | Test questions   | truefalse   | TF5  | This is question 05 |
      | Test questions   | truefalse   | TF6  | This is question 06 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 4    |
      | TF5      | 5    |
      | TF6      | 6    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 3         | 0       |
      | Heading 3 | 5         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I move "TF1" to "After Question 3" in the quiz by clicking the move icon
    Then I should see "TF1" on quiz page "2"

  @javascript
  Scenario: I should not see a delete icon for the first section in the quiz.
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 2         | 0       |
      | Heading 3 | 3         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    Then "Remove heading 'Heading 1'" "link" should not exist
    And "Remove heading 'Heading 2'" "link" should exist
    And "Remove heading 'Heading 3'" "link" should exist

  @javascript
  Scenario: Turn shuffling on for a section
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 0       |
      | Heading 2 | 2         | 0       |
      | Heading 3 | 3         | 0       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I click on shuffle for section "Heading 1" on the quiz edit page
    And I click on shuffle for section "Heading 2" on the quiz edit page
    Then shuffle for section "Heading 1" should be "On" on the quiz edit page
    And shuffle for section "Heading 2" should be "On" on the quiz edit page

  @javascript
  Scenario: Turn shuffling off for a section
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Heading 1 | 1         | 1       |
      | Heading 2 | 2         | 1       |
      | Heading 3 | 3         | 1       |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I click on shuffle for section "Heading 1" on the quiz edit page
    And I click on shuffle for section "Heading 2" on the quiz edit page
    Then shuffle for section "Heading 1" should be "Off" on the quiz edit page
    And shuffle for section "Heading 2" should be "Off" on the quiz edit page
    And I reload the page
    And shuffle for section "Heading 1" should be "Off" on the quiz edit page
    And shuffle for section "Heading 2" should be "Off" on the quiz edit page

  @javascript
  Scenario: Add section heading option only appears for pages that are not the first in their section.
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 1    |
      | TF3      | 2    |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I click on the "Add" page break icon after question "TF1"
    And I click on "Add" "link" in the "Page 1" "list_item"
    Then "a new section heading" "list_item" in the "Page 1" "list_item" should not be visible
    # Click away to close the menu.
    And I click on ".numberofquestions" "css_element"
    And I click on "Add" "link" in the "Page 2" "list_item"
    And "a new section heading" "list_item" in the "Page 2" "list_item" should be visible
    And I click on ".numberofquestions" "css_element"
    And I click on "Add" "link" in the "Page 3" "list_item"
    And "a new section heading" "list_item" in the "Page 3" "list_item" should be visible
    And I click on ".numberofquestions" "css_element"
    And I click on "Add" "link" in the ".last-add-menu" "css_element"
    And "a new section heading" "list_item" in the ".last-add-menu" "css_element" should not be visible

  @javascript
  Scenario: Verify sections are added in the right place afte ajax changes
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext    |
      | Test questions   | truefalse   | TF1  | This is question 01 |
      | Test questions   | truefalse   | TF2  | This is question 02 |
      | Test questions   | truefalse   | TF3  | This is question 03 |
      | Test questions   | truefalse   | TF4  | This is question 04 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 4    |

    When I follow "Course 1"
    And I follow "Quiz 1"
    And I follow "Edit quiz"
    And I click on the "Remove" page break icon after question "TF1"
    And I open the "Page 2" add to quiz menu
    And I follow "a new section heading" in the open menu
    Then "TF3" "list_item" should exist in the "Section heading ..." "list_item"

  @javascript
  Scenario: Add section works after removing a page break with more than 10 pages
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name | questiontext |
      | Test questions   | truefalse   | TF1  | Question 1   |
      | Test questions   | truefalse   | TF2  | Question 2   |
      | Test questions   | truefalse   | TF3  | Question 3   |
      | Test questions   | truefalse   | TF4  | Question 4   |
      | Test questions   | truefalse   | TF5  | Question 5   |
      | Test questions   | truefalse   | TF6  | Question 6   |
      | Test questions   | truefalse   | TF7  | Question 7   |
      | Test questions   | truefalse   | TF8  | Question 8   |
      | Test questions   | truefalse   | TF9  | Question 9   |
      | Test questions   | truefalse   | TF10 | Question 10  |
      | Test questions   | truefalse   | TF11 | Question 11  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |
      | TF4      | 4    |
      | TF5      | 5    |
      | TF6      | 6    |
      | TF7      | 7    |
      | TF8      | 8    |
      | TF9      | 9    |
      | TF10     | 10   |
      | TF11     | 11   |
    When I follow "Course 1"
    And I follow "Quiz 1"
    And I click on "Edit quiz" "link" in the "Administration" "block"
    And I click on the "Remove" page break icon after question "TF10"
    And I open the "Page 10" add to quiz menu
    And I choose "a new section heading" in the open action menu
    Then "TF10" "list_item" should exist in the "Section heading ..." "list_item"
