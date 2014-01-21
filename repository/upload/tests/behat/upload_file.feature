@repository @repository_upload @_only_local @_file_upload
Feature: Upload files
  In order to add contents
  As a user
  I need to upload files

  @javascript
  Scenario: Upload a file in a multiple file filemanager
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Admin User"
    And I follow "My private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the "div.fp-content" "css_element"
    When I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager as:
      | Save as | empty_copy.txt |
    Then I should see "2" elements in "Files" filemanager
    And I should see "empty.txt"
    And I should see "empty_copy.txt"
    And I press "Cancel"
