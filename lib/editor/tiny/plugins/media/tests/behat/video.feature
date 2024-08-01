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
    And I click on "Browse repositories" "button"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    When I click on "Save" "button"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor

  @_file_upload
  Scenario: Insert and update video in the TinyMCE editor
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    And I click on "Save" "button"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    When I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I set the field "Title" to "Test title"
    And I click on "Autoplay" "checkbox"
    And I click on "Muted" "checkbox"
    And I click on "Loop" "checkbox"
    And I click on "Save" "button"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And the field "Title" matches value "Test title"
    And the field "Autoplay" matches value "1"
    And the field "Muted" matches value "1"
    And the field "Loop" matches value "1"
