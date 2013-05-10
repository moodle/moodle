@core @core_filepicker
Feature: Zip folders and unzip compressed files
  In order to download or add contents to file areas easily
  As a user
  I need to zip and unzip folders and files

  @javascript
  Scenario: Zip and unzip folders and files
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
    And I open "Files" folder from "Files" filepicker
    And I zip "Folder 1" folder from "Files" filepicker
    And I delete "Folder 1" from "Files" filepicker
    When I unzip "Folder 1.zip" file from "Files" filepicker
    And I delete "Folder 1.zip" from "Files" filepicker
    Then I should see "Folder 1"
    And I open "Folder 1" folder from "Files" filepicker
    And I should see "SubFolder 1"
    And I press "Save and return to course"
