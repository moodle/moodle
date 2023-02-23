@core @core_filepicker @_file_upload
Feature: Select file feature
  In order to add a file to a filearea
  As a user
  I need to be able to select the file using the file picker

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "activities" exist:
      | activity | course | name        |
      | folder   | C1     | Test folder |
    And I am on the "Test folder" "folder activity" page logged in as admin
    And I press "Edit"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"

  @javascript
  Scenario: Select a file from the "Recent files" repository using "icons" view
    Given I follow "Dashboard"
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
    Given I follow "Dashboard"
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
    Given I follow "Dashboard"
    And I follow "Manage private files"
    And I click on "Add..." "button" in the "Files" "form_row"
    And I click on "Recent files" "link" in the ".fp-repo-area" "css_element"
    And I click on "Display folder as file tree" "link" in the ".file-picker" "css_element"
    And I click on "//div[contains(concat(' ', normalize-space(@class), ' '), ' file-picker ')]/descendant::span[normalize-space(.)='empty.txt']/ancestor::a" "xpath_element"
    And I should see "Select empty.txt"
    When I click on "Select this file" "button"
    Then I should see "1" elements in "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"
