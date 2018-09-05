@tool @tool_dataprivacy @javascript
Feature: Manage data categories
  As the privacy officer
  In order to manage the data registry
  I need to be able to manage the data categories for the data registry

  Background:
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Edit" "link"
    And I choose "Categories" in the open action menu
    And I press "Add category"
    And I set the field "Name" to "Category 1"
    And I set the field "Description" to "Category 1 description"
    When I press "Save"
    Then I should see "Category 1" in the "List of data categories" "table"
    And I should see "Category 1 description" in the "Category 1" "table_row"

  Scenario: Update a data category
    Given I click on "Actions" "link" in the "Category 1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Name" to "Category 1 edited"
    And I set the field "Description" to "Category 1 description edited"
    When I press "Save changes"
    Then I should see "Category 1 edited" in the "List of data categories" "table"
    And I should see "Category 1 description edited" in the "List of data categories" "table"

  Scenario: Delete a data category
    Given I click on "Actions" "link" in the "Category 1" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Delete category"
    And I should see "Are you sure you want to delete the category 'Category 1'?"
    When I press "Delete"
    Then I should not see "Category 1" in the "List of data categories" "table"
