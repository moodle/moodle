@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload an audio
  In order to work with audios
  As a user
  I need to be able to upload and manipulate audios

  Scenario: Clicking on the Audio button in the TinyMCE editor opens the audio dialog
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
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker and add audio file
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/audio-sample.mp3" to the file picker for TinyMCE
    And I click on "Save" "button"
    Then I select the "audio" element in position "1" of the "Description" TinyMCE editor

  @_file_upload
  # The following scenario covers:
  # 1. Preview uploaded audio.
  # 2. Delete previewed audio.
  Scenario: Preview audio before embedding it into TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/audio-sample.mp3" to the file picker for TinyMCE
    # Preview inserted audio.
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "audio-sample"
    And "Delete media" "button" should exist in the "Media details" "dialogue"
    # Add custom thumbnail button should not exist for audio type.
    And "Add custom thumbnail" "button" should not exist in the "Media details" "dialogue"
    # Media controls.
    And the field "Show controls" in the "Media details" "dialogue" matches value "1"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "0"
    And the field "Muted" in the "Media details" "dialogue" matches value "0"
    And the field "Loop" in the "Media details" "dialogue" matches value "0"
    # Size configuration should not exist for audio type.
    And "Original size" "radio" should not exist in the "Media details" "dialogue"
    And "Custom size" "radio" should not exist in the "Media details" "dialogue"
    # Subtitles, captions, description, tracks and metadata button for video/audio.
    And "Subtitles and captions" "link" should exist in the "Media details" "dialogue"
    # Let's delete the previwed audio.
    And I click on "Delete media" "button" in the "Media details" "dialogue"
    And "Delete media" "dialogue" should exist
    And "Delete" "button" should exist in the "Delete media" "dialogue"
    And I click on "Delete" "button" in the "Delete media" "dialogue"
    And "Insert media" "dialogue" should exist
    And "tiny-insert-media" "region" should exist

  @_file_upload
  Scenario: Add subtitles to an audio before embedding it into TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/audio-sample.mp3" to the file picker for TinyMCE
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    # Add media subtitles.
    And I click on "Subtitles and captions" "link" in the "Media details" "dialogue"
    And I click on "Browse repositories..." "button" in the "Media details" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/subtitle-sample.vtt" to the file picker for TinyMCE
    And "File picker" "dialogue" should not exist
    And I set the field "Language" in the "Media details" "dialogue" to "en"
    And I set the field "Label" in the "Media details" "dialogue" to "Subtitle sample"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I select the "audio" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And I click on "Subtitles and captions" "link" in the "Media details" "dialogue"
    And the field "Subtitle track URL" in the "Media details" "dialogue" does not match value ""
    And the field "Language" in the "Media details" "dialogue" matches value "en"
    And the field "Label" in the "Media details" "dialogue" matches value "Subtitle sample"

  @_file_upload
  Scenario: Insert and update audio in the TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/audio-sample.mp3" to the file picker for TinyMCE
    # Preview inserted audio.
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "audio-sample"
    And "Delete media" "button" should exist in the "Media details" "dialogue"
    # Embed media into tiny and preview it by selecting it from tiny.
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I select the "audio" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    # Let's update audio data.
    And I set the field "Media title" in the "Media details" "dialogue" to "Test title"
    And I click on "Autoplay" "checkbox" in the "Media details" "dialogue"
    And I click on "Muted" "checkbox" in the "Media details" "dialogue"
    And I click on "Loop" "checkbox" in the "Media details" "dialogue"
    And I click on "Save" "button" in the "Media details" "dialogue"
    # Let's preview and check the updated data.
    And I select the "audio" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And the field "Media title" in the "Media details" "dialogue" matches value "Test title"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "1"
    And the field "Muted" in the "Media details" "dialogue" matches value "1"
    And the field "Loop" in the "Media details" "dialogue" matches value "1"
