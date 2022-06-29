@core_reportbuilder @javascript
Feature: Manage custom reports for cohorts
  In order to manage custom reports for cohorts
  As an admin and user
  I need to create new, view and edit existing reports

  Background:
    Given the following "cohorts" exist:
      | name                 | idnumber | contextid  |
      | Another one          | AO       | 1          |
      | MDL-62161            | 62161    | 1          |
      | New system cohort    | NSC      | 1          |
      | MDL-62162            | 62162    | 1          |
      | Other cohort         | LC       | 3          |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | Alice     | Last1    | user1@example.com |
      | user2    | Carlos    | Last2    | user2@example.com |
      | user3    | Paul      | Last3    | user3@example.com |
      | user4    | Juan      | Last4    | user4@example.com |
      | user5    | Pedro     | Last5    | user5@example.com |
      | user6    | Luis      | Last6    | user6@example.com |
      | user7    | David     | Last7    | user7@example.com |
      | user8    | Zoe       | Last8    | user8@example.com |
    And the following "cohort members" exist:
      | user     | cohort |
      | user1    | AO     |
      | user2    | AO     |
      | user3    | AO     |
      | user4    | AO     |
      | user5    | 62161  |
      | user6    | 62161  |
      | user7    | NSC    |
      | user8    | NSC    |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                       | default |
      | My report | core_cohort\reportbuilder\datasource\cohorts | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier   |
      | My report | cohort:context     |
      | My report | cohort:name        |

  Scenario: Add condition to cohorts report
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I click on "Show/hide 'Conditions'" "button"
    Then I should see "There are no conditions selected" in the "[data-region='settings-conditions']" "css_element"
    And I set the field "Select a condition" to "Category"
    And I should see "Added condition 'Category'"
    And I should not see "There are no conditions selected" in the "[data-region='settings-conditions']" "css_element"
    And I set the following fields in the "Category" "core_reportbuilder > Condition" to these values:
      | Category operator  | Is equal to |
      | Category value     | 3           |
    And I click on "Apply" "button" in the "[data-region='settings-conditions']" "css_element"
    And I should see "Conditions applied"
    And I should see "Other cohort" in the "reportbuilder-table" "table"
    And I should not see "MDL-62162" in the "reportbuilder-table" "table"

  Scenario: Use filters in cohorts report
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I click on "Show/hide 'Filters'" "button"
    Then I should see "There are no filters selected" in the "[data-region='settings-filters']" "css_element"
    And I set the field "Select a filter" to "Name"
    And I should see "Other cohort" in the ".reportbuilder-table" "css_element"
    And I should see "MDL-62162" in the ".reportbuilder-table" "css_element"
    When I click on "Switch to preview mode" "button"
    And I click on "Filters" "button" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator  | Contains |
      | Name value     | Another  |
    And I click on "Apply" "button" in the "[data-region='core_reportbuilder/report-header']" "css_element"
    Then the following should exist in the "reportbuilder-table" table:
      | Category         | Name              |
      | System           | Another one       |
    And the following should not exist in the "reportbuilder-table" table:
      | Category         | Name              |
      | Miscellaneous    | Other cohort         |

  Scenario: Use sorting and aggregations in cohorts report
    Given the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier   |
      | My report | user:lastname      |
    And I change window size to "large"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I set the field "Rename column 'Surname'" to "Member count"
    And I set the "Surname" column aggregation to "Count"
    And I click on "Show/hide 'Sorting'" "button"
    And I click on "Move sorting for column 'Surname'" "button"
    And I click on "To the top of the list" "link" in the "Move sorting for column 'Surname'" "dialogue"
    And I click on "Enable sorting for column 'Surname'" "checkbox"
    # "New system cohort" has fewer members than "Another one" cohort.
    And "New system cohort" "table_row" should appear before "Another one" "table_row"
    When I click on "Sort column 'Surname' descending" "button"
    Then I should see "Updated sorting for column 'Surname'"
    # Switching sort direction should now show "Another one" cohort ahead of "New system cohort".
    And "Another one" "table_row" should appear before "New system cohort" "table_row"
