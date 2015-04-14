@core @core_filepicker @_file_upload
Feature: Overwrite file feature
  In order to update an existing file
  As a user
  I need to pick the file with the same name and select to overwrite

  @javascript @_bug_phantomjs
  Scenario: Upload a file in filemanager and overwrite it
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Terry | Teacher | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    When I log in as "teacher1"
    And I follow "Manage my private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    Then I should see "1" elements in "Files" filemanager
    And I upload and overwrite "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I should see "1" elements in "Files" filemanager
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager as:
      | Save as | empty_copy.txt |
    And I should see "2" elements in "Files" filemanager
    And I upload and overwrite "lib/tests/fixtures/empty.txt" file to "Files" filemanager as:
      | Save as | empty_copy.txt |
    And I should see "2" elements in "Files" filemanager
    And I press "Save changes"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name | Test folder |
      | Description | Test folder description |
    And I add "empty.txt" file from "Private files" to "Files" filemanager
    And I should see "1" elements in "Files" filemanager
    And I add and overwrite "empty.txt" file from "Private files" to "Files" filemanager
    And I should see "1" elements in "Files" filemanager
    And I add "empty.txt" file from "Private files" to "Files" filemanager as:
      | Save as | empty_copy.txt |
    And I should see "2" elements in "Files" filemanager
    And I add and overwrite "empty.txt" file from "Private files" to "Files" filemanager as:
      | Save as | empty_copy.txt |
    And I should see "2" elements in "Files" filemanager
    And I press "Save and display"
    And I should see "empty.txt"
    And I should see "empty_copy.txt"
