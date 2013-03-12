@repository_upload @core_form @repository @_only_local
Feature: Upload files
  In order to add contents
  As a moodle user
  I need to upload files

  @javascript
  Scenario: Upload a file in a single file filepicker
    Given I log in as "admin"
    And I expand "Front page settings" node
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    And I follow "Upload users"
    When I upload "lib/tests/fixtures/upload_users.csv" file to "File" filepicker
    And I press "Upload users"
    Then I should see "Upload users preview"
    And I should see "Teacher"
    And I should see "teacher1@teacher1.com"
    And I press "Cancel"

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
