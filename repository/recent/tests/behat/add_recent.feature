@repository @repository_recent @_file_upload
Feature: Recent files repository lists the recently used files
  In order to save time when selecting files
  As a user
  I need to use again the files I've just used

  @javascript
  Scenario: Add files recently uploaded
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Manage my private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I upload "lib/tests/fixtures/upload_users.csv" file to "Files" filemanager
    And I press "Save changes"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name | Folder name |
      | Description | Folder description |
    And I add "empty.txt" file from "Recent files" to "Files" filemanager
    And I add "empty.txt" file from "Recent files" to "Files" filemanager as:
      | Save as | empty_copy.txt |
    And I press "Save and display"
    Then I should see "empty.txt"
    And I should see "empty_copy.txt"
    And I should see "Folder description"
