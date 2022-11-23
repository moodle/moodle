@mod @mod_data
Feature: Users can use mod_data without editing the templates
  In order to use the database module
  As a teacher
  I need to manage fields and entries using always the default templates.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
      | data1    | text | field2 | Test field 2 description |
    And the following "mod_data > entries" exist:
      | database | field1          | field2         |
      | data1    | Student entry 1 | Some content 1 |
      | data1    | Student entry 2 | Some content 2 |
    And I am on the "Test database name" "data activity" page logged in as teacher1

  @javascript
  Scenario: The default view templates should be updated when a field is added.
    Given I navigate to "Database" in current page administration
    And I should see "field1"
    And I should see "field2"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should see "Student entry 2"
    And I should see "Some content 2"
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should see "field1"
    And I should see "field2"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should not see "Student entry 2"
    And I should not see "Some content 2"
    When the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field3 | Test field 3 description |
    Then I navigate to "Database" in current page administration
    And I should see "field1"
    And I should see "field2"
    And I should see "field3"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should see "Student entry 2"
    And I should see "Some content 2"
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should see "field1"
    And I should see "field2"
    And I should see "field3"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should not see "Student entry 2"
    And I should not see "Some content 2"

  Scenario: The default add templates should be updated when a field is added.
    Given I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    And I should see "field1"
    And I should see "field2"
    When the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field3 | Test field 3 description |
    Then I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    And I should see "field1"
    And I should see "field2"
    And I should see "field3"

  @javascript
  Scenario: The default view templates should be updated when a field is deleted.
    Given I navigate to "Database" in current page administration
    And I should see "field1"
    And I should see "field2"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should see "Student entry 2"
    And I should see "Some content 2"
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should see "field1"
    And I should see "field2"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    And I should not see "Student entry 2"
    And I should not see "Some content 2"
    When I navigate to "Fields" in current page administration
    And I click on "Actions" "button" in the "field2" "table_row"
    And I choose "Delete" in the open action menu
    And I click on "Continue" "button"
    Then I navigate to "Database" in current page administration
    And I should see "field1"
    And I should not see "field2"
    And I should see "Student entry 1"
    And I should not see "Some content 1"
    And I should see "Student entry 2"
    And I should not see "Some content 2"
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should see "field1"
    And I should not see "field2"
    And I should see "Student entry 1"
    And I should not see "Some content 1"
    And I should not see "Student entry 2"
    And I should not see "Some content 2"

  Scenario: The default add templates should be updated when a field is deleted.
    Given I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    And I should see "field1"
    And I should see "field2"
    When I navigate to "Fields" in current page administration
    And I click on "Delete" "link" in the "field2" "table_row"
    And I click on "Continue" "button"
    Then I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    And I should see "field1"
    And I should not see "field2"

  @javascript
  Scenario: The dynamic default templates can be reset after a manual edition.
    Given I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "List view template"
    And I set the following fields to these values:
      | Header         | New header!                  |
      | Repeated entry | This is the template content |
      | Footer         | New footer!                  |
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I navigate to "Database" in current page administration
    And I should see "New header!"
    And I should see "This is the template content"
    And I should see "New footer!"
    And I should not see "Student entry 1"
    And I should not see "Some content 1"
    When I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "List view template"
    And I click on "Reset" "button" in the "sticky-footer" "region"
    And I click on "Reset" "button" in the "Reset template?" "dialogue"
    And I should see "Template reset"
    And I navigate to "Database" in current page administration
    And I should not see "New header!"
    And I should not see "This is the template content"
    And I should not see "New footer!"
    And I should see "Student entry 1"
    And I should see "Some content 1"
    Then the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field3 | Test field 3 description |
    And I navigate to "Database" in current page administration
    And I click on "Add entry" "button"
    And I should see "field1"
    And I should see "field2"
    And I should see "field3"
