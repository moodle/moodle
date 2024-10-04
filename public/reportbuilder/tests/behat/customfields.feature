@core @core_reportbuilder @javascript
Feature: Manage custom fields for custom reports
  In order to manage custom fields for custom reports
  As an admin
  I need to create new and edit existing report custom fields

  Scenario: Create and edit custom fields in a custom report
    Given the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
      | default | 1                                        |
    When I log in as "admin"
    And I navigate to "Reports > Report builder > Custom report fields" in site administration
    Then I should see "Custom report fields"
    And I press "Add a new category"
    And I should see "Other fields"
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Description |
      | Short name | description |
    And I press "Save changes"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I press "Edit report details" action in the "My report" report row
    And I should see "Other fields" in the "Edit report details" "dialogue"
    And I set the following fields in the "Edit report details" "dialogue" to these values:
      | Description | My awesome report description |
    And I click on "Save" "button" in the "Edit report details" "dialogue"
    And I should see "Report updated"
    And I click on "Filters" "button"
    And I set the following fields in the "Description" "core_reportbuilder > Filter" to these values:
      | Description operator | Contains |
      | Description value    | awesome  |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And the following should exist in the "Reports list" table:
      | Name      | Report source |
      | My report | Users         |
    And I set the following fields in the "Description" "core_reportbuilder > Filter" to these values:
      | Description operator | Does not contain |
      | Description value    | awesome          |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "My report"
    And I should see "Nothing to display"
