@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload a media file
  In order to work with media files
  As a user
  I need to be able to upload and change settings of media files

  Background:
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor

  Scenario: Clicking on the media button in the TinyMCE editor opens the insert media dialog
    Then "Insert media" "dialogue" should exist
    And "Browse repositories" "button" should exist in the "Insert media" "dialogue"
    And I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And "File picker" "dialogue" should exist

  @_file_upload
  Scenario Outline: Insert and update media in the TinyMCE editor
    Given I click on "Browse repositories" "button"
    When I upload "/lib/editor/tiny/tests/behat/fixtures/<fixturefile>" to the file picker for TinyMCE
    Then "Media details" "dialogue" should exist
    And "Media title" "field" should exist in the "Media details" "dialogue"
    And "Add custom thumbnail" "button" <thumbnailaction> in the "Media details" "dialogue"
    And "Subtitles and captions" "link" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "<fixturetitle>"
    And the field "Show controls" in the "Media details" "dialogue" matches value "1"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "0"
    And the field "Muted" in the "Media details" "dialogue" matches value "0"
    And the field "Loop" in the "Media details" "dialogue" matches value "0"
    And <originalsizeverify>
    And <customsizeverify1>
    And I click on "Delete media" "button" in the "Media details" "dialogue"
    And "Delete media" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete media" "dialogue"
    And "Insert media" "dialogue" should exist
    And I click on "Browse repositories" "button"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/<fixturefile>" to the file picker for TinyMCE
    And I click on "Overwrite" "button"
    And I set the field "Media title" to "<newfixturetitle>"
    And I set the field "Autoplay" to "1"
    And I set the field "Muted" to "1"
    And I set the field "Loop" to "1"
    And <customsizestep>
    And <customsizeaction>
    And I click on "Subtitles and captions" "link" in the "Media details" "dialogue"
    And I click on "Browse repositories..." "button" in the "Media details" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/<subtitlefile>" to the file picker for TinyMCE
    And I set the field "Language" in the "Media details" "dialogue" to "<subtitlelangcode>"
    And I set the field "Label" in the "Media details" "dialogue" to "<subtitletitle>"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I switch to the "Description" TinyMCE editor iframe
    And "//*[contains(@data-id, 'id_description_editor')]//<mediatype>[@title='<newfixturetitle>' and @autoplay='autoplay' and @loop='loop' and @muted='true' and @controls='controls']" "xpath_element" should exist
    And "//*[contains(@data-id, 'id_description_editor')]//<mediatype>//source[contains(@src, '<fixturefile>')]" "xpath_element" should exist
    And "//*[contains(@data-id, 'id_description_editor')]//<mediatype>//track[contains(@src, '<subtitlefile>') and @kind='subtitles' and contains(@srclang, '<subtitlelang>') and @label='<subtitletitle>']" "xpath_element" should exist
    And I switch to the main frame
    And I select the "video" element in position "1" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And the field "Media title" in the "Media details" "dialogue" matches value "<newfixturetitle>"
    And the field "Show controls" in the "Media details" "dialogue" matches value "1"
    And the field "Autoplay" in the "Media details" "dialogue" matches value "1"
    And the field "Muted" in the "Media details" "dialogue" matches value "1"
    And the field "Loop" in the "Media details" "dialogue" matches value "1"
    And <customsizeverify2>
    And <customsizeverify3>
    And I click on "Subtitles and captions" "link" in the "Media details" "dialogue"
    And the field "Subtitle track URL" in the "Media details" "dialogue" does not match value ""
    And the field "Language" in the "Media details" "dialogue" matches value "<subtitlelangcode>"
    And the field "Label" in the "Media details" "dialogue" matches value "<subtitletitle>"

    Examples:
      | mediatype | fixturefile      | fixturetitle | newfixturetitle   | subtitlefile        | subtitletitle             | subtitlelangcode | subtitlelang | thumbnailaction  | originalsizeverify                                                            | customsizeverify1                                                           | customsizeverify2                                                           | customsizeverify3                                                       | customsizestep                                                             | customsizeaction                                                         |
      | video     | moodle-logo.mp4  | moodle-logo  | Moodle LMS Logo   | subtitle-sample.vtt | Subtitle sample for video | en               | English      | should exist     | the field "Original size" in the "Media details" "dialogue" matches value "1" | the field "Custom size" in the "Media details" "dialogue" matches value "0" | the field "Custom size" in the "Media details" "dialogue" matches value "1" | the field "Width" in the "Media details" "dialogue" matches value "300" | I click on "Custom size" "radio" in the "Media details" "dialogue"         | I set the field "Width" in the "Media details" "dialogue" to "300"       |
      | audio     | audio-sample.mp3 | audio-sample | Sample Audio File | subtitle-sample.vtt | Subtitle sample for audio | fr               | French       | should not exist | "Original size" "field" should not exist in the "Media details" "dialogue"    | "Custom size" "field" should not exist in the "Media details" "dialogue"    | "Custom size" "field" should not exist in the "Media details" "dialogue"    | "Width" "field" should not exist in the "Media details" "dialogue"      | "Original size" "field" should not exist in the "Media details" "dialogue" | "Custom size" "field" should not exist in the "Media details" "dialogue" |

  @_file_upload
  Scenario: Add custom thumbnail to a video in TinyMCE editor
    Given I click on "Browse repositories" "button" in the "Insert media" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/moodle-logo.mp4" to the file picker for TinyMCE
    When I click on "Add custom thumbnail" "button" in the "Media details" "dialogue"
    Then "Insert media thumbnail" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media thumbnail" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And "Media thumbnail" "dialogue" should exist
    And "tiny-media-thumbnail-preview" "region" should exist in the "Media thumbnail" "dialogue"
    And I click on "Delete media thumbnail" "button" in the "Media thumbnail" "dialogue"
    And "Delete media thumbnail" "dialogue" should exist
    And I click on "Delete" "button" in the "Delete media thumbnail" "dialogue"
    And I click on "Browse repositories" "button" in the "Insert media thumbnail" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I click on "Overwrite" "button" in the "File exists" "dialogue"
    And I click on "Next" "button" in the "Media thumbnail" "dialogue"
    But "Add custom thumbnail" "button" should not exist in the "Media details" "dialogue"
    And "Change thumbnail" "button" should exist in the "Media details" "dialogue"
    And I click on "Delete thumbnail" "button" in the "Media details" "dialogue"
    And I click on "Delete" "button" in the "Delete thumbnail" "dialogue"
    And "Media details" "dialogue" should exist
    And "Change thumbnail" "button" should not exist in the "Media details" "dialogue"
    And "Delete thumbnail" "button" should not exist in the "Media details" "dialogue"
    And I click on "Add custom thumbnail" "button" in the "Media details" "dialogue"
    And "Insert media thumbnail" "dialogue" should exist
    And I click on "Browse repositories" "button" in the "Insert media thumbnail" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I click on "Overwrite" "button" in the "File exists" "dialogue"
    And I click on "Next" "button" in the "Media thumbnail" "dialogue"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I switch to the "Description" TinyMCE editor iframe
    And "//*[contains(@data-id, 'id_description_editor')]//video[contains(@poster, 'moodle-logo.png')]" "xpath_element" should exist

  Scenario: Embed external video link - External video service
    Given the "mediaplugin" filter is "on"
    And I enable "youtube" "media" plugin
    And I disable "videojs" "media" plugin
    And I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    When I set the field "Or add via URL" to "https://www.youtube.com/watch?v=JeimE8Wz6e4"
    And I click on "Add" "button" in the "Insert media" "dialogue"
    Then "Media details" "dialogue" should exist
    And "Media title" "field" should exist in the "Media details" "dialogue"
    And "Show controls" "field" should not exist in the "Media details" "dialogue"
    And "Autoplay" "field" should not exist in the "Media details" "dialogue"
    And "Muted" "field" should not exist in the "Media details" "dialogue"
    And "Loop" "field" should not exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "https://www.youtube.com/watch?v=JeimE8Wz6e4"
    And I set the field "Media title" to "Hey, that is pretty good!"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I switch to the "Description" TinyMCE editor iframe
    And "//*[contains(@data-id, 'id_description_editor')]//a[@class='external-media-provider' and @href='https://www.youtube.com/watch?v=JeimE8Wz6e4' and normalize-space(text())='Hey, that is pretty good!']" "xpath_element" should exist
    And I switch to the main frame
    And I select the "a" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And the field "Media title" in the "Media details" "dialogue" matches value "Hey, that is pretty good!"
    And I click on "Cancel" "button" in the "Media details" "dialogue"
    And I press "Update profile"
    And "//span[contains(@class, 'mediaplugin_youtube')]//iframe[@title='Hey, that is pretty good!']" "xpath_element" should exist
