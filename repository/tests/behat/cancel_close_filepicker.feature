@core @core_filepicker
Feature: Cancel file and folder upload
  In order to cancel file and folder upload
  As a user
  I need to be able to close the filepicker

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "admin"
    And I follow "Dashboard"
    And I follow "Manage private files"

  @javascript @_file_upload
  Scenario: Cancel a file upload by pressing close button
    Given I follow "Add..."
    And I follow "Upload a file"
    When I set the field "Attachment" to "#dirroot#/lib/tests/fixtures/empty.txt"
    And I set the field "Save as" to "empty_upload.txt"
    And I click on "Close" "button" in the "File picker" "dialogue"
    Then I should not see "empty_upload.txt"
    And I should not see "empty.txt"

  @javascript
  Scenario: Cancel folder creation by pressing close button
    # Add the folder into the private files repository
    Given I follow "Create folder"
    When I set the field "New folder name" to "Test folder 1"
    And I click on "button.fp-dlg-butcreate" "css_element" in the "div.fp-mkdir-dlg" "css_element"
    # Ensure that folder is created
    Then I should see "Test folder 1"
    # Close the dialogue box using X button instead of saving
    And I click on "Close" "button" in the "Manage private files" "dialogue"
    # Confirm folder was not added
    And I should not see "Test folder 1"
