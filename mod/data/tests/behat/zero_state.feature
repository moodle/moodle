@mod @mod_data
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

  @javascript
  Scenario: Teachers see buttons to manage database when there is no field created
    Given I am on the "Test database name" "data activity" page logged in as "teacher1"
    And "Import a preset" "button" should exist
    When I click on "Import a preset" "button"
    Then I should see "Import from zip file"
    And I am on the "Test database name" "data activity" page
    And "Create a new field" "button" should exist
    And I click on "Create a new field" "button"
    And I should see "Manage fields"
    And I am on the "Test database name" "data activity" page
    And "Use preset" "button" should exist
    And I click on "Use preset" "button"
    And I should see "Presets"
