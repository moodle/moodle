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

  Scenario: Teacher can import from preset page on an empty database
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    Then I should not see "Field mappings"
    And I should see "Image" in the "image" "table_row"

  Scenario: Teacher can import from preset page on a database with fields
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    Then I should see "Field mappings"
    And I should see "image"
    And I should see "Create a new field" in the "image" "table_row"

  Scenario: Teacher can import from preset page on a database with entries
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | field1          |
      | data1    | Student entry 1 |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    Then I should see "Field mappings"
    And I should see "image"
    And I should see "Create a new field" in the "image" "table_row"

  Scenario: Teacher can import from field page on a database with entries
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | field1          |
      | data1    | Student entry 1 |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    Then I should see "Field mappings"
    And I should see "title"
    And I should see "Create a new field" in the "title" "table_row"
    # We map existing field to keep the entry data
    And I set the field "id_title" to "Map to field1"
    And I click on "Continue" "button"
    And I follow "Database"
    And I should see "Student entry"

  Scenario: Teacher can import from zero state page on an empty database
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I click on "Import a preset" "button"
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    Then I should not see "Field mappings"
    And I should see "Image" in the "image" "table_row"

  Scenario: Importing a preset could create new fields
    Given the following "mod_data > fields" exist:
      | database | type | name    |
      | data1    | text | title   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Fields"
    And I should see "title"
    And I should not see "Description"
    And I should not see "image"
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I click on "Continue" "button"
    And I should see "Preset applied"
    Then I should see "title"
    And I should see "description" in the "description" "table_row"
    And I should see "image" in the "image" "table_row"

  Scenario: Importing a preset could create map fields
    Given the following "mod_data > fields" exist:
      | database | type | name            |
      | data1    | text | oldtitle        |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Fields"
    And I should see "oldtitle"
    And I should not see "Description"
    And I should not see "image"
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    # Let's map a field that is not mapped by default
    And I should see "Create a new field" in the "oldtitle" "table_row"
    And I set the field "id_title" to "Map to oldtitle"
    And I click on "Continue" "button"
    And I should see "Preset applied"
    Then I should not see "oldtitle"
    And I should see "title"
    And I should see "description" in the "description" "table_row"
    And I should see "image" in the "image" "table_row"

  Scenario: Importing same preset twice doesn't show mapping dialogue
    # Importing a preset on an empty database doesn't show the mapping dialogue, so we add a field for the database
    # not to be empty.
    Given the following "mod_data > fields" exist:
      | database | type | name    |
      | data1    | text | title   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I should see "Field mappings"
    And I click on "Continue" "button"
    And I should see "Preset applied"
    And I follow "Presets"
    And I choose the "Import preset" item in the "Action" action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    And I click on "Import preset and apply" "button"
    Then I should not see "Field mappings"
    And I should see "Preset applied"

  Scenario: Teacher can import from field page on a non-empty database and previous fields will be removed
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I click on "Actions" "button"
    And I choose "Import preset" in the open action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I click on "Continue" "button"
    Then I should see "Preset applied."
    And I follow "Fields"
    And I should see "image"
    And I should see "title"
    And I should not see "Test field name"
