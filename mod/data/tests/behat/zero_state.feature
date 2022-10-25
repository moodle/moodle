@mod @mod_data @javascript
Feature: Zero state page (no fields created)

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |

  Scenario: Teachers see buttons to manage database when there is no field created on view page
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And "Import a preset" "button" should exist
    When I click on "Import a preset" "button"
    Then I should see "Import from zip file"
    And I am on the "Test database name" "data activity" page
    And "Create a field" "button" should exist
    And I click on "Create a field" "button"
    And I click on "Short text" "link"
    And I should see "Create a field"
    And I am on the "Test database name" "data activity" page
    And "Use a preset" "button" should exist
    And I click on "Use a preset" "button"
    And I should see "Presets"

  Scenario: Teachers see buttons to manage database when there is no field created on templates page
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And "Import a preset" "button" should exist
    When I click on "Import a preset" "button"
    Then I should see "Import from zip file"
    And I am on the "Test database name" "data activity" page
    And I click on "Templates" "link"
    And "Create a field" "button" should exist
    And I click on "Create a field" "button"
    And I click on "Short text" "link"
    And I should see "Create a field"
    And I am on the "Test database name" "data activity" page
    And I click on "Templates" "link"
    And "Use a preset" "button" should exist
    And I click on "Use a preset" "button"
    And I should see "Presets"

  Scenario: Teachers see buttons to manage database when there is no field created on fields page
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And I click on "Fields" "link"
    And "Import a preset" "button" should not exist
    And "Use a preset" "button" should not exist
    And "Create a field" "button" should exist
    Then I should see "No fields yet"
    And I click on "Create a field" "button"
    And I click on "Short text" "link"
    And I should see "Create a field"
