@qbank @qbank_managecategories @category_reorder @javascript
Feature: A teacher can reorder question categories
  In order to change question category order
  As a teacher
  I need to reorder them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "activities" exist:
      | activity   | name    | intro           | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1 | C1     | qbank1   |
      | qbank      | Qbank 2 | Question bank 2 | C1     | qbank2   |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "system role assigns" exist:
      | user     | role           | contextlevel |
      | teacher1 | editingteacher | System       |
    And the following "question categories" exist:
      | contextlevel    | reference | name                   | idnumber     |
      | Activity module | qbank1    | Course category 1      | questioncat1 |
      | Activity module | qbank1    | Course category 2      | questioncat2 |
      | Activity module | qbank1    | Course category 3      | questioncat3 |
    And I am on the "Qbank 1" "core_question > question categories" page logged in as "teacher1"

  Scenario: Teacher cannot move or delete single category under context
    And I am on the "Qbank 2" "core_question > question categories" page logged in as "teacher1"
    When I open the action menu in "Default for Qbank 2" "list_item"
    Then I should not see "Delete"

  Scenario: Teacher can see complete edit menu if multiples categories exist under context
    When I open the action menu in "Course category 1" "list_item"
    Then I should see "Edit settings"
    And I should see "Delete"
    And I should see "Export as Moodle XML"

  Scenario: Teacher can move one category after another
    Given "Course category 1" "list_item" should appear before "Course category 2" "list_item"
    And "Course category 2" "list_item" should appear before "Course category 3" "list_item"
    When I open the action menu in "Course category 1" "list_item"
    And I choose "Move" in the open action menu
    And I click on "After Course category 3" "link" in the "Move Course category 1" "dialogue"
    Then "Course category 2" "list_item" should appear before "Course category 3" "list_item"
    And "Course category 3" "list_item" should appear before "Course category 1" "list_item"

  Scenario: Teacher can move one category before another
    Given "Course category 1" "list_item" should appear before "Course category 2" "list_item"
    And "Course category 2" "list_item" should appear before "Course category 3" "list_item"
    And I open the action menu in "Course category 3" "list_item"
    And I choose "Move" in the open action menu
    And I click on "Before Course category 1" "link" in the "Move Course category 3" "dialogue"
    Given "Course category 3" "list_item" should appear before "Course category 1" "list_item"
    And "Course category 1" "list_item" should appear before "Course category 2" "list_item"

  Scenario: Teacher can make a category a child of an existing category
    Given "Course category 1" "list_item" should appear before "Course category 2" "list_item"
    And "Course category 2" "list_item" should appear before "Course category 3" "list_item"
    And "Course category 3" "list_item" should not exist in the "Course category 1" "list_item"
    When I open the action menu in "Course category 3" "list_item"
    And I choose "Move" in the open action menu
    And I click on "As new child of Course category 1" "link" in the "Move Course category 3" "dialogue"
    And "Course category 3" "list_item" should appear before "Course category 2" "list_item"
    And "Course category 3" "list_item" should exist in the "Course category 1" "list_item"

  Scenario: Teacher can display and hide category descriptions
    Given I am on the "Qbank 2" "core_question > question categories" page logged in as "teacher1"
    When I click on "Show descriptions" "checkbox"
    Then I should see "The default category for questions shared in context 'Qbank 2'."
    And I click on "Show descriptions" "checkbox"
    And I should not see "The default category for questions shared in context 'Qbank 2'."

  Scenario: Teacher can move a category via the edit form
    Given "Course category 1" "list_item" should appear before "Course category 2" "list_item"
    And "Course category 2" "list_item" should appear before "Course category 3" "list_item"
    When I open the action menu in "Course category 3" "list_item"
    And I choose "Edit" in the open action menu
    And I should see "Top for Qbank 1" in the "Parent category" "select"
    And I should see "Course category 1" in the "Parent category" "field"
    And I should see "Course category 2" in the "Parent category" "field"
    And I should not see "Course category 3" in the "Parent category" "field"
    And I set the field "Parent category" to "Course category 1"
    And I press "Save changes"
    Then "Course category 1" "list_item" should appear before "Course category 3" "list_item"
    And "Course category 3" "list_item" should appear before "Course category 2" "list_item"
    And I open the action menu in "Course category 1" "list_item"
    And I choose "Edit" in the open action menu
    And I should see "Top for Qbank 1" in the "Parent category" "select"
    And I should not see "Course category 1" in the "Parent category" "field"
    And I should see "Course category 2" in the "Parent category" "field"
    And I should not see "Course category 3" in the "Parent category" "field"

  Scenario: Dragging a category over a category with no children shows an 'as new child' drop target
    Given the following "question categories" exist:
      | contextlevel    | reference | name                   | idnumber     | questioncategory  |
      | Activity module | qbank1    | Course category 4      | questioncat4 | Course category 2 |
    And I reload the page
    And I change viewport size to "large"
    And I drag "Course category 3" "list_item" and I drop it in "Course category 2" "text"
    And I should not see "+" in the "Course category 2" "list_item"
    When I drag "Course category 3" "list_item" and I drop it in "Course category 1" "text"
    Then I should see "+" in the "Course category 1" "list_item"
