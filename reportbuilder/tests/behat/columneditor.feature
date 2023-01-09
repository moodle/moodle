@core_reportbuilder @javascript
Feature: Manage custom report columns
  In order to manage the columns of custom reports
  As an admin
  I need to add, edit and delete columns in a report

  Scenario: Add column to report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Add column 'Full name'" "link"
    Then I should see "Added column 'Full name'"
    And I should see "Full name" in the "reportbuilder-table" "table"

  Scenario: Search for and add column to report
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
      | default | 0                                        |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I set the field "Search" in the "[data-region=sidebar-menu]" "css_element" to "Last name"
    Then I should see "Last name" in the "[data-region=sidebar-menu]" "css_element"
    And I should not see "Email address" in the "[data-region=sidebar-menu]" "css_element"
    And I click on "Add column 'Last name'" "link"
    And I should see "Added column 'Last name'"
    And I should see "Last name" in the "reportbuilder-table" "table"

  Scenario: Rename column in report
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
      | default | 0                                        |
    And the following "core_reportbuilder > Column" exists:
      | report           | My report     |
      | uniqueidentifier | user:fullname |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I set the field "Rename column 'Full name'" to "My renamed column"
    And I reload the page
    Then I should see "My renamed column" in the "reportbuilder-table" "table"

  Scenario: Rename column in report using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I set the field "Rename column 'Full name'" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    And I reload the page
    Then I should see "English" in the "reportbuilder-table" "table"
    And I should not see "Spanish" in the "reportbuilder-table" "table"

  Scenario: Move column in report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
      | My report | user:email       |
      | My report | user:lastaccess  |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Move column 'Last access'" "button"
    And I click on "After \"Full name\"" "link" in the "Move column 'Last access'" "dialogue"
    Then I should see "Moved column 'Last access'"
    And "Last access" "text" should appear before "Email address" "text"

  Scenario: Delete column from report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Delete column 'Full name'" "button"
    And I click on "Delete" "button" in the "Delete column 'Full name'" "dialogue"
    Then I should see "Deleted column 'Full name'"
    And I should see "Nothing to display"
