@editor @editor_tiny @tiny_media @javascript
Feature: Use the TinyMCE editor to upload an image
  In order to work with images
  As a user
  I need to be able to upload and manipulate images

  Scenario: Clicking on the Image button in the TinyMCE editor opens the image dialog
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    Then "Image properties" "dialogue" should exist

  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Image properties" "dialogue"
    Then "File picker" "dialogue" should exist

  @_file_upload @test_tiny
  Scenario: Browsing repositories in the TinyMCE editor shows the FilePicker
    Given I log in as "admin"
    And I open my profile in edit mode
    When I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories" "button" in the "Image properties" "dialogue"
    And I upload "/lib/editor/tiny/tests/behat/fixtures/tinyscreenshot.png" to the file picker for TinyMCE
    # Note: This needs to be replaced with a label.
    Then ".tiny_image_preview" "css_element" should be visible

  @_file_upload
  Scenario: Insert image to the TinyMCE editor
    Given I log in as "admin"
    And I open my profile in edit mode
    And I click on the "Image" button for the "Description" TinyMCE editor
    And I click on "Browse repositories..." "button" in the "Image properties" "dialogue"
    And I upload "lib/editor/tiny/tests/behat/fixtures/moodle-logo.png" to the file picker for TinyMCE
    And I set the field "Describe this image for someone who cannot see it" to "It's the Moodle"
    And I click on "Save image" "button" in the "Image properties" "dialogue"
    When I select the "img" element in position "0" of the "Description" TinyMCE editor
    And I click on the "Image" button for the "Description" TinyMCE editor
    Then the field "Describe this image for someone who cannot see it" matches value "It's the Moodle"
    # Note: This needs to be replaced with a label.
    And ".tiny_image_preview" "css_element" should be visible
