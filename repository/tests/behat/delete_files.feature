@core @core_filepicker @_file_upload
Feature: Delete files and folders from the file manager
  In order to clean the file manager contents
  As a user
  I need to delete files from file areas

  @javascript @_bug_phantomjs
  Scenario: Delete a file and a folder
    Given I log in as "admin"
    And I follow "Manage private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files"
    When I delete "empty.txt" from "Files" filemanager
    And I press "Save changes"
    Then I should not see "empty.txt"
    And I follow "Manage private files"
    And I delete "Delete me" from "Files" filemanager
    And I press "Save changes"
    And I should not see "Delete me"

  @javascript
  Scenario: Delete a file and a folder using bulk functionality (individually)
    Given I log in as "admin"
    And I follow "Manage private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me later" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files"
    And I click on "Display folder with file details" "link"
    And I set the field "Select file 'empty.txt'" to "1"
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 1 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "empty.txt"
    But I should see "Delete me later"
    When I press "Save changes"
    And I follow "Manage private files"
    Then I should not see "empty.txt"
    But I should see "Delete me later"
    And I set the field "Select file 'Delete me later'" to "1"
    And I click on "Delete" "link"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me later"
    When I press "Save changes"
    Then I should not see "Delete me later"

  @javascript
  Scenario: Delete a file and a folder using bulk functionality (multiple)
    Given I log in as "admin"
    And I follow "Manage private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I create "Do not delete me" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files"
    And I click on "Display folder with file details" "link"
    And I set the field "Select file 'empty.txt'" to "1"
    And I set the field "Select file 'Delete me'" to "1"
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 2 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me"
    And I should not see "empty.txt"
    But I should see "Do not delete me"
    When I press "Save changes"
    Then I should not see "Delete me" in the "Private files" "block"
    And I should not see "empty.txt" in the "Private files" "block"
    But I should see "Do not delete me" in the "Private files" "block"

  @javascript
  Scenario: Delete files using the select all checkbox
    Given I log in as "admin"
    And I follow "Manage private files"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I create "Delete me too" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files"
    And I click on "Display folder with file details" "link"
    When I click on "Select all/none" "checkbox"
    Then the following fields match these values:
      | Select file 'empty.txt' | 1 |
      | Select file 'Delete me' | 1 |
      | Select file 'Delete me too' | 1 |
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 3 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me"
    And I should not see "empty.txt"
    And I should not see "Delete me too"
    When I press "Save changes"
    Then I should not see "Delete me" in the "Private files" "block"
    And I should not see "empty.txt" in the "Private files" "block"
    And I should not see "Delete me too" in the "Private files" "block"
