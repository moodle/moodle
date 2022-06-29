@editor @editor_atto @atto @atto_media @_file_upload
Feature: Add media to Atto
  To write rich text - I need to add media.

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "admin"
    And I change window size to "large"
    And I follow "Manage private files..."
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.webm" file to "Files" filemanager
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.mp4" file to "Files" filemanager
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.png" file to "Files" filemanager
    And I upload "lib/editor/atto/tests/fixtures/pretty-good-en.vtt" file to "Files" filemanager
    And I upload "lib/editor/atto/tests/fixtures/pretty-good-sv.vtt" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I follow "Profile" in the user menu
    And I follow "Blog entries"
    And I follow "Add a new entry"
    And I set the field "Blog entry body" to "<p>Media test</p>"
    And I select the text in the "Blog entry body" Atto editor
    And I set the field "Entry title" to "The best video in the entire world (not really)"
    And I click on "Insert or edit an audio/video file" "button"

  @javascript
  Scenario: Insert some media as a link
    Given I click on "Browse repositories..." "button" in the "#id_summary_editor_link .atto_media_source.atto_media_link_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And the field "Enter name" matches value "moodle-logo.webm"
    And I wait until the page is ready
    And I click on "Insert media" "button"
    When I click on "Save changes" "button"
    Then "//a[. = 'moodle-logo.webm']" "xpath_element" should exist

  @javascript @atto_media_video
  Scenario: Insert some media as a plain video
    Given I click on "Video" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And I click on "Add alternative source" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_media_source:nth-of-type(2)" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.mp4" "link"
    And I click on "Select this file" "button"
    When I click on "Insert media" "button"
    Then "//video[descendant::source[contains(@src, 'moodle-logo.webm')]][descendant::source[contains(@src, 'moodle-logo.mp4')]]" "xpath_element" should exist

  @javascript @atto_media_video
  Scenario: Insert some media as a video with display settings
    Given I click on "Video" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And I click on "Display options" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_poster_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I set the field with xpath "//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_width_entry ')]" to "420"
    And I set the field with xpath "//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_height_entry ')]" to "69"
    And I set the field "Enter title" to "VideoTitle"
    And I click on "Display options" "link"
    When I click on "Insert media" "button"
    Then "//video[descendant::source[contains(@src, 'moodle-logo.webm')]][contains(@poster, 'moodle-logo.png')][@width=420][@height=69][@title='VideoTitle']" "xpath_element" should exist

  @javascript @atto_media_video
  Scenario: Insert some media as a video with advanced settings
    Given I click on "Video" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And I click on "Advanced settings" "link"
    And the field "Show controls" matches value "1"
    And I set the field "Play automatically" to "1"
    And I set the field "Muted" to "1"
    And I set the field "Loop" to "1"
    When I click on "Insert media" "button"
    Then "//video[descendant::source[contains(@src, 'moodle-logo.webm')]][@controls='true'][@loop='true'][@autoplay='true'][@autoplay='true']" "xpath_element" should exist

  @javascript @atto_media_video
  Scenario: Insert some media as a video with tracks
    Given I click on "Video" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.webm" "link"
    And I click on "Select this file" "button"
    And I click on "Subtitles and captions" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_subtitles .atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-sv.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And the field "Label" matches value "Swedish"
    And the field "Language" matches value "sv"
    And I click on "Add subtitle track" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_subtitles .atto_media_track~.atto_media_track .atto_media_source.atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-en.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[2]" matches value "English"
    And I set the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_default ')])[1]" to "1"
    And I click on "Captions" "link" in the ".nav-item[data-track-kind='captions']" "css_element"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_captions .atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-sv.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[3]" matches value "Swedish"
    And I click on "Add caption track" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_captions .atto_media_track~.atto_media_track .atto_media_source.atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-en.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[4]" matches value "English"
    And I set the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_default ')])[4]" to "1"
    And I click on "Descriptions" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_descriptions .atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And  I click on "pretty-good-sv.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[5]" matches value "Swedish"
    And I click on "Add description track" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_descriptions .atto_media_track~.atto_media_track .atto_media_source.atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-en.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[6]" matches value "English"
    And I set the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_default ')])[5]" to "1"
    And I click on "Chapters" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_chapters .atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And  I click on "pretty-good-sv.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[7]" matches value "Swedish"
    And I click on "Add chapter track" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_chapters .atto_media_track~.atto_media_track .atto_media_source.atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-en.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[8]" matches value "English"
    And I set the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_default ')])[8]" to "1"
    And I click on "Metadata" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_metadata .atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And  I click on "pretty-good-sv.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[9]" matches value "Swedish"
    And I click on "Add metadata track" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_video_metadata .atto_media_track~.atto_media_track .atto_media_source.atto_media_track_source" "css_element"
    And I click on "Private files" "link" in the ".moodle-dialogue-base[aria-hidden='false'] .fp-repo-area" "css_element"
    And I click on "pretty-good-en.vtt" "link"
    And I click on "Select this file" "button" in the ".moodle-dialogue-base[aria-hidden='false']" "css_element"
    And I click on "Overwrite" "button"
    And the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_label_entry ')])[10]" matches value "English"
    And I set the field with xpath "(//*[contains(concat(' ', normalize-space(@class), ' '), ' atto_media_track_default ')])[9]" to "1"
    When I click on "Insert media" "button"
    Then "//video[descendant::source[contains(@src, 'moodle-logo.webm')]][descendant::track[contains(@src, 'pretty-good-sv.vtt')][@kind='subtitles'][@label='Swedish'][@srclang='sv'][@default='true']][descendant::track[contains(@src, 'pretty-good-en.vtt')][@kind='subtitles'][@label='English'][@srclang='en'][not(@default)]][descendant::track[contains(@src, 'pretty-good-sv.vtt')][@kind='captions'][@label='Swedish'][@srclang='sv'][not(@default)]][descendant::track[contains(@src, 'pretty-good-en.vtt')][@kind='captions'][@label='English'][@srclang='en'][@default='true']][descendant::track[contains(@src, 'pretty-good-sv.vtt')][@kind='descriptions'][@label='Swedish'][@srclang='sv'][@default='true']][descendant::track[contains(@src, 'pretty-good-en.vtt')][@kind='descriptions'][@label='English'][@srclang='en'][not(@default)]][descendant::track[contains(@src, 'pretty-good-sv.vtt')][@kind='chapters'][@label='Swedish'][@srclang='sv'][not(@default)]][descendant::track[contains(@src, 'pretty-good-en.vtt')][@kind='chapters'][@label='English'][@srclang='en'][@default='true']][descendant::track[contains(@src, 'pretty-good-sv.vtt')][@kind='metadata'][@label='Swedish'][@srclang='sv'][@default='true']][descendant::track[contains(@src, 'pretty-good-en.vtt')][@kind='metadata'][@label='English'][@srclang='en'][not(@default)]]" "xpath_element" should exist

  @javascript @atto_media_audio
  Scenario: Insert some media as a plain audio
    Given I click on "Audio" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_audio .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.mp4" "link"
    And I click on "Select this file" "button"
    When I click on "Insert media" "button"
    Then "//audio[descendant::source[contains(@src, 'moodle-logo.mp4')]]" "xpath_element" should exist

  @javascript @atto_media_audio
  Scenario: Insert some media as an audio with display settings
    Given I click on "Audio" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_audio .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.mp4" "link"
    And I click on "Select this file" "button"
    And I click on "Display options" "link" in the "#id_summary_editor_audio" "css_element"
    And I set the field "audio_media-title-entry" to "AudioTitle"
    When I click on "Insert media" "button"
    Then "//audio[descendant::source[contains(@src, 'moodle-logo.mp4')]][@title='AudioTitle']" "xpath_element" should exist

  @javascript @atto_media_audio
  Scenario: Insert some media as an audio with advanced settings
    Given I click on "Audio" "link"
    And I click on "Browse repositories..." "button" in the "#id_summary_editor_audio .atto_media_source.atto_media_media_source" "css_element"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.mp4" "link"
    And I click on "Select this file" "button"
    And I click on "Advanced settings" "link" in the "#id_summary_editor_audio" "css_element"
    And the field "audio_media-controls-toggle" matches value "1"
    And I set the field "audio_media-autoplay-toggle" to "1"
    And I set the field "audio_media-mute-toggle" to "1"
    And I set the field "audio_media-loop-toggle" to "1"
    When I click on "Insert media" "button"
    Then "//audio[descendant::source[contains(@src, 'moodle-logo.mp4')]][@controls='true'][@loop='true'][@autoplay='true'][@autoplay='true']" "xpath_element" should exist
