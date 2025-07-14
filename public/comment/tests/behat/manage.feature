@core_comment @javascript
Feature: Manage comments made by users
  As an admin
  I want to view, filter and delete comments

  Background:
    Given I log in as "admin"
    And the following "course" exists:
      | fullname  | Course 1 |
      | shortname | CS101    |
    And the following "core_comment > Comments" exist:
      | contextlevel | reference | component      | area          | content |
      | Course       | CS101     | block_comments | page_comments | Uno     |
      | Course       | CS101     | block_comments | page_comments | Dos     |
      | Course       | CS101     | block_comments | page_comments | Tres    |

  Scenario: View and filter site comments
    When I navigate to "Reports > Comments" in site administration
    And the following should exist in the "reportbuilder-table" table:
      | First name | Content | Context URL      |
      | Admin User | Uno     | Course: Course 1 |
      | Admin User | Dos     | Course: Course 1 |
      | Admin User | Tres    | Course: Course 1 |
    And I click on "Filters" "button"
    And I set the following fields in the "Content" "core_reportbuilder > Filter" to these values:
      | Content operator | Contains |
      | Content value    | Uno      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Uno" in the "reportbuilder-table" "table"
    And I should not see "Dos" in the "reportbuilder-table" "table"
    And I should not see "Tres" in the "reportbuilder-table" "table"

  Scenario: Delete single comment
    When I navigate to "Reports > Comments" in site administration
    And I press "Delete" action in the "Uno" report row
    And I click on "Delete" "button" in the "Delete" "dialogue"
    Then I should not see "Uno" in the "reportbuilder-table" "table"
    And I should see "Dos" in the "reportbuilder-table" "table"
    And I should see "Tres" in the "reportbuilder-table" "table"

  Scenario: Delete multiple comments
    When I navigate to "Reports > Comments" in site administration
    And I click on "Select" "checkbox" in the "Uno" "table_row"
    And I click on "Select" "checkbox" in the "Dos" "table_row"
    And I press "Delete selected"
    And I click on "Delete" "button" in the "Delete selected" "dialogue"
    Then I should not see "Uno" in the "reportbuilder-table" "table"
    And I should not see "Dos" in the "reportbuilder-table" "table"
    And I should see "Tres" in the "reportbuilder-table" "table"
