@editor @editor_atto @atto @atto_image @_file_upload
Feature: Add images to Atto
  To write rich text - I need to add images.

  @javascript
  Scenario: Insert an image
    Given I log in as "admin"
    And I follow "Manage my private files..."
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    When I set the field "Description" to "<p>Image test</p>"
    And I select the text in the "Description" Atto editor
    And I click on "Image" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image" to "It's the Moodle"
    # Wait for the page to "settle".
    And I wait until the page is ready
    And the field "Width" matches value "204"
    And the field "Height" matches value "61"
    And I set the field "Auto size" to "1"
    And I set the field "Width" to "2040"
    # Trigger blur on the width field.
    And I take focus off "Width" "field"
    And the field "Height" matches value "610"
    And I set the field "Height" to "61"
    # Trigger blur on the height field.
    And I take focus off "Height" "field"
    And the field "Width" matches value "204"
    And I set the field "Auto size" to "0"
    And I set the field "Width" to "123"
    And I set the field "Height" to "456"
    # Trigger blur on the height field.
    And I take focus off "Height" "field"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"
    And I click on "Save image" "button"
    And I click on "Update profile" "button"
    And I follow "Edit profile"
    And I select the text in the "Description" Atto editor
    And I click on "Image" "button"
    Then the field "Describe this image" matches value "It's the Moodle"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"

  @javascript
  Scenario: Manually inserting an image
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<p>Image: <img src='/nothing/here'>.</p>"
    And I select the text in the "Description" Atto editor
    When I click on "Image" "button"
    Then the field "Enter URL" matches value "/nothing/here"
    And I set the field "Describe this image" to "Something"
    And I set the field "Enter URL" to ""
    And I press "Save image"
    And I set the field "Description" to "<p>Image: <img src='/nothing/again' width='123' height='456' alt='Awesome!'>.</p>"
    And I press "Update profile"
    And I follow "Edit profile"
    And I select the text in the "Description" Atto editor
    And I click on "Image" "button"
    And the field "Enter URL" matches value "/nothing/again"
    And the field "Width" matches value "123"
    And the field "Height" matches value "456"
    And the field "Describe this image" matches value "Awesome!"
