@core @core_filepicker @_file_upload
Feature: Delete files and folders from the file manager
  In order to clean the file manager contents
  As a user
  I need to delete files from file areas

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript @_bug_phantomjs
  Scenario: Delete a file and a folder
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    When I delete "empty.txt" from "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    Then I should not see "empty.txt" in the "Manage private files" "dialogue"
    And I delete "Delete me" from "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I should not see "Delete me" in the "Manage private files" "dialogue"

  @javascript
  Scenario: Delete a file and a folder using bulk functionality (individually)
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me later" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I click on "Display folder with file details" "link"
    And I set the field "Select file 'empty.txt'" to "1"
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 1 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "empty.txt" in the "Manage private files" "dialogue"
    But I should see "Delete me later" in the "Manage private files" "dialogue"
    When I press "Save changes"
    And I follow "Manage private files..."
    Then I should not see "empty.txt" in the "Manage private files" "dialogue"
    But I should see "Delete me later" in the "Manage private files" "dialogue"
    And I set the field "Select file 'Delete me later'" to "1"
    And I click on "Delete" "link"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me later" in the "Manage private files" "dialogue"
    When I press "Save changes"
    And I follow "Manage private files..."
    Then I should not see "Delete me later" in the "Manage private files" "dialogue"

  @javascript
  Scenario: Delete a file and a folder using bulk functionality (multiple)
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I create "Do not delete me" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I click on "Display folder with file details" "link"
    And I set the field "Select file 'empty.txt'" to "1"
    And I set the field "Select file 'Delete me'" to "1"
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 2 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me" in the "Manage private files" "dialogue"
    And I should not see "empty.txt" in the "Manage private files" "dialogue"
    But I should see "Do not delete me" in the "Manage private files" "dialogue"
    When I press "Save changes"
    And I follow "Manage private files..."
    Then I should not see "Delete me" in the "Manage private files" "dialogue"
    And I should not see "empty.txt" in the "Manage private files" "dialogue"
    And I am on homepage
    Then I should not see "Delete me" in the "Private files" "block"
    And I should not see "empty.txt" in the "Private files" "block"
    But I should see "Do not delete me" in the "Private files" "block"

  @javascript
  Scenario: Delete files using the select all checkbox
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I create "Delete me" folder in "Files" filemanager
    And I create "Delete me too" folder in "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I click on "Display folder with file details" "link"
    When I click on "Select all/none" "checkbox"
    Then the following fields match these values:
      | Select file 'empty.txt' | 1 |
      | Select file 'Delete me' | 1 |
      | Select file 'Delete me too' | 1 |
    When I click on "Delete" "link"
    Then I should see "Are you sure you want to delete the selected 3 file(s)?"
    When I click on "OK" "button" in the "Confirm" "dialogue"
    Then I should not see "Delete me" in the "Manage private files" "dialogue"
    And I should not see "empty.txt" in the "Manage private files" "dialogue"
    And I should not see "Delete me too" in the "Manage private files" "dialogue"
    When I press "Save changes"
    And I follow "Manage private files..."
    Then I should not see "Delete me" in the "Manage private files" "dialogue"
    And I should not see "empty.txt" in the "Manage private files" "dialogue"
    And I am on homepage
    Then I should not see "Delete me" in the "Private files" "block"
    And I should not see "empty.txt" in the "Private files" "block"
    And I should not see "Delete me too" in the "Private files" "block"
