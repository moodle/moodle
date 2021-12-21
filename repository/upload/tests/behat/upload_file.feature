@repository @repository_upload @_file_upload
Feature: Upload files
  In order to add contents
  As a user
  I need to upload files

  @javascript
  Scenario: Upload a file in a multiple file filemanager
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I turn editing mode on
    And I add the "Private files" block if not present
    When I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the "div.fp-content" "css_element"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager as:
      | Save as | empty_copy.txt |
    Then I should see "2" elements in "Files" filemanager
    And I should see "empty.txt"
    And I should see "empty_copy.txt"
