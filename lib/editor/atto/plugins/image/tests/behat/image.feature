@atto @atto_image @_file_upload
Feature: Add images to Atto
  To write rich text - I need to add images.

  @javascript
  Scenario: Insert an image
    Given I log in as "admin"
    And I navigate to "My private files" node in "My profile"
    And I upload "lib/editor/atto/tests/fixtures/moodle-logo.png" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I follow "Admin User"
    And I follow "Edit profile"
    And I click on "Image" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image" to "It's the Moodle"
    # Wait for the page to "settle".
    And I wait "2" seconds
    And I click on "Save image" "button"
    And I click on "Update profile" "button"
    And I follow "Edit profile"
    And I select the text in the "Description" field
    And I click on "Image" "button"
    Then the field "Describe this image" matches value "It's the Moodle"

