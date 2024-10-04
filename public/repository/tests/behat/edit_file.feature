@core @core_filepicker @_file_upload
Feature: Edit file feature
  In order to edit a file
  As a user
  I need to be able to select the file in filemanager and modify the file information

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "admin"

  @javascript
  Scenario: Select file from "Files" filemanager using "icons" view and edit the name
    Given I follow "Manage private files"
    And I click on "Display folder with file icons" "link" in the ".filemanager" "css_element"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-file" "css_element"
    And I click on "//div[contains(concat(' ', normalize-space(@class), ' '), ' fp-file ')]/descendant::a[normalize-space(.)='empty.txt']" "xpath_element"
    And I should see "Edit empty.txt"
    And I set the following fields to these values:
      | Name  | empty_edited.txt |
    When I click on "Update" "button"
    Then I should see "empty_edited.txt" in the ".fp-content .fp-file" "css_element"
    And I should not see "empty.txt" in the ".fp-content .fp-file" "css_element"

  @javascript
  Scenario: Select file from "Files" filemanager using "list" view and edit the name
    Given I follow "Manage private files"
    And I click on "Display folder with file details" "link" in the ".filemanager" "css_element"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-filename" "css_element"
    And I click on "//span[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename-icon ')]/descendant::a[normalize-space(.)='empty.txt']" "xpath_element"
    And I should see "Edit empty.txt"
    And I set the following fields to these values:
      | Name  | empty_edited.txt |
    When I click on "Update" "button"
    Then I should see "empty_edited.txt" in the ".fp-content .fp-filename" "css_element"
    And I should not see "empty.txt" in the ".fp-content .fp-filename" "css_element"

  @javascript
  Scenario: Select file from "Files" filemanager using "tree" view and edit the name
    Given I follow "Manage private files"
    And I click on "Display folder as file tree" "link" in the ".filemanager" "css_element"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I should see "empty.txt" in the ".fp-content .fp-hascontextmenu .fp-filename" "css_element"
    And I click on "//span[contains(concat(' ', normalize-space(@class), ' '), ' fp-filename-icon ')]/descendant::a[normalize-space(.)='empty.txt']" "xpath_element"
    And I should see "Edit empty.txt"
    And I set the following fields to these values:
      | Name  | empty_edited.txt |
    When I click on "Update" "button"
    Then I should see "empty_edited.txt" in the ".fp-content .fp-hascontextmenu .fp-filename" "css_element"
    And I should not see "empty.txt" in the ".fp-content .fp-hascontextmenu .fp-filename" "css_element"
