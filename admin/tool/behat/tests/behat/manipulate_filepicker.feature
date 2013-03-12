@tool_behat @core_form @filepicker
Feature: Manipulate filepicker
  In order to provide external resources
  As a moodle user
  I need to upload files to moodle

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Folder" to section "0"
    And I fill the moodle form with:
      | Name | Folder resource |
      | Description | The description |
    And I create "Folder 1" folder in "Files" filepicker
    And I open "Folder 1" folder from "Files" filepicker
    And I create "SubFolder 1" folder in "Files" filepicker

  @javascript
  Scenario: Create folders and subfolders
    When I open "Files" folder from "Files" filepicker
    Then I should see "Folder 1"
    And I open "Folder 1" folder from "Files" filepicker
    And I should see "SubFolder 1"
    And I press "Save and return to course"

  @javascript
  Scenario: Zip and unzip folders and files
    Given I open "Files" folder from "Files" filepicker
    And I zip "Folder 1" folder from "Files" filepicker
    And I delete "Folder 1" from "Files" filepicker
    When I unzip "Folder 1.zip" file from "Files" filepicker
    And I delete "Folder 1.zip" from "Files" filepicker
    Then I should see "Folder 1"
    And I open "Folder 1" folder from "Files" filepicker
    And I should see "SubFolder 1"
    And I press "Save and return to course"
