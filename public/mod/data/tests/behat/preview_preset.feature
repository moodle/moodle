@mod @mod_data
Feature: Users can preview presets
  In order to find the preset I am looking for
  As a teacher
  I need to preview the database activity presets

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "tags" exist:
      | name | isstandard |
      | Tag1 | 1          |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |
    And I am on the "Test database name" "data activity" page logged in as teacher1

  @javascript @_file_upload
  Scenario: Preview a user preset as list view template in database
    Given I follow "Presets"
    And I click on "Actions" "button"
    And I choose "Import preset" in the open action menu
    And I upload "mod/data/tests/fixtures/behat_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "Saved preset by teacher1"
    And I set the field "Description" to "Behat test preset"
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    When I follow "Presets"
    And I click on "Saved preset by teacher1" "link"
    # Check list template preview fields.
    Then I should see "Saved preset by teacher1"
    And I should see "List header" in the "template-preview" "region"
    And I should see "List footer" in the "template-preview" "region"
    And I should see "List template content" in the "template-preview" "region"
    And I should see "My text field" in the "template-preview" "region"
    And I should see "This is a short text" in the "template-preview" "region"
    And I should see "My multiple selection" in the "template-preview" "region"
    And I should see "Multi 1" in the "template-preview" "region"
    And I should see "My date field" in the "template-preview" "region"
    And I should see "My checkbox field" in the "template-preview" "region"
    And I should see "Check 2" in the "template-preview" "region"
    And I should see "My geo field" in the "template-preview" "region"
    And I should see "41.3912°N 2.1639°E" in the "template-preview" "region"
    And I should see "My menu field" in the "template-preview" "region"
    And I should see "Menu 2" in the "template-preview" "region"
    And I should see "My number field" in the "template-preview" "region"
    And I should see "1234" in the "template-preview" "region"
    And I should see "My radio field" in the "template-preview" "region"
    And I should see "Radio 2" in the "template-preview" "region"
    And I should see "My text area field" in the "template-preview" "region"
    And I should see "This is a text area" in the "template-preview" "region"
    And I should see "My URL field" in the "template-preview" "region"
    And I should see "https://example.com" in the "template-preview" "region"
    And I should see "My file field" in the "template-preview" "region"
    And "Comma-separated values" "icon" should exist in the "template-preview" "region"
    And I should see "samplefile.csv" in the "template-preview" "region"
    And I should see "My picture field" in the "template-preview" "region"
    # Test CSS and JS templates.
    And I should not see "This content should not be displayed" in the "template-preview" "region"
    And I should not see "This text should change" in the "template-preview" "region"
    And I should see "New value" in the "template-preview" "region"

  @javascript @_file_upload
  Scenario: Preview a user preset as single view template in database
    Given I follow "Presets"
    And I click on "Actions" "button"
    And I choose "Import preset" in the open action menu
    And I upload "mod/data/tests/fixtures/behat_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "Saved preset by teacher1"
    And I set the field "Description" to "Behat test preset"
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    When I follow "Presets"
    And I click on "Saved preset by teacher1" "link"
    And I set the field "Templates tertiary navigation" to "Single view template"
    # Check single view template preview fields.
    Then I should see "Saved preset by teacher1"
    And I should see "Single template content" in the "template-preview" "region"
    And I should see "My text field" in the "template-preview" "region"
    And I should see "This is a short text" in the "template-preview" "region"
    And I should see "My multiple selection" in the "template-preview" "region"
    And I should see "Multi 2" in the "template-preview" "region"
    And I should see "My date field" in the "template-preview" "region"
    And I should see "My checkbox field" in the "template-preview" "region"
    And I should see "Check 2" in the "template-preview" "region"
    And I should see "My geo field" in the "template-preview" "region"
    And I should see "41.3912°N 2.1639°E" in the "template-preview" "region"
    And I should see "My menu field" in the "template-preview" "region"
    And I should see "Menu 2" in the "template-preview" "region"
    And I should see "My number field" in the "template-preview" "region"
    And I should see "1234" in the "template-preview" "region"
    And I should see "My radio field" in the "template-preview" "region"
    And I should see "Radio 2" in the "template-preview" "region"
    And I should see "My text area field" in the "template-preview" "region"
    And I should see "This is a text area" in the "template-preview" "region"
    And I should see "My URL field" in the "template-preview" "region"
    And I should see "https://example.com" in the "template-preview" "region"
    And I should see "My file field" in the "template-preview" "region"
    And "Comma-separated values" "icon" should exist in the "template-preview" "region"
    And I should see "samplefile.csv" in the "template-preview" "region"
    And I should see "My picture field" in the "template-preview" "region"
    # Test CSS and JS templates.
    And I should not see "This content should not be displayed" in the "template-preview" "region"
    And I should not see "This text should change" in the "template-preview" "region"
    And I should see "New value" in the "template-preview" "region"

  @javascript
  Scenario: Preview a plugin preset in database
    Given I follow "Presets"
    When I click on "Journal" "link"
    Then I should see "Journal"
    And I should see "This is a short text"
    And I should see "This is a text area"
    And I select "Single view template" from the "Templates tertiary navigation" singleselect
    And I should see "This is a short text"
    And I should see "This is a text area"
    And I should see "This is a short text" in the "template-preview" "region"

  @javascript
  Scenario: Use back button to return to the presets page in database
    Given I follow "Presets"
    And I click on "Image gallery" "link"
    And I should see "Image gallery"
    When I click on "Back" "button"
    Then I should see "Choose a preset to use as a starting point."

  @javascript
  Scenario: Apply plugin preset from preview in database
    Given I follow "Presets"
    And I click on "Image gallery" "link"
    When I click on "Use this preset" "button"
    Then I should see "image"
    And I should see "title"

  @javascript @_file_upload
  Scenario: Apply user preset from preview in database
    Given I follow "Presets"
    And I click on "Actions" "button"
    And I choose "Import preset" in the open action menu
    And I upload "mod/data/tests/fixtures/behat_preset.zip" file to "Preset file" filemanager
    When I click on "Import preset and apply" "button"
    And I follow "Templates"
    And I click on "Actions" "button"
    And I choose "Publish preset on this site" in the open action menu
    And I set the field "Name" to "Saved preset by teacher1"
    And I set the field "Description" to "Behat test preset"
    And I click on "Save" "button" in the "Save all fields and templates and publish as preset on this site" "dialogue"
    When I follow "Presets"
    And I click on "Saved preset by teacher1" "link"
    And I click on "Use this preset" "button"
    Then I should see "Preset applied"
    And I should see "My URL field"
