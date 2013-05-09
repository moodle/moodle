@repository @repository_recent @_only_local
Feature: Recent files repository lists the recently used files
  In order to save time when selecting files
  As a user
  I need to use again the files I've just used

  @javascript
  Scenario: Add files recently uploaded
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I expand "My profile" node
    And I follow "My private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filepicker
    And I upload "lib/tests/fixtures/upload_users.csv" file to "Files" filepicker
    And I press "Save changes"
    And I am on homepage
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "Folder" to section "1"
    And I fill the moodle form with:
      | Name | Folder name |
      | Description | Folder description |
    And I add "empty.txt" file from recent files to "Files" filepicker
    And I press "Save and display"
    Then I should see "empty.txt"
    And I should see "Folder description"
