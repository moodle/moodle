@mod @mod_data
Feature: Users can view and manage data presets
  In order to use presets
  As a user
  I need to view, manage and use presets

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
      | activity | name                | intro | course | idnumber |
      | data     | Mountain landscapes | n     | C1     | data1    |
    And the following "mod_data > presets" exist:
      | database | name                     | description                        | user     |
      | data1    | Saved preset 1           | The preset1 has description        | admin    |
      | data1    | Saved preset 2           |                                    | admin    |
      | data1    | Saved preset by teacher1 | This preset has also a description | teacher1 |

  @javascript
  Scenario: Admins can delete saved presets
    Given I am on the "Mountain landscapes" "data activity" page logged in as admin
    When I follow "Presets"
    Then I should see "Choose a preset to use as a starting point."
    And I should see "Image gallery"
    And I should see "Saved preset 1"
    And I should see "Saved preset 2"
    And I should see "Saved preset by teacher1"
    # Plugin presets can't be removed.
    And the "Delete" item should not exist in the "Actions" action menu of the "Image gallery" "table_row"
    # The admin should be able to delete saved presets.
    But the "Delete" item should exist in the "Actions" action menu of the "Saved preset 1" "table_row"
    And the "Delete" item should exist in the "Actions" action menu of the "Saved preset 2" "table_row"
    And the "Delete" item should exist in the "Actions" action menu of the "Saved preset by teacher1" "table_row"

  @javascript
  Scenario: Teachers can see and use presets
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    Then I should see "Choose a preset to use as a starting point."
    And I should see "Image gallery"
    And I should see "Use this preset to collect images." in the "Image gallery" "table_row"
    And I should see "Saved preset 1"
    And I should see "The preset1 has description" in the "Saved preset 1" "table_row"
    And I should see "Saved preset 2"
    And I should see "Saved preset by teacher1"
    And I should see "This preset has also a description" in the "Saved preset by teacher1" "table_row"
    # Plugin presets can't be removed.
    And the "Delete" item should not exist in the "Actions" action menu of the "Image gallery" "table_row"
    # Teachers should be able to delete their saved presets.
    And the "Delete" item should exist in the "Actions" action menu of the "Saved preset by teacher1" "table_row"
    # Teachers can't delete the presets they haven't created.
    And the "Delete" item should not exist in the "Actions" action menu of the "Saved preset 1" "table_row"
    # The "Use this preset" button should be enabled only when a preset is selected.
    And the "Use this preset" "button" should be disabled
    And I click on "fullname" "radio" in the "Image gallery" "table_row"
    And the "Use this preset" "button" should be enabled

  @javascript
  Scenario: Only users with the viewalluserpresets capability can see presets created by other users
    Given the following "permission override" exists:
      | role         | editingteacher              |
      | capability   | mod/data:viewalluserpresets |
      | permission   | Prohibit                    |
      | contextlevel | System                      |
      | reference    |                             |
    When I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    Then I should see "Image gallery"
    And I should not see "Saved preset 1"
    And I should not see "Saved preset 2"
    But I should see "Saved preset by teacher1"

  @javascript
  Scenario: Teachers can save presets
    Given the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Templates"
    When I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    Then I should see "Name" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "Description" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should not be visible
    # Teacher should be able to save preset.
    And I set the field "Name" to "New saved preset"
    And I set the field "Description" to "My funny description goes here."
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "Preset saved."
    And I follow "Presets"
    And I should see "New saved preset"
    And I should see "My funny description goes here." in the "New saved preset" "table_row"
    # Teacher can't overwrite an existing preset that they haven't created.
    And I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "Saved preset 1"
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "A preset with this name already exists. Choose a different name."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should not be visible
    # Teacher can overwrite existing presets created by them, but they are not overwritten if the checkbox is not marked.
    And I set the field "Name" to "New saved preset"
    And I set the field "Description" to "This is a new description that shouldn't be saved."
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "A preset with this name already exists."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should be visible
    # Confirm the checkbox is still displayed and nothing happens if it's not checked and no change is done in the name.
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "A preset with this name already exists."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should be visible
    And I click on "Cancel" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I follow "Presets"
    And I should see "New saved preset"
    And I should see "My funny description goes here." in the "New saved preset" "table_row"
    And I should not see "This is a new description that shouldn't be saved."
    # But teacher can overwrite existing presets created by them.
    But I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "New saved preset"
    And I set the field "Description" to "This is a new description that will be overwritten."
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "A preset with this name already exists."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should be visible
    And I click on "Replace existing preset with this name and overwrite its contents" "checkbox" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "Preset saved."
    And I follow "Presets"
    And I should see "New saved preset"
    And I should see "This is a new description that will be overwritten." in the "New saved preset" "table_row"
    And I should not see "My funny description goes here." in the "New saved preset" "table_row"

  @javascript
  Scenario: Teachers can edit presets
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    # Plugin presets can't be edited.
    Then the "Edit" item should not exist in the "Actions" action menu of the "Image gallery" "table_row"
    # Teachers can't edit the presets they haven't created.
    And the "Edit" item should not exist in the "Actions" action menu of the "Saved preset 1" "table_row"
    # Teachers should be able to edit their saved presets.
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Name" to "RENAMED preset by teacher1"
    And I set the field "Description" to "My funny description goes here."
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see "Preset saved."
    And I should see "RENAMED preset by teacher1"
    And I should see "My funny description goes here." in the "RENAMED preset by teacher1" "table_row"
    And I should not see "Saved preset by teacher1"
    And I should not see "This preset has also a description"

  @javascript
  Scenario: Nothing happens when teachers edit a preset and do not change anything
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I choose "Edit" in the open action menu
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    Then I should not see "Preset saved."
    And I should see "Saved preset by teacher1"

  @javascript
  Scenario: Teachers can edit presets and overwrite them if they are the authors
    Given the following "mod_data > preset" exists:
      | database    | data1                                |
      | name        | Another preset created by teacher1   |
      | description | This description will be overwritten |
      | user        | teacher1                             |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Name" to "Another preset created by teacher1"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    Then I should see "A preset with this name already exists."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should be visible
    # If the checkbox is not selected, the preset shoudn't be saved.
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see "A preset with this name already exists."
    # But when I select the overwrite checkbox, the preset should be overwritten.
    But I click on "Replace existing preset with this name and overwrite its contents" "checkbox" in the "Edit preset" "dialogue"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see "Preset saved."
    And I should see "Another preset created by teacher1"
    And I should see "This preset has also a description" in the "Another preset created by teacher1" "table_row"
    And I should not see "Saved preset by teacher1"
    And I should not see "This description will be overwritten"

  @javascript
  Scenario: Teachers cannot overwrite presets if they are not the authors
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Name" to "Saved preset 1"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    Then I should see " A preset with this name already exists. Choose a different name."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should not be visible
    # If the teacher clicks again the Save button, the preset shoudn't be saved.
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see " A preset with this name already exists. Choose a different name."
    # But if they set a different name (which doesn't exist), the preset should be saved.
    And I set the field "Name" to "New saved preset"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see "Preset saved."
    And I should see "New saved preset"
    And I should see "This preset has also a description" in the "New saved preset" "table_row"
    And I should not see "Saved preset by teacher1"

  @javascript
  Scenario: Admins can overwrite presets even if they are not the authors
    Given I am on the "Mountain landscapes" "data activity" page logged in as admin
    When I follow "Presets"
    And I open the action menu in "Saved preset by teacher1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Name" to "Saved preset 1"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    Then I should see " A preset with this name already exists."
    And "Replace existing preset with this name and overwrite its contents" "checkbox" should be visible
    # But when admin selects the overwrite checkbox, the preset should be overwritten.
    But I click on "Replace existing preset with this name and overwrite its contents" "checkbox" in the "Edit preset" "dialogue"
    And I click on "Save" "button" in the "Edit preset" "dialogue"
    And I should see "Preset saved."
    And I should see "Saved preset 1"
    And I should see "This preset has also a description" in the "Saved preset 1" "table_row"
    And I should not see "Saved preset by teacher1"
    And I should not see "The preset1 has description"

  @javascript
  Scenario: Teachers can delete their own presets
    Given the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And the following "mod_data > presets" exist:
      | database | name                     | description                     | user     |
      | data1    | Saved preset by teacher1 | My funny description goes here. | teacher1 |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    And I should see "Image gallery"
    And I should see "Saved preset 1"
    And I should see "Saved preset by teacher1"
    # Plugin presets can't be removed.
    And the "Delete" item should not exist in the "Actions" action menu of the "Image gallery" "table_row"
    # The teacher should not be able to delete presets saved by others.
    And the "Delete" item should not exist in the "Actions" action menu of the "Saved preset 1" "table_row"
    # The teacher should be able to delete their own preset.
    And I open the action menu in "Saved preset by teacher" "table_row"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete preset Saved preset by teacher1?" "dialogue"
    And I should see "Preset deleted"
    And I should not see "Saved preset by teacher1"

  @javascript
  Scenario: Teachers can preview a saved preset from the notification
    Given the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "New saved preset"
    And I set the field "Description" to "My funny description goes here."
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    And I should see "Preset saved"
    When I click on "Preview preset" "link"
    Then I should see "Preview"
    And I should see "New saved preset"
    And I should see "Test field name"
    And I should see "This is a short text"
    Then "Use this preset" "button" should exist

  Scenario: Teachers can export any saved preset
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    When I follow "Presets"
    # Plugin presets can't be exported.
    And the "Export" item should not exist in the "Actions" action menu of the "Image gallery" "table_row"
    # The teacher should be able to export any saved preset.
    And the "Export" item should exist in the "Actions" action menu of the "Saved preset by teacher1" "table_row"
    And following "Export" in the "Saved preset by teacher1" "table_row" should download a file that:
      | Contains file in zip | preset.xml |
    And the "Export" item should exist in the "Actions" action menu of the "Saved preset 1" "table_row"
    And following "Export" in the "Saved preset 1" "table_row" should download a file that:
      | Contains file in zip | preset.xml |

  @javascript @_file_upload
  Scenario Outline: Admins and Teachers can load a preset from a file
    Given I am on the "Mountain landscapes" "data activity" page logged in as <user>
    When I follow "Presets"
    Then I click on "Actions" "button"
    And I choose "Import preset" in the open action menu
    And I upload "mod/data/tests/fixtures/image_gallery_preset.zip" file to "Preset file" filemanager
    Then I click on "Import preset and apply" "button" in the ".modal-dialog" "css_element"
    Then I should see "Preset applied"
    # I am on the field page.
    And I should see "Manage fields"
    Then I should see "Preset applied"

    Examples:
      | user     |
      | admin    |
      | teacher1 |

  @javascript
  Scenario Outline: Teachers can use "Use this preset" actions menu next to each preset.
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I open the action menu in "<Preset Name>" "table_row"
    When I click on "Use this preset" "link" in the "<Preset Name>" "table_row"
    Then I should see "Preset applied"

    Examples:
      | Preset Name                          |
      | Image gallery                        |
      | Saved preset 1 (Admin User)          |
      | Saved preset by teacher1 (Teacher 1) |

  @javascript
  Scenario Outline: Teachers can use "Preview" actions menu next to each preset.
    Given I am on the "Mountain landscapes" "data activity" page logged in as teacher1
    And I follow "Presets"
    And I open the action menu in "<Preset Name>" "table_row"
    When I choose "Preview" in the open action menu
    Then I should see "Preview of <Preset preview name>"

    Examples:
      | Preset Name                          | Preset preview name      |
      | Image gallery                        | Image gallery            |
      | Saved preset 1 (Admin User)          | Saved preset 1           |
      | Saved preset by teacher1 (Teacher 1) | Saved preset by teacher1 |
