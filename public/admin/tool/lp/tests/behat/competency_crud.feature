@tool @tool_lp @tool_lp_framework
Feature: Manage CRUD operations for competencies
  In order to perform CRUD operations on competencies
  As a manager
  I need to be able to create, read, update and delete competencies

  Background:
    Given the following "core_competency > frameworks" exist:
      | shortname | idnumber |
      | CF1       | CF1      |
    And the following "core_competency > competencies" exist:
      | shortname | competencyframework | idnumber | description    |
      | C1        | CF1                 | C1ID     | C1 description |
    And I log in as "admin"
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I click on "CF1 (CF1)" "link"

  @javascript
  Scenario: Create a new competency
    # Targets the `CF1` element to make `Add competency` button visible and accessible to avoid ambiguity with the other 'CF1' element on the screen.
    Given I click on "//span[text()='CF1']" "xpath_element"
    When I press "Add competency"
    And I set the field "Name" to "C2"
    And I set the field "Description" to "C2 description"
    And I set the field "ID number" to "C2ID"
    And I press "Save changes"
    # Access the newly created competencies to ensure that correct information was registered.
    Then "Competency created" "text" should exist
    And "C2" "text" should appear after "C1" "text"
    And I select "C2" of the competency tree
    And "C2ID" "text" should exist
    And "C2 description" "text" should exist

  @javascript
  Scenario: Read a competency
    When I select "C1" of the competency tree
    # Confirm that selected competency info displayed matches registered info.
    Then "C1ID" "text" should exist
    And "C1 description" "text" should exist
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    # Similar to the previous step, to avoid ambiguity with the competency framework "Edit", target css element with data-action=edit.
    And I click on "[data-action=edit]" "css_element"
    # Confirm that the details displayed when accessing edit screen values match registered data.
    And the field "Name" matches value "C1"
    And the field "Description" matches value "C1 description"
    And the field "ID number" matches value "C1ID"

  @javascript
  Scenario: Update a competency
    Given I select "C1" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    # Similar to the previous step, to avoid ambiguity with the competency framework "Edit", target css element with data-action=edit.
    When I click on "[data-action=edit]" "css_element"
    And I set the field "Name" to "C2"
    And I set the field "Description" to "C2 description"
    And I set the field "ID number" to "C2ID"
    And I press "Save changes"
    Then "Competency updated" "text" should exist
    And "C1" "text" should not exist
    And "C2" "text" should exist
    And I select "C2" of the competency tree
    And "C2ID" "text" should exist
    And "C1ID" "text" should not exist
    And "C2 description" "text" should exist
    And "C1 description" "text" should not exist

  @javascript
  Scenario: Delete a competency
    Given I select "C1" of the competency tree
    # Targets the unique 'Edit' link needed to open the menu, avoiding ambiguity with the other 'Edit' link.
    And I click on "//a[@href='#' and text()='Edit']" "xpath_element"
    When I click on "Delete" "link"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    # Confirm that "C1" competency was successfully deleted.
    Then "C1" "text" should not exist
