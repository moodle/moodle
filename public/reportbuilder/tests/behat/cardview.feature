@core_reportbuilder @javascript
Feature: Manage card view settings in the report editor
  In order to manage a report card view settings
  As an admin
  I need to be able to edit and save the form

  Background:
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:fullname    |
      | My report | user:email       |
      | My report | user:city        |
    And the following "users" exist:
      | username | firstname   | lastname | email              | city     |
      | l.smith  | Lionel      | Smith    | lionel@example.com | Bilbao   |

  Scenario: Edit card view settings form
    When I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    Then I change window size to "large"
    And I click on "Show/hide 'Card view'" "button"
    # Check default values.
    And the following fields match these values:
      | Columns visible       | 1     |
      | First column title    | No   |
    And I set the following fields to these values:
      | Columns visible       | 3     |
      | First column title    | Yes   |
    And I press "Save changes"
    And I should see "Card view settings saved"
    # Let's check that after switching to preview mode card view form gets rendered again.
    And I click on "Switch to preview mode" "button"
    And I click on "Switch to edit mode" "button"
    And I click on "Show/hide 'Card view'" "button"
    And the following fields match these values:
      | Columns visible       | 3     |
      | First column title    | Yes   |
    And I click on "Delete column 'Full name'" "button"
    And I click on "Delete" "button" in the "Delete column 'Full name'" "dialogue"
    # Check that 'Columns visible' select updates taking into account report maximum columns.
    And the field "visiblecolumns" matches value "2"
    And the "visiblecolumns" select box should not contain "3"

  Scenario: Show Card view
    When I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    And I press "Switch to preview mode"
    And I change window size to "530x812"
    # Card view should just show user fullname while collapsed with default settings.
    And I should see "Lionel Smith" in the "reportbuilder-table" "table"
    And I should not see "lionel@example.com" in the "reportbuilder-table" "table"
    And I should not see "Bilbao" in the "reportbuilder-table" "table"
    And I click on "Show/hide 'Lionel Smith'" "button" in the "reportbuilder-table" "table"
    And I should see "Lionel Smith" in the "reportbuilder-table" "table"
    And I should see "lionel@example.com" in the "reportbuilder-table" "table"
    And I should see "Bilbao" in the "reportbuilder-table" "table"
    # Card view do not show first column title with default settings.
    And "[data-cardtitle=\"Full name\"]" "css_element" should not exist in the "reportbuilder-table" "table"
    And "[data-cardtitle=\"Email address\"]" "css_element" should exist in the "reportbuilder-table" "table"
    And "[data-cardtitle=\"City/town\"]" "css_element" should exist in the "reportbuilder-table" "table"
    # Change 'Columns visible' to 3 and 'First column title' to yes.
    And I change window size to "large"
    And I press "Switch to edit mode"
    And I click on "Show/hide 'Card view'" "button"
    And I set the following fields to these values:
      | Columns visible       | 3     |
      | First column title    | Yes   |
    And I press "Save changes"
    # Check now all the columns are shown in the card and there is no toggle button.
    And I press "Switch to preview mode"
    And I change window size to "530x812"
    And I should see "Lionel Smith" in the "reportbuilder-table" "table"
    And I should see "lionel@example.com" in the "reportbuilder-table" "table"
    And I should see "Bilbao" in the "reportbuilder-table" "table"
    And "[data-cardtitle=\"Full name\"]" "css_element" should exist in the "reportbuilder-table" "table"
    And "[data-cardtitle=\"Email address\"]" "css_element" should exist in the "reportbuilder-table" "table"
    And "[data-cardtitle=\"City/town\"]" "css_element" should exist in the "reportbuilder-table" "table"
    And "Show/hide 'Lionel Smith'" "button" should not exist

  Scenario Outline: Toggle card view according to content of first column
    Given the following "core_reportbuilder > Reports" exist:
      | name       | source                                   | default |
      | New report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report     | uniqueidentifier |
      | New report | <firstcolumn>    |
      | New report | user:email       |
    And the following "core_reportbuilder > Conditions" exist:
      | report     | uniqueidentifier |
      | New report | user:username    |
    When I am on the "New report" "reportbuilder > Editor" page logged in as "admin"
    And I click on "Show/hide 'Conditions'" "button"
    # Make sure we're only viewing our test user in the report.
    And I set the following fields in the "Username" "core_reportbuilder > Condition" to these values:
      | Username operator | Is equal to |
      | Username value    | l.smith     |
    And I click on "Apply" "button" in the "[data-region='settings-conditions']" "css_element"
    # Now use the card show/hide toggle.
    And I change window size to "530x812"
    And I press "Switch to preview mode"
    And I should not see "lionel@example.com" in the "reportbuilder-table" "table"
    And I click on "<togglebutton>" "button" in the "reportbuilder-table" "table"
    Then I should see "lionel@example.com" in the "reportbuilder-table" "table"
    Examples:
      | firstcolumn           | togglebutton             |
      | user:fullnamewithlink | Show/hide 'Lionel Smith' |
      | user:firstname        | Show/hide 'Lionel'       |
      | user:idnumber         | Show/hide card           |
