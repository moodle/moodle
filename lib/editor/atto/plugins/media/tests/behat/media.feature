@editor @editor_atto @atto @atto_media @_file_upload
Feature: Add media to Atto
  To write rich text - I need to add media.

  @javascript
  Scenario: Insert some media
    Given I log in as "admin"
    And I follow "Manage my private files..."
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.webm" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I follow "My profile" in the user menu
    And I follow "My blog entries"
    And I follow "Add a new entry"
    And I set the field "Blog entry body" to "<p>Media test</p>"
    And I select the text in the "Blog entry body" Atto editor
    And I set the field "Entry title" to "The best video in the entire world (not really)"
    And I click on "Media" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And I set the field "Enter name" to "It's the logo"
    And I click on "Insert media" "button"
    And I click on "Save changes" "button"
    Then "video" "css_element" should be visible

