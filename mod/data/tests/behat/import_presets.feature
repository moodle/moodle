@mod @mod_data @javascript @_file_upload
Feature: Users can import presets
  In order to use presets
  As a user
  I need to import and apply presets from zip files

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

  Scenario: Teacher can import from field page on a database with fields and previous fields will be
    removed
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "Import" "link"
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Choose file" filemanager
    When I click on "Save" "button"
    And I click on "Continue" "button"
    Then I should see "The preset has been successfully applied."
    And I follow "Fields"
    And I should see "image"
    And I should see "title"
    And I should not see "Test field name"
