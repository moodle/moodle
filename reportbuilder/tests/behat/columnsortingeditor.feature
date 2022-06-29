@core_reportbuilder @javascript
Feature: Manage custom report columns sorting
  In order to manage the sorting for columns of custom reports
  As an admin
  I need to be able to enable/disable, change sort direction and reorder sorting for columns

  Background:
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:username    |
      | My report | user:lastname    |
      | My report | user:firstname   |
    And the following "users" exist:
      | username | firstname | lastname | email              |
      | user01   | Alice     | Zebra    | user01@example.com |
      | user02   | Zoe       | Aardvark | user02@example.com |
      | user03   | Alice     | Badger   | user03@example.com |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"

  Scenario: Toggle column sorting in report
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    # This will be the fallback sort after toggling lastname sorting.
    And I click on "Enable sorting for column 'First name'" "checkbox"
    When I click on "Enable sorting for column 'Surname'" "checkbox"
    Then I should see "Updated sorting for column 'Surname'"
    And "user02" "table_row" should appear before "user01" "table_row"
    And I click on "Disable sorting for column 'Surname'" "checkbox"
    And I should see "Updated sorting for column 'Surname'"
    And "user01" "table_row" should appear before "user02" "table_row"

  Scenario: Change column sort direction in report
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    When I click on "Enable sorting for column 'Surname'" "checkbox"
    And I click on "Sort column 'Surname' descending" "button"
    Then I should see "Updated sorting for column 'Surname'"
    And "user01" "table_row" should appear before "user02" "table_row"
    And I click on "Sort column 'Surname' ascending" "button"
    And I should see "Updated sorting for column 'Surname'"
    And "user02" "table_row" should appear before "user01" "table_row"

  Scenario: Change column sort order in report
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    When I click on "Enable sorting for column 'Surname'" "checkbox"
    And I click on "Enable sorting for column 'First name'" "checkbox"
    And I click on "Move sorting for column 'First name'" "button"
    And I click on "To the top of the list" "link" in the "Move sorting for column 'First name'" "dialogue"
    Then I should see "Updated sorting for column 'First name'"
    And "First name" "text" should appear before "Surname" "text" in the "#settingssorting" "css_element"
    And "user01" "table_row" should appear before "user02" "table_row"

  Scenario: Change column sorting for column sorted by multiple fields
    Given I change window size to "large"
    And I click on "Add column 'Full name'" "link"
    And I click on "Show/hide 'Sorting'" "button"
    When I click on "Enable sorting for column 'Full name'" "checkbox"
    Then I should see "Updated sorting for column 'Full name'"
    # User1 = Alice Zebra; User2=Zoe Aardvark; User3 = Alice Badger.
    And "user03" "table_row" should appear before "user01" "table_row"
    And "user01" "table_row" should appear before "user02" "table_row"
    And I click on "Sort column 'Full name' descending" "button"
    And I should see "Updated sorting for column 'Full name'"
    And "user02" "table_row" should appear before "user01" "table_row"
    And "user01" "table_row" should appear before "user03" "table_row"

  Scenario: Configured report sorting is always applied when editing
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    # Sort by last name descending.
    When I click on "Enable sorting for column 'Surname'" "checkbox"
    Then "user02" "table_row" should appear before "user01" "table_row"
    # Switching to preview mode should observe report config.
    And I click on "Switch to preview mode" "button"
    And "user02" "table_row" should appear before "user01" "table_row"
    # Custom sorting for the user.
    And I click on "Sort by First name Ascending" "link"
    And "user01" "table_row" should appear before "user02" "table_row"
    # Switching back to edit mode should observe report config.
    And I click on "Switch to edit mode" "button"
    And "user02" "table_row" should appear before "user01" "table_row"

  Scenario: Sortable columns are updated when column is added to report
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    When I click on "Add column 'Full name'" "link"
    Then I should see "Full name" in the "#settingssorting" "css_element"

  Scenario: Sortable columns are updated when column is deleted from report
    Given I change window size to "large"
    And I click on "Show/hide 'Sorting'" "button"
    When I click on "Delete column 'Username'" "button"
    And I click on "Delete" "button" in the "Delete column 'Username'" "dialogue"
    Then I should not see "Username" in the "#settingssorting" "css_element"
