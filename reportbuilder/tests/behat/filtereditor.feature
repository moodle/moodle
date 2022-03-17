@core_reportbuilder @javascript
Feature: Manage custom report filters
  In order to manage the filters of custom reports
  As an admin
  I need to add, edit and delete filters in a report

  Scenario: Add filter to report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Show/hide 'Filters'" "button"
    Then I should see "There are no filters selected" in the "[data-region='active-filters']" "css_element"
    And I set the field "Select a filter" to "Email address"
    And I should see "Added filter 'Email address'"
    And I should not see "There are no filters selected" in the "[data-region='active-filters']" "css_element"
    And I should see "Email address" in the "[data-region='active-filters']" "css_element"

  Scenario: Rename filter in report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Show/hide 'Filters'" "button"
    When I set the field "Rename filter 'Email address'" to "My Email filter"
    And I reload the page
    And I click on "Show/hide 'Filters'" "button"
    Then I should see "My Email filter" in the "[data-region='active-filters']" "css_element"

  Scenario: Rename filter in report using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Show/hide 'Filters'" "button"
    When I set the field "Rename filter 'Email address'" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    And I reload the page
    And I click on "Show/hide 'Filters'" "button"
    Then I should see "English" in the "[data-region='active-filters']" "css_element"
    And I should not see "Spanish" in the "[data-region='active-filters']" "css_element"

  Scenario: Rename filter in report using special characters
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Show/hide 'Filters'" "button"
    When I set the field "Rename filter 'Email address'" to "Fish & Chips"
    And I click on "Switch to preview mode" "button"
    And I click on "Filters" "button"
    Then I should see "Fish & Chips" in the "[data-region='report-filters']" "css_element"

  Scenario: Move filter in report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
      | My report | user:email       |
      | My report | user:country     |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Show/hide 'Filters'" "button"
    And I click on "Move filter 'Country'" "button"
    And I click on "After \"Full name\"" "link" in the "Move filter 'Country'" "dialogue"
    Then I should see "Moved filter 'Country'"
    And "Country" "text" should appear before "Email address" "text"

  Scenario: Delete filter from report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Show/hide 'Filters'" "button"
    And I click on "Delete filter 'Email address'" "button"
    And I click on "Delete" "button" in the "Delete filter 'Email address'" "dialogue"
    Then I should see "Deleted filter 'Email address'"
    And I should see "There are no filters selected" in the "[data-region='active-filters']" "css_element"
    And I should not see "Email address" in the "[data-region='active-filters']" "css_element"

  Scenario: Use report filters when previewing report
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user1     | User      | 1        |
      | user2     | User      | 2        |
      | user3     | User      | 3        |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier  |
      | My report | user:fullname     |
      | My report | user:email        |
    And the following "core_reportbuilder > Filters" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    And I should see "user1@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user2@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user3@example.com" in the ".reportbuilder-table" "css_element"
    When I click on "Switch to preview mode" "button"
    And I click on "Filters" "button"
    And I set the following fields in the "Full name" "core_reportbuilder > Filter" to these values:
      | Full name operator | Does not contain |
      | Full name value    | User 2           |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "Filters (1)" in the "#dropdownFiltersButton" "css_element"
    And the following should exist in the "reportbuilder-table" table:
      | Full name | Email address     |
      | User 1    | user1@example.com |
      | User 3    | user3@example.com |
    And the following should not exist in the "reportbuilder-table" table:
      | Full name | Email address     |
      | User 2    | user2@example.com |
    # Switching back to edit mode should not apply filters.
    And I click on "Switch to edit mode" "button"
    And I should see "user1@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user2@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user3@example.com" in the ".reportbuilder-table" "css_element"

  Scenario: Use report filters when previewing report that contains same condition
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user1     | User   | 1        |
      | user2     | User   | 2        |
      | user3     | User   | 3        |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier  |
      | My report | user:fullname     |
      | My report | user:email        |
    And the following "core_reportbuilder > Condition" exists:
      | report           | My report  |
      | uniqueidentifier | user:email |
    And the following "core_reportbuilder > Filter" exists:
      | report           | My report  |
      | uniqueidentifier | user:email |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    And I should see "user1@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user2@example.com" in the ".reportbuilder-table" "css_element"
    And I should see "user3@example.com" in the ".reportbuilder-table" "css_element"
    # Set a condition to the report.
    When I click on "Show/hide 'Conditions'" "button"
    And I set the following fields in the "Email address" "core_reportbuilder > Condition" to these values:
      | Email address operator | Is not equal to   |
      | Email address value    | user3@example.com |
    And I click on "Apply" "button" in the "#settingsconditions" "css_element"
    And I click on "Switch to preview mode" "button"
    And I click on "Filters" "button"
    And I set the following fields in the "Email address" "core_reportbuilder > Filter" to these values:
      | Email address operator | Is not equal to   |
      | Email address value    | user2@example.com |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "Filters (1)" in the "#dropdownFiltersButton" "css_element"
    # Assert we haven't overridden the condition and user3 is still not showing in the report.
    And the following should exist in the "reportbuilder-table" table:
      | Full name | Email address     |
      | User 1    | user1@example.com |
    And the following should not exist in the "reportbuilder-table" table:
      | Full name | Email address     |
      | User 2    | user2@example.com |
      | User 3    | user3@example.com |
