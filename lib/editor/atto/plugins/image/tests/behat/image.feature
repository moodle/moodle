@editor @editor_atto @atto @atto_image @_file_upload
Feature: Add images to Atto
  To write rich text - I need to add images.

  @javascript
  Scenario: Insert an image
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I open my profile in edit mode
    When I set the field "Description" to "<p>Image test</p>"
    And I select the text in the "Description" Atto editor
    And I click on "Insert or edit image" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the Moodle"
    # Wait for the page to "settle".
    And I wait until the page is ready
    And the field "Width" matches value "204"
    And the field "Height" matches value "61"
    And I set the field "Auto size" to "1"
    And I wait until the page is ready
    And I set the field "Width" to "2040"
    # Trigger blur on the width field.
    And I take focus off "Width" "field"
    And the field "Height" matches value "610"
    And I set the field "Height" to "61"
    # Trigger blur on the height field.
    And I take focus off "Height" "field"
    And the field "Width" matches value "204"
    And I set the field "Auto size" to "0"
    And I wait until the page is ready
    And I set the field "Width" to "123"
    And I set the field "Height" to "456"
    # Trigger blur on the height field.
    And I take focus off "Height" "field"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"
    And I change window size to "large"
    And I press "Save image"
    And I press "Update profile"
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I select the text in the "Description" Atto editor
    And I click on "Insert or edit image" "button"
    Then the field "Describe this image for someone who cannot see it" matches value "It's the Moodle"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"

  @javascript
  Scenario: Manually inserting an image
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Image: <img src='/nothing/here'>.</p>"
    And I select the text in the "Description" Atto editor
    When I click on "Insert or edit image" "button"
    Then the field "Enter URL" matches value "/nothing/here"
    And I set the field "Describe this image for someone who cannot see it" to "Something"
    And I set the field "Width" to "1"
    And I set the field "Height" to "1"
    And I press "Save image"
    And I set the field "Description" to "<p>Image: <img src='/nothing/again' width='123' height='456' alt='Awesome!'>.</p>"
    And I press "Update profile"
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I select the text in the "Description" Atto editor
    And I click on "Insert or edit image" "button"
    And the field "Enter URL" matches value "/nothing/again"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"
    And the field "Describe this image" matches value "Awesome!"

  @javascript
  Scenario: Error handling when inserting an image manually
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Image: <img src='/nothing/here'>.</p>"
    And I select the text in the "Description" Atto editor
    When I click on "Insert or edit image" "button"
    Then the field "Enter URL" matches value "/nothing/here"
    And I set the field "Describe this image for someone who cannot see it" to ""
    And I take focus off "Describe this image for someone who cannot see it" "field"
    And I should see "An image must have a description, unless it is marked as decorative only."
    And I set the field "Describe this image for someone who cannot see it" to "Something"
    And I set the field "Enter URL" to ""
    And I press "Save image"
    And I should see "An image must have a URL."
    And I set the field "Enter URL" to "/nothing/here"
    And I set the field "Width" to "1"
    And I set the field "Height" to "1"
    And I press "Save image"
    And I press "Update profile"
