@mod @mod_data
Feature: Users can navigate through the database activity using the tertiary navigation
  In order to use the database module
  As a user
  I need to navigate using the tertiary navigation.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name                    | intro          | course | idnumber |
      | data     | Test database name      | Database intro | C1     | data1    |
      | data     | Database without fields | Database intro | C1     | data2    |
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
      | data1    | text | field2 | Test field 2 description |
    And the following "mod_data > entries" exist:
      | database | user      | field1               | field2          |
      | data1    | teacher1  | Teacher entry 1      | Some content 1  |
      | data1    | teacher1  | Teacher entry 2      | Some content 2  |
    And the following "mod_data > presets" exist:
      | database | name                      | description                          | user      |
      | data1    | Saved preset by teacher1  | This preset has also a description   | teacher1  |
    And I log in as "admin"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1

  @javascript
  Scenario: The tertiary navigation in the Database page.
    Given I navigate to "Database" in current page administration
    # Teacher: List view.
    Then I should not see "List view" in the "data-listview-content" "region"
    And the "Import entries" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export entries" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export to portfolio" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    # Teacher: Single view.
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should not see "Single view" in the "data-singleview-content" "region"
    And the "Import entries" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export entries" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export to portfolio" item should not exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    # Teacher: Database without fields.
    And I am on the "Database without fields" "data activity" page
    And "Actions" "actionmenu" should not exist
    # Student without entries: List view.
    And I am on the "Test database name" "data activity" page logged in as student1
    And "Actions" "actionmenu" should not exist in the ".tertiary-navigation" "css_element"
    # Student without entries: Single view.
    And I set the field "View mode tertiary navigation" to "Single view"
    And I should not see "Actions" in the ".tertiary-navigation" "css_element"
    # Student with entries: Single view.
    But the following "mod_data > entries" exist:
      | database | user      | field1               | field2          |
      | data1    | student1  | Student entry 3      | Some content 3  |
    And I should not see "Actions" in the ".tertiary-navigation" "css_element"
    # Student with entries: List view.
    And I set the field "View mode tertiary navigation" to "List view"
    And the "Import entries" item should not exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export entries" item should not exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"
    And the "Export to portfolio" item should exist in the "Actions" action menu of the ".tertiary-navigation" "css_element"

  @javascript
  Scenario: The tertiary navigation in the Presets page.
    Given I navigate to "Presets" in current page administration
    When I click on "Actions" "button"
    Then I should see "Import preset" in the ".presetsactions" "css_element"
    And I should see "Export preset" in the ".presetsactions" "css_element"
    And I should see "Publish preset on this site" in the ".presetsactions" "css_element"
    And I press the escape key
    # Database without fields.
    But I am on the "Database without fields" "data activity" page
    And I navigate to "Presets" in current page administration
    And I click on "Actions" "button"
    And I should see "Import preset" in the ".presetsactions" "css_element"
    And I should not see "Export preset" in the ".presetsactions" "css_element"
    And I should not see "Publish preset on this site" in the ".presetsactions" "css_element"

  Scenario: The tertiary navigation in the Presets preview page.
    Given I navigate to "Presets" in current page administration
    When I follow "Saved preset by teacher1"
    Then I should see "Preview of Saved preset by teacher1"
    And "Use this preset" "button" should exist
    # Single view
    And I set the field "Templates tertiary navigation" to "Single view template"
    And I should see "Preview of Saved preset by teacher1"
    And "Use this preset" "button" should exist

  Scenario: The tertiary navigation in the Fields page.
    Given I navigate to "Fields" in current page administration
    Then the "Edit" item should exist in the "Actions" action menu of the "field1" "table_row"
    And the "Delete" item should exist in the "Actions" action menu of the "field1" "table_row"
    And I should not see "Actions" in the ".tertiary-navigation" "css_element"

  @javascript
  Scenario: The tertiary navigation in the Templates page.
    Given I navigate to "Templates" in current page administration
    When I click on "Actions" "button"
    Then I should see "Export preset" in the ".presetsactions" "css_element"
    And I should see "Publish preset on this site" in the ".presetsactions" "css_element"
    And I press the escape key
    And I should see "Add entry template"
    # List template.
    And I set the field "Templates tertiary navigation" to "List view template"
    And I should not see "Add entry template"
    And I should see "Header"
    And I should see "Repeated entry"
    And I should see "Footer"
