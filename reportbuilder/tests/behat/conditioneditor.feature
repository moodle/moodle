@core_reportbuilder @javascript
Feature: Manage custom report conditions
  In order to manage the conditions of custom reports
  As an admin
  I need to add, edit and delete conditions in a report

  Background:
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
      | My report | user:email       |
    And the following "users" exist:
      | username  | firstname | lastname | email              |
      | user01    | User      | One      | user01@example.com |
      | user02    | User      | Two      | user02@example.com |

  Scenario: Add condition to report
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I click on "Show/hide 'Conditions'" "button"
    Then I should see "There are no conditions selected" in the "[data-region='settings-conditions']" "css_element"
    And I set the field "Select a condition" to "Email address"
    And I should see "Added condition 'Email address'"
    And I should not see "There are no conditions selected" in the "[data-region='settings-conditions']" "css_element"
    And I set the following fields in the "Email address" "core_reportbuilder > Condition" to these values:
      | Email address operator | Does not contain |
      | Email address value    | user02           |
    And I click on "Apply" "button" in the "[data-region='settings-conditions']" "css_element"
    And I should see "Conditions applied"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"

  Scenario: Move condition in report
    Given the following "core_reportbuilder > Conditions" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
      | My report | user:email       |
      | My report | user:country     |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on "Show/hide 'Conditions'" "button"
    And I click on "Move condition 'Country'" "button"
    And I click on "After \"Full name\"" "link" in the "Move condition 'Country'" "dialogue"
    Then I should see "Moved condition 'Country'"
    And "Country" "text" should appear before "Email address" "text"

  Scenario: Delete condition from report
    Given the following "core_reportbuilder > Conditions" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I click on "Show/hide 'Conditions'" "button"
    And I set the following fields in the "Email address" "core_reportbuilder > Condition" to these values:
      | Email address operator | Does not contain |
      | Email address value    | user02           |
    And I click on "Apply" "button" in the "[data-region='settings-conditions']" "css_element"
    And I click on "Delete condition 'Email address'" "button"
    And I click on "Delete" "button" in the "Delete condition 'Email address'" "dialogue"
    Then I should see "Deleted condition 'Email address'"
    And I should see "There are no conditions selected" in the "[data-region='settings-conditions']" "css_element"
    And "[data-region='active-conditions']" "css_element" should not exist
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"

  Scenario: Reset conditions in report
    Given the following "core_reportbuilder > Conditions" exist:
      | report    | uniqueidentifier |
      | My report | user:email       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I click on "Show/hide 'Conditions'" "button"
    And I set the following fields in the "Email address" "core_reportbuilder > Condition" to these values:
      | Email address operator | Does not contain |
      | Email address value    | example.com      |
    And I click on "Apply" "button" in the "[data-region='settings-conditions']" "css_element"
    And I should see "Nothing to display"
    And I click on "Reset all" "button" in the "[data-region='settings-conditions']" "css_element"
    And I click on "Reset all" "button" in the "Reset conditions" "dialogue"
    Then I should see "Conditions reset"
    And the following fields in the "Email address" "core_reportbuilder > Condition" match these values:
      | Email address operator | Is any value |
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
