@core @core_grades @javascript
Feature: Teachers can perform bulk actions on grade items and categories in the gradebook setup
  In order to be able to easily organize my gradebook
  As a teacher
  I need to be able to select multiple grade items and categories and perform bulk actions on them

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course   | C1        | 0        |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher 1 | 1         | teacher1@example.com  | t1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
    And the following "grade categories" exist:
      | fullname   | course |
      | Category 1 | C1     |
    And the following "grade categories" exist:
      | fullname   | course | gradecategory |
      | Category 2 | C1     | Category 1    |
      | Category 3 | C1     | Category 2    |
    And the following "grade items" exist:
      | itemname     | course | category   |
      | Grade item 1 | C1     | ?          |
      | Grade item 2 | C1     | Category 1 |
      | Grade item 3 | C1     | Category 1 |
      | Grade item 4 | C1     | Category 2 |
    And I log in as "teacher1"
    And I am on "Course" course homepage
    And I navigate to "Setup > Gradebook setup" in the course gradebook

  Scenario: A teacher can see bulk action options in the gradebook setup
    # Bulk action options should not be displayed until at least one grade item is selected.
    Given I should not see "Move" in the "sticky-footer" "region"
    When I set the field "Grade item 1" to "1"
    Then I should see "Move" in the "sticky-footer" "region"
    And I set the field "Grade item 2" to "1"
    And I should see "Move" in the "sticky-footer" "region"
    # Unchecking all grade items should hide the bulk action area.
    And I set the following fields to these values:
      | Grade item 1 | 0 |
      | Grade item 2 | 0 |
    And I should not see "Move" in the "sticky-footer" "region"

  Scenario: A teacher can see the number of selected grade items and categories in the bulk action area
    Given I set the field "Grade item 1" to "1"
    # Selecting a grade category should also select all grade items in that category.
    When I set the field "Category 2" to "1"
    Then I should see "4 selected" in the "sticky-footer" "region"
    And I set the field "Category 2" to "0"
    And I should see "1 selected" in the "sticky-footer" "region"
    And I set the field "All" to "1"
    And I should see "8 selected" in the "sticky-footer" "region"
    And I set the field "All" to "0"
    And I should not see "selected" in the "sticky-footer" "region"

  Scenario: A teacher can unselect all grade items and categories in the bulk action area
    Given I set the following fields to these values:
      | Grade item 1 | 1 |
      | Category 2   | 1 |
    And I should see "4 selected" in the "sticky-footer" "region"
    And "Close bulk edit" "button" should exist in the "sticky-footer" "region"
    When I click on "Close bulk edit" "button" in the "sticky-footer" "region"
    Then the following fields match these values:
      | All          | 0 |
      | Grade item 1 | 0 |
      | Category 1   | 0 |
      | Grade item 2 | 0 |
      | Grade item 3 | 0 |
      | Category 2   | 0 |
      | Grade item 4 | 0 |
      | Category 3   | 0 |
    And "Close bulk edit" "button" should not exist in the "sticky-footer" "region"

  Scenario: A teacher can see bulk move modal with all available grade categories
    Given I set the field "Grade item 1" to "1"
    When I click on "Move" "button" in the "sticky-footer" "region"
    Then "Move items" "dialogue" should exist
    And "Course" "list_item" should exist in the "Move items" "dialogue"
    And "Category 1" "list_item" should exist in the "Move items" "dialogue"
    And "Category 2" "list_item" should exist in the "Move items" "dialogue"
    And "Category 3" "list_item" should exist in the "Move items" "dialogue"

  Scenario: A teacher can collapse grade categories in the bulk move modal
    Given I set the field "Grade item 1" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    # Collapse "Category 2" category.
    When I click on "Collapse" "link" in the "Category 2" "list_item"
    Then "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Expand" "link" should exist in the "Category 2" "list_item"
    # Collapse "Category 1" category.
    And I click on "Collapse" "link" in the "Category 1" "list_item"
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should not be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Expand" "link" should exist in the "Category 1" "list_item"
    # Expand "Category 1" category.
    And I click on "Expand" "link" in the "Category 1" "list_item"
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Collapse" "link" should exist in the "Category 1" "list_item"
    # Expand "Category 2" category.
    And I click on "Expand" "link" in the "Category 2" "list_item"
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should be visible
    And "Collapse" "link" should exist in the "Category 2" "list_item"

  Scenario: A teacher can move multiple grade items to a category
    Given I set the field "Grade item 2" to "1"
    And I set the field "Grade item 1" to "1"
    And I click on "Move" "button" in the "sticky-footer" "region"
    And I click on "Category 3" "list_item" in the "Move items" "dialogue"
    And I click on "Move" "button" in the "Move items" "dialogue"
    And I wait to be redirected
    # Confirm that 'Grade item 1' and 'Grade item 2' have been moved to 'Category 3'
    And I click on grade item menu "Grade item 1" of type "gradeitem" on "setup" page
    When I choose "Edit grade item" in the open action menu
    And "Edit grade item" "dialogue" should exist
    Then I should see "Category 3" in the "Grade category" "form_row"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Grade item 2" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And "Edit grade item" "dialogue" should exist
    And I should see "Category 3" in the "Grade category" "form_row"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    # Confirm that all other grade items have not been moved.
    And I click on grade item menu "Grade item 3" of type "gradeitem" on "setup" page
    When I choose "Edit grade item" in the open action menu
    And I wait until "Edit grade item" "dialogue" exists
    Then I should see "Category 1" in the "Grade category" "form_row"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Grade item 4" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And I wait until "Edit grade item" "dialogue" exists
    And I should see "Category 2" in the "Grade category" "form_row"

  @accessibility
  Scenario: A teacher can navigate through the grade categories in the bulk modal using the keyboard
    Given I set the field "Grade item 1" to "1"
    And I press tab key in "sticky-footer" "region"
    And the focused element is "Move" "button" in the "sticky-footer" "region"
    And I press enter
    And "Move items" "dialogue" should exist
    And I press tab
    And the focused element is "Close" "button" in the "Move items" "dialogue"
    And I press tab
    And the focused element is "Course" "list_item" in the "Move items" "dialogue"
    # Move to the next grade category.
    When I press the down key
    Then the focused element is "Category 1" "list_item" in the "Move items" "dialogue"
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    And I press the down key
    And the focused element is "Category 2" "list_item" in the "Move items" "dialogue"
    # Move to the previous grade category.
    And I press the up key
    And the focused element is "Category 1" "list_item" in the "Move items" "dialogue"
    And I press the up key
    And the focused element is "Course" "list_item" in the "Move items" "dialogue"
    # Move to the last grade category.
    And I press the end key
    And the focused element is "Category 3" "list_item" in the "Move items" "dialogue"
    # Move to the first grade category.
    And I press the home key
    And the focused element is "Course" "list_item" in the "Move items" "dialogue"

  @accessibility
  Scenario: A teacher can collapse and expand the grade categories in the bulk modal using the keyboard
    Given I set the field "Grade item 1" to "1"
    And I press tab key in "sticky-footer" "region"
    And the focused element is "Move" "button" in the "sticky-footer" "region"
    And I press enter
    And "Move items" "dialogue" should exist
    And I press tab
    And the focused element is "Close" "button" in the "Move items" "dialogue"
    And I press tab
    And the focused element is "Course" "list_item" in the "Move items" "dialogue"
    And I press the down key
    Then the focused element is "Category 1" "list_item" in the "Move items" "dialogue"
    And I press the down key
    And the focused element is "Category 2" "list_item" in the "Move items" "dialogue"
    And "Collapse" "link" should exist in the "Category 2" "list_item"
    # Collapse "Category 2" category.
    When I press the left key
    Then "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Expand" "link" should exist in the "Category 2" "list_item"
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    And the focused element is "Category 1" "list_item" in the "Move items" "dialogue"
    And "Collapse" "link" should exist in the "Category 2" "list_item"
    # Collapse "Category 1" category.
    And I press the left key
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should not be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Expand" "link" should exist in the "Category 1" "list_item"
    And I press the down key
    # Expand "Category 1" category.
    And I press the right key
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should not be visible
    And "Collapse" "link" should exist in the "Category 1" "list_item"
    And the focused element is "Category 2" "list_item" in the "Move items" "dialogue"
    # Expand "Category 2" category.
    And I press the right key
    And "Course" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 1" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 2" "list_item" in the "Move items" "dialogue" should be visible
    And "Category 3" "list_item" in the "Move items" "dialogue" should be visible
    And "Collapse" "link" should exist in the "Category 2" "list_item"

  @accessibility
  Scenario: A teacher can move multiple items to a category using the keyboard
    Given I set the following fields to these values:
      | Grade item 1 | 1 |
      | Grade item 2 | 1 |
    And I press tab key in "sticky-footer" "region"
    And the focused element is "Move" "button" in the "sticky-footer" "region"
    And I press enter
    And "Move items" "dialogue" should exist
    And I press tab
    And the focused element is "Close" "button" in the "Move items" "dialogue"
    And I press tab
    And the focused element is "Course" "list_item" in the "Move items" "dialogue"
    And I press the down key
    And the focused element is "Category 1" "list_item" in the "Move items" "dialogue"
    And I press the down key
    And the focused element is "Category 2" "list_item" in the "Move items" "dialogue"
    And I press the down key
    And the focused element is "Category 3" "list_item" in the "Move items" "dialogue"
    And the "data-selected" attribute of "Category 3" "list_item" should contain "false"
    # Select the grade category "Category 2".
    And I press enter
    And the "data-selected" attribute of "Category 3" "list_item" should contain "true"
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    And I press tab
    And the focused element is "Cancel" "button" in the "Move items" "dialogue"
    And I press tab
    And the focused element is "Move" "button" in the "Move items" "dialogue"
    When I press the enter key
    And I wait until the page is ready
    And I wait "2" seconds
    And I press tab key in "region-main" "region"
    # Confirm that 'Grade item 1' and 'Grade item 2' have been moved to 'Category 3'
    And I click on grade item menu "Grade item 1" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And I wait until "Edit grade item" "dialogue" exists
    Then I should see "Category 3" in the "Grade category" "form_row"
    And I click on "Cancel" "button" in the "Edit grade item" "dialogue"
    And I click on grade item menu "Grade item 2" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And I wait until "Edit grade item" "dialogue" exists
    And I should see "Category 3" in the "Grade category" "form_row"
