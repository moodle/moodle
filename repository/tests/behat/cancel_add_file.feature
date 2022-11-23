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
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "activities" exist:
      | activity | course | name        | intro              |
      | folder   | C1     | Folder name | Folder description |
    And I log in as "admin"
    And I follow "Manage private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on the "Folder name" "folder activity" page
    And I press "Edit"
    And I upload "lib/tests/fixtures/upload_users.csv" file to "Files" filemanager
    And I click on "Add..." "button" in the "Files" "form_row"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "//a[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')][normalize-space(.)='empty.txt']" "xpath_element"
    And I click on ".moodle-dialogue-focused .fp-select .fp-select-cancel" "css_element"
    And I click on "Close" "button" in the "File picker" "dialogue"
    And I press "Save changes"
    Then I should see "upload_users.csv"
    And I should not see "empty.txt"
    And I should see "Folder description"
