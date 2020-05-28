@core @core_files
Feature: View license links
  In order to get select the applicable license when uploading a file
  As a user
  I need to be able to navigate to a page containing license terms from the file manager

  @javascript
  Scenario: Uploading a file displays license list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I wait until the page is ready
    And I follow "Add..."
    And I follow "Upload a file"
    And I click on "Help with Choose license" "icon" in the "File picker" "dialogue"
    Then I should see "Follow these links for further information on the available license options:"

  @javascript @_file_upload
  Scenario: Altering a file should display license list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I click on "empty.txt" "link"
    And I click on "Help with Choose license" "icon"
    Then I should see "Follow these links for further information on the available license options:"

  @javascript @_file_upload
  Scenario: Recent files should display license list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I follow "Add..."
    And I click on "Recent files" "link" in the "File picker" "dialogue"
    And I click on "empty.txt" "link" in the "File picker" "dialogue"
    And I click on "Help with Choose license" "icon" in the ".fp-setlicense" "css_element"
    Then I should see "Follow these links for further information on the available license options:"

  @javascript @_file_upload
  Scenario: Private files should display license list modal
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I follow "Manage private files..."
    And I follow "Add..."
    And I click on "Private files" "link" in the "File picker" "dialogue"
    And I click on "empty.txt" "link" in the "File picker" "dialogue"
    And I click on "Help with Choose license" "icon" in the ".fp-setlicense" "css_element"
    Then I should see "Follow these links for further information on the available license options:"
