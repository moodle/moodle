@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload a media link
  In order to work with media links
  As a user
  I need to be able to upload and manipulate media links

  Scenario: Clicking on the media button in the TinyMCE editor opens the dialog
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist

  # The following scenario covers:
  # 1. Add YouTube link.
  # 3. Preview embedded YouTube link from tiny.
  # 4. Embed YouTube link as a hyperlink.
  # 5. Preview embedded YouTube link from tiny.
  Scenario: Embed YouTube link as a hyperlink
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    Then "Insert media" "dialogue" should exist
    # Add audio link using "Or add via URL" input.
    And I set the field "Or add via URL" to "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And I click on "Add" "button" in the "Insert media" "dialogue"
    # Preview inserted link.
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And "Add custom thumbnail" "button" should not exist in the "Media details" "dialogue"
    And "Show controls" "checkbox" should not exist in the "Media details" "dialogue"
    And "Autoplay" "checkbox" should not exist in the "Media details" "dialogue"
    And "Muted" "checkbox" should not exist in the "Media details" "dialogue"
    And "Loop" "checkbox" should not exist in the "Media details" "dialogue"
    # Embed link as video into tiny and preview it by selecting it from tiny.
    And I click on "Save" "button"
    And I select the "a" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And the field "Media title" in the "Media details" "dialogue" matches value "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And "Show controls" "checkbox" should not exist in the "Media details" "dialogue"
    And "Autoplay" "checkbox" should not exist in the "Media details" "dialogue"
    And "Muted" "checkbox" should not exist in the "Media details" "dialogue"
    And "Loop" "checkbox" should not exist in the "Media details" "dialogue"
    And I set the field "Media title" in the "Media details" "dialogue" to "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And I click on "Save" "button" in the "Media details" "dialogue"
    And I select the "a" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Multimedia" button for the "Description" TinyMCE editor
    And "Media details" "dialogue" should exist
    And the field "Media title" in the "Media details" "dialogue" matches value "https://www.youtube.com/watch?v=-fPTdIruJUU"
    # Delete previewed link.
    And I click on "Delete media" "button" in the "Media details" "dialogue"
    And "Delete media" "dialogue" should exist
    And "Delete" "button" should exist in the "Delete media" "dialogue"
    And I click on "Delete" "button" in the "Delete media" "dialogue"
    And "Insert media" "dialogue" should exist
    And "tiny-insert-media" "region" should exist in the "Insert media" "dialogue"
    # Let's add the same YouTube link.
    And I set the field "Or add via URL" to "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And I click on "Add" "button" in the "Insert media" "dialogue"
    # Preview inserted media link.
    And "Media details" "dialogue" should exist
    And "tiny-insert-media" "region" should not exist in the "Media details" "dialogue"
    And "tiny-media-details-body" "region" should exist in the "Media details" "dialogue"
    And the field "Media title" in the "Media details" "dialogue" matches value "https://www.youtube.com/watch?v=-fPTdIruJUU"
    And "Add custom thumbnail" "button" should not exist in the "Media details" "dialogue"
    And "Show controls" "checkbox" should not exist in the "Media details" "dialogue"
    And "Autoplay" "checkbox" should not exist in the "Media details" "dialogue"
    And "Muted" "checkbox" should not exist in the "Media details" "dialogue"
    And "Loop" "checkbox" should not exist in the "Media details" "dialogue"
    And "Original size" "radio" should not exist in the "Media details" "dialogue"
    And "Custom size" "radio" should not exist in the "Media details" "dialogue"
    And I click on "Save" "button" in the "Media details" "dialogue"
