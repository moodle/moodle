@mod @mod_data
Feature: Users can view and manage data presets
  In order to use presets
  As a user
  I need to view, manage and use presets

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
      | activity | name                | intro | course | idnumber |
      | data     | Mountain landscapes | n     | C1     | data1    |
    And the following "mod_data > presets" exist:
      | database | name            |
      | data1    | Saved preset 1  |
      | data1    | Saved preset 2  |

  @javascript
  Scenario: Admins can delete saved presets
    Given I am on the "Mountain landscapes" "data activity" page logged in as admin
    When I follow "Presets"
    Then I should see "Choose a preset to use as a starting point."
    And I should see "Image gallery"
    And I should see "Saved preset 1"
    And I should see "Saved preset 2"
    # Plugin presets can't be removed.
    And I should not see "Actions" in the "Image gallery" "table_row"
    # The admin should be able to delete saved presets.
    But I open the action menu in "Saved preset 1" "table_row"
    And I should see "Delete"
    And I open the action menu in "Saved preset 2" "table_row"
    And I should see "Delete"

  @javascript
  Scenario: Teachers can see and use presets
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Templates"
    And I click on "Save as preset" "button"
    And I set the field "Name" to "Saved preset by teacher1"
    And I click on "Save" "button" in the "Save all fields and templates as preset" "dialogue"
    When I follow "Presets"
    Then I should see "Choose a preset to use as a starting point."
    And I should see "Image gallery"
    And I should see "Saved preset 1"
    And I should see "Saved preset 2"
    And I should see "Saved preset by teacher1"
    # Plugin presets can't be removed.
    And I should not see "Actions" in the "Image gallery" "table_row"
    # Teachers should be able to delete their saved presets.
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I should see "Delete"
    # Teachers can't delete the presets they haven't created.
    And I should not see "Actions" in the "Saved preset 1" "table_row"
    # The "Use preset" button should be enabled only when a preset is selected.
    And the "Use preset" "button" should be disabled
    And I click on "fullname" "radio" in the "Image gallery" "table_row"
    And the "Use preset" "button" should be enabled

  @javascript
  Scenario: Only users with the viewalluserpresets capability can see presets created by other users
    Given the following "permission override" exists:
      | role         | editingteacher                       |
      | capability   | mod/data:viewalluserpresets          |
      | permission   | Prohibit                             |
      | contextlevel | System                               |
      | reference    |                                      |
    When I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    Then I should see "Image gallery"
    And I should not see "Saved preset 1"
    And I should not see "Saved preset 2"
