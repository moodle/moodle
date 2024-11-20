@core_reportbuilder @javascript
Feature: Manage custom reports
  In order to manage custom reports
  As an admin
  I need to create new and edit existing reports

  Scenario: Create custom report with default setup
    Given the following "users" exist:
      | username  | firstname | lastname | suspended |
      | user1     | User      | 1        | 1         |
      | user2     | User      | 2        | 0         |
    And I log in as "admin"
    And I change window size to "large"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Name                  | My report |
      | Report source         | Users     |
      | Include default setup | 1         |
    And I click on "Save" "button" in the "New report" "dialogue"
    Then I should see "My report"
    # Confirm we see the default columns in the report.
    And I should see "Full name" in the "Users" "table"
    And I should see "Username" in the "Users" "table"
    And I should see "Email address" in the "Users" "table"
    # Confirm we see the default sorting in the report
    And "Admin User" "table_row" should appear before "User 2" "table_row"
    And I click on "Show/hide 'Sorting'" "button"
    And "Disable initial sorting for column 'Full name'" "checkbox" should exist in the "#settingssorting" "css_element"
    And I click on "Show/hide 'Sorting'" "button"
    # Confirm we only see not suspended users in the report.
    And I should see "Admin User" in the "Users" "table"
    And I should see "User 2" in the "Users" "table"
    And I should not see "User 1" in the "Users" "table"
    # Confirm we see the default conditions in the report.
    And I click on "Show/hide 'Conditions'" "button"
    Then I should see "Full name" in the "[data-region='settings-conditions']" "css_element"
    Then I should see "Username" in the "[data-region='settings-conditions']" "css_element"
    Then I should see "Email address" in the "[data-region='settings-conditions']" "css_element"
    Then I should see "Suspended" in the "[data-region='settings-conditions']" "css_element"
    And the following fields in the "Suspended" "core_reportbuilder > Condition" match these values:
      | Suspended operator | No |
    # Confirm we see the default filters in the report.
    And I click on "Switch to preview mode" "button"
    And I click on "Filters" "button" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    Then I should see "Full name" in the "[data-region='report-filters']" "css_element"
    Then I should see "Username" in the "[data-region='report-filters']" "css_element"
    Then I should see "Email address" in the "[data-region='report-filters']" "css_element"
    And I click on "Close 'My report' editor" "button"
    And the following should exist in the "Reports list" table:
      | Name      | Report source | Modified by |
      | My report | Users         | Admin User  |

  Scenario: Create custom report without default setup
    Given I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Report source         | Users     |
      | Include default setup | 0         |
    # Try to set the report name to some blank spaces.
    And I set the field "Name" in the "New report" "dialogue" to "   "
    And I click on "Save" "button" in the "New report" "dialogue"
    And I should see "Required"
    And I set the field "Name" in the "New report" "dialogue" to "My report"
    And I click on "Save" "button" in the "New report" "dialogue"
    Then I should see "My report"
    And I should see "Nothing to display"
    And I click on "Close 'My report' editor" "button"
    And the following should exist in the "Reports list" table:
      | Name      | Report source |
      | My report | Users         |

  Scenario: Create custom report as a manager
    # Create a report that our manager can access, but not edit.
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
    And the following "core_reportbuilder > Audience" exists:
      | report     | My report                                          |
      | classname  | core_reportbuilder\reportbuilder\audience\allusers |
      | configdata |                                                    |
    And the following "users" exist:
      | username  | firstname | lastname |
      | manager1  | Manager   | One        |
    And the following "role assigns" exist:
      | user     | role    | contextlevel   | reference |
      | manager1 | manager | System         |           |
    And I log in as "manager1"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Name          | Manager report |
      | Report source | Users          |
      | Tags          | Cat, Dog       |
    And I click on "Save" "button" in the "New report" "dialogue"
    And I click on "Close 'Manager report' editor" "button"
    And the following should exist in the "Reports list" table:
      | Name           | Tags     | Report source |
      | Manager report | Cat, Dog | Users         |
    # Manager can edit their own report, but not those of other users.
    And I set the field "Edit report name" in the "Manager report" "table_row" to "Manager report (renamed)"
    Then the "Edit report content" item should exist in the "Actions" action menu of the "Manager report (renamed)" "table_row"
    And "Edit report name" "link" should not exist in the "My report" "table_row"
    And "Actions" "actionmenu" should not exist in the "My report" "table_row"

  Scenario: Rename custom report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    # Try to set the report name to some blank spaces. It should simply ignore the change.
    And I set the field "Edit report name" in the "My report" "table_row" to "   "
    And I set the field "Edit report name" in the "My report" "table_row" to "My renamed report"
    And I reload the page
    Then the following should exist in the "Reports list" table:
      | Name              | Report source |
      | My renamed report | Users         |

  Scenario: Rename custom report using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I set the field "Edit report name" in the "My report" "table_row" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    And I reload the page
    Then I should see "English" in the "Reports list" "table"
    And I should not see "Spanish" in the "Reports list" "table"
    # Confirm report name is correctly shown in action.
    And I press "Delete report" action in the "English" report row
    And I should see "Are you sure you want to delete the report 'English' and all associated data?" in the "Delete report" "dialogue"
    And I click on "Cancel" "button" in the "Delete report" "dialogue"

  Scenario: Edit custom report from the custom reports page
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    When I press "Edit report details" action in the "My report" report row
    And I set the following fields in the "Edit report details" "dialogue" to these values:
      | Name | My renamed report |
      | Tags | Cat, Dog          |
    And I click on "Save" "button" in the "Edit report details" "dialogue"
    Then I should see "Report updated"
    And the following should exist in the "Reports list" table:
      | Name              | Tags     | Report source |
      | My renamed report | Cat, Dog | Users         |

  Scenario Outline: Filter custom reports
    Given the following "core_reportbuilder > Reports" exist:
      | name       | source                                       | tags     |
      | My users   | core_user\reportbuilder\datasource\users     | Cat, Dog |
      | My courses | core_course\reportbuilder\datasource\courses |          |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And the following should exist in the "Reports list" table:
      | Name       | Tags     | Report source |
      | My users   | Cat, Dog | Users         |
      | My courses |          | Courses       |
    And I click on "Filters" "button"
    And I set the following fields in the "<filter>" "core_reportbuilder > Filter" to these values:
      | <filter> operator | Is equal to |
      | <filter> value    | <value>     |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And the following should exist in the "Reports list" table:
      | Name     | Tags     | Report source |
      | My users | Cat, Dog | Users         |
    And I should not see "My courses" in the "Reports list" "table"
    Examples:
      | filter        | value    |
      | Name          | My users |
      | Report source | Users    |
      | Tags          | Cat      |

  Scenario: Filter custom reports by date
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "Filters" "button"
    And I set the following fields in the "Time created" "core_reportbuilder > Filter" to these values:
      | Time created operator | Range          |
      | Time created from     | ##2 days ago## |
      | Time created to       | ##tomorrow##   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "My report" in the "Reports list" "table"
    And I set the field "Time created to" in the "Time created" "core_reportbuilder > Filter" to "##yesterday##"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Nothing to display"
    And "Reports list" "table" should not exist

  Scenario: Reset filters in system report
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "Filters" "button"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator | Contains |
      | Name value    | Lionel   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Filters (1)" in the "#dropdownFiltersButton" "css_element"
    And I should see "Nothing to display"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "Filters (1)" in the "#dropdownFiltersButton" "css_element"
    And I should see "Filters" in the "#dropdownFiltersButton" "css_element"
    And "[data-region='report-filters']" "css_element" should be visible
    Then I should see "Filters reset"
    And the following fields in the "Name" "core_reportbuilder > Filter" match these values:
      | Name operator | Is any value |
    And I should see "My report" in the "Reports list" "table"

  Scenario: Custom report tags are not displayed if tagging is disabled
    Given the following config values are set as admin:
      | usetags | 0 |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    Then the following should exist in the "Reports list" table:
      | Name      | Report source |
      | My report | Users         |
    And "Tags" "link" should not exist in the "Reports list" "table"
    And I click on "Filters" "button"
    And "Tags" "core_reportbuilder > Filter" should not exist

  Scenario: Delete custom report
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I log in as "admin"
    When I navigate to "Reports > Report builder > Custom reports" in site administration
    And I press "Delete report" action in the "My report" report row
    And I click on "Delete" "button" in the "Delete report" "dialogue"
    Then I should see "Report deleted"
    And I should see "Nothing to display"

  Scenario: Switch between Preview and Edit mode
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    Then I should see "Preview" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should not see "Edit" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should not see "Filters" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should see "Full name" in the ".reportbuilder-sidebar-menu" "css_element"
    Then I click on "Switch to preview mode" "button"
    Then I should not see "Preview" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should see "Edit" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should see "Filters" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    Then I click on "Switch to edit mode" "button"
    Then I should see "Preview" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I should not see "Edit" in the "[data-region='core_reportbuilder/report-header']" "css_element"

  Scenario: Access report clicking on the report name
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    When I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "My report" "link" in the "My report" "table_row"
    Then I should see "Preview" in the "[data-region='core_reportbuilder/report']" "css_element"
    And I should not see "Edit" in the "[data-region='core_reportbuilder/report']" "css_element"
    And "button[title='Filters']" "css_element" should not exist in the "[data-region='core_reportbuilder/report']" "css_element"

  Scenario: Access report clicking on the view icon
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   |
      | My report | core_user\reportbuilder\datasource\users |
    When I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I press "View" action in the "My report" report row
    Then I should not see "Preview" in the "[data-region='core_reportbuilder/report']" "css_element"
    And I should not see "Edit" in the "[data-region='core_reportbuilder/report']" "css_element"
    And "button[title='Filters']" "css_element" should exist in the "[data-region='core_reportbuilder/report']" "css_element"

  Scenario: Special characters in report name are shown correctly
    Given the following "core_reportbuilder > Reports" exist:
      | name                    | source                                   |
      | My fish & chips report  | core_user\reportbuilder\datasource\users |
    When I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I press "Edit report content" action in the "My fish & chips report" report row
    Then I should see "My fish & chips report" in the "#region-main .navbar" "css_element"

  Scenario: Site report limit is observed when creating new reports
    Given the following config values are set as admin:
      | customreportslimit     | 0 |
    And the following "core_reportbuilder > Reports" exist:
      | name           | source                                       |
      | Report users   | core_user\reportbuilder\datasource\users     |
      | Report courses | core_course\reportbuilder\datasource\courses |
    When I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    Then "New report" "button" should exist
    And the following config values are set as admin:
      | customreportslimit     | 2 |
    And I reload the page
    And "New report" "button" should not exist

  Scenario: Disable live editing of custom reports
    Given the following config values are set as admin:
      | customreportsliveediting     | 0 |
    And the following "core_reportbuilder > Reports" exist:
      | name           | source                                       |
      | Report users   | core_user\reportbuilder\datasource\users     |
    When I am on the "Report users" "reportbuilder > Editor" page logged in as "admin"
    Then I should see "Viewing of report data while editing is disabled by the site administrator. Switch to preview mode to view the report." in the "[data-region='core_table/dynamic']" "css_element"
    And I click on "Switch to preview mode" "button"
    And I should see "admin" in the "Users" "table"
    And I click on "Close 'Report users' editor" "button"
    And I press "View" action in the "Report users" report row
    And I should see "admin" in the "Users" "table"

  Scenario Outline: Download custom report in different formats
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user1     | User      | 1        |
      | user2     | User      | 2        |
    And the following "core_reportbuilder > Reports" exist:
      | name           | source                                       |
      | Report users   | core_user\reportbuilder\datasource\users     |
    When I am on the "Report users" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Switch to preview mode" "button"
    Then I set the field "Download table data as" to "<format>"
    And I press "Download"
    Examples:
      | format                             |
      | Comma separated values (.csv)      |
      | Microsoft Excel (.xlsx)            |
      | HTML table                         |
      | Javascript Object Notation (.json) |
      | OpenDocument (.ods)                |
      | Portable Document Format (.pdf)    |
