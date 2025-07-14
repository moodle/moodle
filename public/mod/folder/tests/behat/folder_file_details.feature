@mod @mod_resource @_file_upload
Feature: Details of uploaded file in the resource can be changed
  In order to change details of an uploaded file in a folder resource
  As a teacher
  I should be able to upload a file

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name    |
      | folder   | C1     | Folder1 |

  @javascript
  Scenario: Uploaded file details can be changed
    Given I am on the "Folder1" "folder activity" page logged in as teacher1
    And I click on "Edit" "button"
    # Upload a file in folder resource
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save changes"
    And I click on "Edit" "button"
    And I click on "empty.txt" "link"
    # Initially, file details are set to default values
    And the following fields match these values:
      | Name    | empty.txt             |
      | Author  | Teacher 1             |
      | licence | Licence not specified |
    # Update the file details for testing
    When I set the following fields to these values:
      | Name    | empty_file.txt      |
      | Author  | Teacher 1           |
      | licence | All rights reserved |
    And I press "Update"
    And I press "Save changes"
    # Confirm that file details have been updated correctly
    And I click on "Edit" "button"
    And I click on "empty_file.txt" "link"
    Then the following fields match these values:
      | Name    | empty_file.txt      |
      | Author  | Teacher 1           |
      | licence | All rights reserved |
