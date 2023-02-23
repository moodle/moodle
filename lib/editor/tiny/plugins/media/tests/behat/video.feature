@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload a video
  In order to work with videos
  As a user
  I need to be able to upload and manipulate videos

  Scenario: Clicking on the Video button in the TinyMCE editor opens the video dialog
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist

  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    Then "File picker" "dialogue" should exist

  @_file_upload
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I follow "Video"
    And I click on "Browse repositories..." "button" in the "#id_description_editor_video .tiny_media_source.tiny_media_media_source" "css_element"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    When I click on "Insert media" "button"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
