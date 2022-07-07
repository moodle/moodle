@core @core_filepicker @_file_upload
Feature: Select file feature
  In order to add a file to a filearea
  As a user
  I need to be able to select the file using the file picker

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |

  @javascript
  Scenario: Select a file from the "Recent files" repository using "icons" view
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name        | Test folder             |
      | Description | Test folder description |
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I click on "Save and display" "button"
    And I follow "Dashboard" in the user menu
    And I follow "Manage private files"
    And I click on "Add..." "button" in the "Files" "form_row"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "Display folder with file icons" "link" in the ".file-picker" "css_element"
    And I click on "//a[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')][normalize-space(.)='empty.txt']" "xpath_element"
    And I should see "Select empty.txt"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"

  @javascript
  Scenario: Select a file from the "Recent files" repository using "list" view
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name        | Test folder             |
      | Description | Test folder description |
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I click on "Save and display" "button"
    And I follow "Dashboard" in the user menu
    And I follow "Manage private files"
    And I click on "Add..." "button" in the "Files" "form_row"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "Display folder with file details" "link" in the ".file-picker" "css_element"
    And I click on "//div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]/descendant::span[normalize-space(.)='empty.txt']/ancestor::a" "xpath_element"
    And I should see "Select empty.txt"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"

  @javascript
  Scenario: Select a file from the "Recent files" repository using "tree" view
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Folder" to section "1"
    And I set the following fields to these values:
      | Name        | Test folder             |
      | Description | Test folder description |
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I click on "Save and display" "button"
    And I follow "Dashboard" in the user menu
    And I follow "Manage private files"
    And I click on "Add..." "button" in the "Files" "form_row"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "Display folder as file tree" "link" in the ".file-picker" "css_element"
    And I click on "//div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]/descendant::span[normalize-space(.)='empty.txt']/ancestor::a" "xpath_element"
    And I should see "Select empty.txt"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"
