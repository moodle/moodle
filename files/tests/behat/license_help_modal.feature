@core @core_files
Feature: View licence links
  In order to get select the applicable licence when uploading a file
  As a user
  I need to be able to navigate to a page containing licence terms from the file manager

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript
  Scenario: Uploading a file displays licence list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I follow "Add..."
    And I follow "Upload a file"
    And I click on "Help with Choose licence" "icon" in the "File picker" "dialogue"
    Then I should see "Follow these links for further information on the available licence options:"

  @javascript @_file_upload
  Scenario: Altering a file should display licence list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I click on "empty.txt" "link" in the "Manage private files" "dialogue"
    And I click on "Help with Choose licence" "icon"
    Then I should see "Follow these links for further information on the available licence options:"

  @javascript @_file_upload
  Scenario: Recent files should display licence list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I follow "Add..."
    And I click on "Recent files" "link" in the "File picker" "dialogue"
    And I click on "empty.txt" "link" in the "File picker" "dialogue"
    And I click on "Help with Choose licence" "icon" in the ".fp-setlicense" "css_element"
    Then I should see "Follow these links for further information on the available licence options:"

  @javascript @_file_upload
  Scenario: Private files should display licence list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I follow "Add..."
    And I click on "Private files" "link" in the "File picker" "dialogue"
    And I click on "empty.txt" "link" in the "File picker" "dialogue"
    And I click on "Help with Choose licence" "icon" in the ".fp-setlicense" "css_element"
    Then I should see "Follow these links for further information on the available licence options:"
