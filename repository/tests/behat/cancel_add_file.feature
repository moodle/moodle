@core @core_filepicker @_file_upload
Feature: A selected file can be cancelled
  In order to refine the file manager contents
  As a user
  I need to cancel a selected file

  @javascript @_bug_phantomjs
  Scenario: Cancel a selected recent file from being added to a folder
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Manage my private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name | Folder name |
      | Description | Folder description |
    And I upload "lib/tests/fixtures/upload_users.csv" file to "Files" filemanager
    And I click on "#fitem_id_files .fp-btn-add a" "css_element"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "//a[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')][normalize-space(.)='empty.txt']" "xpath_element"
    And I click on ".moodle-dialogue-focused .fp-select .fp-select-cancel" "css_element"
    And I click on ".moodle-dialogue-focused.filepicker .yui3-button.closebutton" "css_element"
    And I press "Save and display"
    Then I should see "upload_users.csv"
    And I should not see "empty.txt"
    And I should see "Folder description"
