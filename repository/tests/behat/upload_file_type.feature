@core @core_filepicker @_file_upload
Feature: File type can be validated on upload
  While uploading files
  As a user
  I want the file type to be validated to save me from errors

  @javascript
  Scenario: File-picker does not break if you upload the wrong file type
    Given I am on the "filemanager_hideif_disabledif_form" "core_form > Fixture" page logged in as "admin"
    When I click on "Add..." "link"
    And I select "Upload a file" repository in file picker
    And I set the field "Attachment" to "#dirroot#/lib/form/tests/fixtures/filemanager_hideif_disabledif_form.php"
    And I click on "Upload this file" "button" in the "File picker" "dialogue"
    Then I should see "Text file filetype cannot be accepted." in the "Error" "dialogue"
    And I click on "OK" "button" in the "Error" "dialogue"
    And I should see "Attachment" in the "File picker" "dialogue"
    And "Upload this file" "button" in the "File picker" "dialogue" should be visible
