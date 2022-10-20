@mod @mod_data @javascript
Feature: Users can use predefined presets
  In order to use presets
  As a user
  I need to select an existing preset

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                | intro           | course | idnumber |
      | data     | Mountain landscapes | introduction... | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |

  Scenario: If Teacher use another preset then the previous fields are removed
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Fields"
    And I should see "Test field name"
    And I follow "Presets"
    And I click on "fullname" "radio" in the "Image gallery" "table_row"
    And I click on "Use preset" "button"
    When I click on "Continue" "button"
    Then I should see "The preset has been successfully applied."
    And I follow "Fields"
    And I should see "image"
    And I should see "title"
    And I should not see "Test field name"
