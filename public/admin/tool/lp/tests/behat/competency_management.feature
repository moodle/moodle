@tool @tool_lp @tool_lp_framework
Feature: Move and cross-reference competencies
  In order to move and cross-reference competencies
  As a manager
  I need to be open the competency's menu items.

  Background:
    Given the following "core_competency > frameworks" exist:
      | shortname | idnumber |
      | CF1       | CF1      |
    And the following "core_competency > competencies" exist:
      | shortname | competencyframework |
      | C1        | CF1                 |
      | C2        | CF1                 |
      | C3        | CF1                 |
      | C4        | CF1                 |
    And I log in as "admin"
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I click on "CF1 (CF1)" "link"

  @javascript
  Scenario: Move a competency using Move up/Move down menu items
    Given I select "C1" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    When I click on "Move down" "link"
    Then "C1" "text" should appear after "C2" "text"
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    And I click on "Move up" "link"
    And "C1" "text" should appear before "C2" "text"

  @javascript
  Scenario: Move a competency using Relocate menu item
    Given I select "C3" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    When I click on "Relocate" "link"
    And I click on "C1" "text" in the "Move competency" "dialogue"
    And I click on "Move" "button" in the "Move competency" "dialogue"
    Then "C1" "text" should appear before "C3" "text"
    And "C3" "text" should appear before "C2" "text"
    And I select "C3" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    # Similar to the previous step, to avoid ambiguity with the competency framework "Edit", target css element with data-action=edit.
    And I click on "[data-action=edit]" "css_element"
    And "C1" "text" should exist
    And "No parent (top-level competency)" "text" should not exist
    And I press "Cancel"
    And "C1" "text" should appear before "C3" "text"
    And "C3" "text" should appear before "C2" "text"

  @javascript
  Scenario: Cross-reference a competency
    Given I select "C1" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    When I click on "Add cross-referenced competency" "link"
    And I click on "C2" "text" in the "Competency picker" "dialogue"
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    Then "Cross-referenced competencies:" "text" should exist
    And I should see "C2 cmp2"
