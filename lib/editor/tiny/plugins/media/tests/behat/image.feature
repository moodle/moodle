@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload an image
  In order to work with images
  As a user
  I need to be able to upload and manipulate images

  Scenario: Clicking on the Image button in the TinyMCE editor opens the image dialog
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    Then "Insert image" "dialogue" should exist

  Scenario: Browsing repositories in the TinyMCE editor opens the image dialog and shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    Then "File picker" "dialogue" should exist

  @_file_upload @test_tiny
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker and upload url image
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/tinyscreenshot.png" to the file picker for TinyMCE
    # Note: This needs to be replaced with a label.
    Then ".tiny_image_preview" "css_element" should be visible

  @_file_upload
  Scenario: Insert image to the TinyMCE editor
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I set the field "How would you describe this image to someone who can't see it?" to "It's the Moodle"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "How would you describe this image to someone who can't see it?" matches value "It's the Moodle"
    # Note: This needs to be replaced with a label.
    And ".tiny_image_preview" "css_element" should be visible

  @_file_upload
  Scenario: Resizing the image uses the original and custom sizes and the keep proportion checkbox
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Insert image" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I click on "This image is decorative only" "checkbox"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "Original size" matches value "1"
    And I click on "Custom size" "radio"
    Then the field "Keep proportion" matches value "1"
    And I click on "Keep proportion" "checkbox"
    And I set the field "Width" to "102"
    And I click on "Save" "button" in the "Image details" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "Custom size" matches value "1"
    And the field "Width" matches value "102"
    And the field "Keep proportion" matches value "0"
