@repository @repository_upload @_only_local
Feature: Upload files
  In order to add contents
  As a user
  I need to upload files

  @javascript
  Scenario: Upload a file in a multiple file filepicker
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Admin User"
    And I follow "My private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filepicker
    Then I should see "empty.txt" in the "div.fp-content" "css_element"
    And I press "Cancel"
