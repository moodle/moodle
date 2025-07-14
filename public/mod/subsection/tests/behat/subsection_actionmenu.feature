@mod @mod_subsection
Feature: The module menu replaces the delegated section menu
  In order to use subsections
  As an teacher
  I need to see the delegated section action menu instead of module menu.

  Background:
    Given the following "users" exist:
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
    And I should not see "Move"
    # Duplicate is not implemented yet.
    And I should not see "Duplicate"
    And I should see "Hide"
    And I should see "Delete"
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
    And I should see "Move"
    # Duplicate is not implemented yet.
    And I should not see "Duplicate"
    And I should see "Hide"
    And I should see "Delete"

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
    And I should see "Move"
    # Duplicate is not implemented yet.
    And I should not see "Duplicate"
    And I should see "Hide"
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
    And I click on "Delete" "button" in the "Delete subsection?" "dialogue"
    And "Subsection1" "link" should not exist in the "#region-main-box" "css_element"
    And I am on the "C1 > Section 1" "course > section" page
    # Section page. Open Subsection2 module action menu.
    And I open "Subsection2" actions menu
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete subsection?" "dialogue"
    And "Subsection2" "link" should not exist in the "#region-main-box" "css_element"
    And I am on the "C1 > Subsection3" "course > section" page
    # Subsection page. Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete subsection?" "dialogue"
    And I should not see "Subsection3"
    And I should see "Course 1" in the "h1" "css_element"

  @javascript
  Scenario: Hide/Show option in subsection action menu
    Given I turn editing mode on
    And I should not see "Hidden from students"
    And I open "Subsection1" actions menu
    When I choose "Hide" in the open action menu
    Then I should see "Hidden from students"
    Given I am on the "C1 > Subsection1" "course > section" page
    And I should see "Hidden from students"
    # Subsection page. Open the section header action menu.
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And I choose "Show" in the open action menu
    And I should not see "Hidden from students"
    And I click on "Section 1" "link" in the ".breadcrumb" "css_element"
    And I should not see "Hidden from students"
    # Section page. Open Subsection1 module action menu.
    And I open "Subsection1" actions menu
    And I choose "Hide" in the open action menu
    And I should see "Hidden from students"

  @javascript
  Scenario: Hide/Show option in course page action menu for subsections
    Given I am on the "C1" "Course" page
    And I turn editing mode on
    When I hide section "Subsection1"
    Then I should see "Hidden from students"
    And I show section "Subsection1"
    And I should not see "Hidden from students"

  @javascript
  Scenario: Hide/Show option in subsection page action menu for subsections
    Given I am on the "C1 > Subsection1" "course > section" page
    And I turn editing mode on
    When I hide section "Subsection1"
    Then I should see "Hidden from students"
    And I show section "Subsection1"
    And I should not see "Hidden from students"

  @javascript
  Scenario: Subsections can't change visibility in hidden sections.
    Given I am on the "C1" "Course" page
    And I turn editing mode on
    And I hide section "Section 1"
    When I open section "Subsection1" edit menu
    Then I should not see "Hide"
    And I should not see "Show"
    And I am on the "C1 > Section 1" "course > section" page
    And I open section "Subsection1" edit menu
    And I should not see "Hide"
    And I should not see "Show"

  @javascript
  Scenario: Move option in subsection action menu
    Given the following "activities" exist:
      | activity   | course | idnumber    | name        | intro            | section |
      | subsection | C1     | subsection2 | Subsection2 | Test Subsection2 | 1       |
    And I turn editing mode on
    And I open "Subsection1" actions menu
    When I choose "Move" in the open action menu
    And I should see "Move Subsection1 after" in the "Move subsection" "dialogue"
    # Can't be moved inside same subsection.
    And I click on "Subsection1" "link" in the "Move subsection" "dialogue"
    And I should see "Move Subsection1 after" in the "Move subsection" "dialogue"
    # Can't be moved inside other subsection.
    And I click on "Subsection2" "link" in the "Move subsection" "dialogue"
    And I should see "Move Subsection1 after" in the "Move subsection" "dialogue"
    # Can be moved to other position.
    And I click on "General" "link" in the "Move subsection" "dialogue"
    Then I should not see "Move Subsection1 after"
    And I should see "Subsection1" in the "General" "section"
    And I should not see "Subsection1" in the "Section 1" "section"
    # Section page. Subsection is still content, so Move option should exist.
    And I am on the "C1 > Section 1" "course > section" page
    And I open "Subsection2" actions menu
    And I should see "Move"
    # Subsection page. Move option should not exist.
    And I am on the "C1 > Subsection1" "course > section" page
    And I click on "Edit" "icon" in the "[data-region='header-actions-container']" "css_element"
    And "Move" "link" should not exist in the "[data-region='header-actions-container']" "css_element"
