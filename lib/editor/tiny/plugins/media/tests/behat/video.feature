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
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker and upload video file
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    And I click on "Save" "button"
    Then I select the "video" element in position "1" of the "Description" TinyMCE editor

  @_file_upload
  # The following scenario covers:
  # 1. Preview uploaded video.
  # 2. Delete previewed video.
  Scenario: Preview video before embedding it into TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    # Preview inserted video.
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "moodle-logo"
    # Delete media preview button.
    And "Delete media" "button" should exist in the "Media details" "dialogue"
    # Only video has add custom thumbnail button.
    And "Add custom thumbnail" "button" should exist in the "Media details" "dialogue"
    # Media controls.
    And the field "Show controls" in the "Media details" "dialogue" matches value "1"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "0"
    And the field "Muted" in the "Media details" "dialogue" matches value "0"
    And the field "Loop" in the "Media details" "dialogue" matches value "0"
    # Only video has size configuration.
    And the field "Original size" in the "Media details" "dialogue" matches value "1"
    And the field "Custom size" in the "Media details" "dialogue" matches value "0"
    # Subtitles, captions, description, tracks and metadata button for video/audio.
    And "Subtitles and captions" "link" should exist in the "Media details" "dialogue"
    # Let's delete the previwed video.
    And I click on "Delete media" "button" in the "Media details" "dialogue"
    And "Delete media" "dialogue" should exist
    And "Delete" "button" should exist in the "Delete media" "dialogue"
    And I click on "Delete" "button" in the "Delete media" "dialogue"
    And "Insert media" "dialogue" should exist
    And "tiny-insert-media" "region" should exist

  @_file_upload
  # The following screnario covers:
  # 1. Add video custom thumbnail.
  # 2. Preview uploaded custom thumbnail.
  # 3. Delete previewed thumbnail.
  # 4. Delete added thumbnail.
  Scenario: Add custom thumbnail from local storage to a video
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    # Add custom thumbnail.
    And "Add custom thumbnail" "button" should exist in the "Media details" "dialogue"
    And I click on "Add custom thumbnail" "button" in the "Media details" "dialogue"
    And "Insert media thumbnail" "dialogue" should exist
    # And "tiny-insert-media" "region" should exist in the "Insert media thumbnail" "dialogue"
    And I click on "Browse repositories" "button" in the "Insert media thumbnail" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    # Delete previewed thumbnail.
    And "Media thumbnail" "dialogue" should exist
    And "tiny-media-thumbnail-preview" "region" should exist in the "Media thumbnail" "dialogue"
    And "Delete media thumbnail" "button" should exist in the "Media thumbnail" "dialogue"
    And I click on "Delete media thumbnail" "button" in the "Media thumbnail" "dialogue"
    And "Delete media thumbnail" "dialogue" should exist
    And "Delete" "button" should exist in the "Delete media thumbnail" "dialogue"
    And I click on "Delete" "button" in the "Delete media thumbnail" "dialogue"
    # Add back the same thumbnail link again.
    And "Insert media thumbnail" "dialogue" should exist
    # And "tiny-insert-media" "region" should exist in the "Insert media thumbnail" "dialogue"
    And I click on "Browse repositories" "button" in the "Insert media thumbnail" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    # Same file upload will cause "File exists" dialogue to display.
    And "File exists" "dialogue" should exist
    And I click on "Overwrite" "button" in the "File exists" "dialogue"
    And "Media thumbnail" "dialogue" should exist
    And "tiny-media-thumbnail-preview-footer" "region" should exist in the "Media thumbnail" "dialogue"
    And "Next" "button" should exist in the "Media thumbnail" "dialogue"
    And I click on "Next" "button" in the "Media thumbnail" "dialogue"
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    # Now "Add custom thumbnail" button should not exist.
    And "Add custom thumbnail" "button" should not exist in the "Media details" "dialogue"
    # And Change and Delete thumbnail buttons now exist.
    And "Change thumbnail" "button" should exist in the "Media details" "dialogue"
    And "Delete thumbnail" "button" should exist in the "Media details" "dialogue"
    # Delete added thumbnail.
    And I click on "Delete thumbnail" "button" in the "Media details" "dialogue"
    And "Delete thumbnail" "dialogue" should exist
    And "Delete" "button" should exist in the "Delete thumbnail" "dialogue"
    And I click on "Delete" "button" in the "Delete thumbnail" "dialogue"
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    # After the added thumbnail is deleted, both Change and Delete thumbnial buttons should not exist anymore.
    And "Change thumbnail" "button" should not exist in the "Media details" "dialogue"
    And "Delete thumbnail" "button" should not exist in the "Media details" "dialogue"
    # After the added thumbnail is deleted, "Add custom thubmnail" should exist again.
    And "Add custom thumbnail" "button" should exist in the "Media details" "dialogue"

  @_file_upload
  Scenario: Add subtitles to video before embedding it into TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
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
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And I click on "Subtitles and captions" "link" in the "Media details" "dialogue"
    And the field "Subtitle track URL" in the "Media details" "dialogue" does not match value ""
    And the field "Language" in the "Media details" "dialogue" matches value "en"
    And the field "Label" in the "Media details" "dialogue" matches value "Subtitle sample"

  @_file_upload
  Scenario: Insert and update video in the TinyMCE editor
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button"
    And "File picker" "dialogue" should exist
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    And "Media details" "dialogue" should exist
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And I set the field "Media title" in the "Media details" "dialogue" to "Test title"
    And I click on "Autoplay" "checkbox" in the "Media details" "dialogue"
    And I click on "Muted" "checkbox" in the "Media details" "dialogue"
    And I click on "Loop" "checkbox" in the "Media details" "dialogue"
    And the field "Original size" in the "Media details" "dialogue" matches value "1"
    And the field "Custom size" in the "Media details" "dialogue" matches value "0"
    And I click on "Custom size" "radio" in the "Media details" "dialogue"
    And I set the field "Width" in the "Media details" "dialogue" to "300"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And the field "Media title" in the "Media details" "dialogue" matches value "Test title"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "1"
    And the field "Muted" in the "Media details" "dialogue" matches value "1"
    And the field "Loop" in the "Media details" "dialogue" matches value "1"
    And the field "Custom size" in the "Media details" "dialogue" matches value "1"
    And the field "Width" in the "Media details" "dialogue" matches value "300"
