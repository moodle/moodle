@mod @mod_subsection
Feature: The module menu replaces the delegated section menu
  In order to use subsections
  As an teacher
  I need to see the delegated section action menu instead of module menu.

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "activity" exists:
      | activity | subsection  |
      | name     | Subsection1 |
      | course   | C1          |
      | idnumber | subsection1 |
      | section  | 1           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I am on the "C1" "Course" page logged in as "teacher1"

  @javascript
  Scenario: The action menu for subsection page meets the module menu
    Given I click on "Subsection1" "link" in the "region-main" "region"
    And I turn editing mode on
    # Open the action menu.
    When I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    Then I should not see "Move right"
    And I should not see "Assign roles"
    And I should not see "Highlight"
    And I should see "Edit settings"
    # Duplicate, Move and Show/Hide are not implemented yet.
    And I should not see "Move"
    And I should not see "Duplicate"
    And I should not see "Hide"
    # Delete option for subsection page is not implemented yet.
    And I should not see "Delete"
    And I should see "Permalink"

  @javascript
  Scenario: The action menu for subsection module has less options than a regular activity
    Given I turn editing mode on
    When I open "Subsection1" actions menu
    Then I should not see "Move right"
    And I should not see "Assign roles"
    And I should not see "Highlight"
    And I should see "View"
    And I should see "Edit settings"
    # Duplicate, Move and Show/Hide are not implemented yet.
    And I should not see "Move"
    And I should not see "Duplicate"
    And I should not see "Hide"
    And I should see "Delete"
    And I should see "Permalink"

  @javascript
  Scenario: The action menu for subsection module in section page has less options than a regular activity
    Given I click on "Section 1" "link"
    And I turn editing mode on
    When I open "Subsection1" actions menu
    Then I should not see "Move right"
    And I should not see "Assign roles"
    And I should not see "Highlight"
    And I should see "View"
    And I should see "Edit settings"
    # Duplicate, Move and Show/Hide are not implemented yet.
    And I should not see "Move"
    And I should not see "Duplicate"
    And I should not see "Hide"
    And I should see "Delete"
    And I should see "Permalink"

  @javascript
  Scenario: View option in subsection action menu
    Given I turn editing mode on
    And I open "Subsection1" actions menu
    When I choose "View" in the open action menu
    # Subsection page. Subsection name should be the title.
    Then I should see "Subsection1" in the "h1" "css_element"
    And "Section 1" "text" should exist in the ".breadcrumb" "css_element"
    # Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And "View" "link" should not exist in the "[data-region='header-actions-container']" "css_element"
    And I click on "Section 1" "link" in the ".breadcrumb" "css_element"
    # Section page. Section name should be the title.
    And I should see "Section 1" in the "h1" "css_element"
    And "Subsection1" "text" should not exist in the ".breadcrumb" "css_element"
    # Open the section header action menu.
    And I open "Subsection1" actions menu
    And I choose "View" in the open action menu
    And I should see "Subsection1" in the "h1" "css_element"

  @javascript
  Scenario: Edit settings option in subsection action menu
    Given I turn editing mode on
    And I open "Subsection1" actions menu
    When I choose "Edit settings" in the open action menu
    And the field "Section name" matches value "Subsection1"
    And I click on "Cancel" "button"
    And I am on the "C1 > Subsection1" "course > section" page
    # Subsection page. Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Edit settings" in the open action menu
    And the field "Section name" matches value "Subsection1"
    And I click on "Cancel" "button"
    And I am on the "C1 > Section 1" "course > section" page
    # Section page. Open Subsection1 module action menu.
    And I open "Subsection1" actions menu
    And I choose "Edit settings" in the open action menu
    And the field "Section name" matches value "Subsection1"

  @javascript
  Scenario: Permalink option in subsection action menu
    Given I turn editing mode on
    And I open "Subsection1" actions menu
    When I choose "Permalink" in the open action menu
    Then I click on "Copy to clipboard" "link"
    And I should see "Text copied to clipboard"
    And I am on the "C1 > Subsection1" "course > section" page
    # Subsection page. Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Permalink" in the open action menu
    And I click on "Copy to clipboard" "link"
    And I should see "Text copied to clipboard"
    And I am on the "C1 > Section 1" "course > section" page
    # Section page. Open Subsection1 module action menu.
    And I open "Subsection1" actions menu
    And I choose "Permalink" in the open action menu
    And I click on "Copy to clipboard" "link"
    And I should see "Text copied to clipboard"

  @javascript
  Scenario: Delete option in subsection action menu
    Given the following "activities" exist:
      | activity   | course | idnumber    | name        | intro            | section |
      | subsection | C1     | subsection2 | Subsection2 | Test Subsection2 | 1       |
      | subsection | C1     | subsection3 | Subsection3 | Test Subsection3 | 1       |
    Given I turn editing mode on
    And "Subsection1" "link" should exist
    And "Subsection2" "link" should exist
    And "Subsection3" "link" should exist
    And I open "Subsection1" actions menu
    When I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete activity?" "dialogue"
    And "Subsection1" "link" should not exist in the "#region-main-box" "css_element"
    And I am on the "C1 > Section 1" "course > section" page
    # Section page. Open Subsection2 module action menu.
    And I open "Subsection2" actions menu
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete activity?" "dialogue"
    And "Subsection2" "link" should not exist in the "#region-main-box" "css_element"
    And I am on the "C1 > Subsection3" "course > section" page
    # Subsection page. Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And "Delete" "link" should not exist in the "[data-region='header-actions-container']" "css_element"
