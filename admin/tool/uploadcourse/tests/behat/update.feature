@tool @tool_uploadcourse @_only_local @_file_upload
Feature: An admin can update courses using a CSV file
  In order to update courses using a CSV file
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Some random name | C1 | 0 |
    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Courses" node
    And I follow "Upload courses"

  @javascript
  Scenario: Updating a course fullname
    Given I upload "admin/tool/uploadcourse/tests/fixtures/courses.csv" file to "File" filemanager
    And I select "Only update existing courses" from "Upload mode"
    And I select "Update with CSV data only" from "Update mode"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I should see "The course does not exist and creating course is not allowed"
    And I should see "Courses total: 3"
    And I should see "Courses updated: 1"
    And I should see "Courses created: 0"
    And I should see "Courses errors: 2"
    And I follow "Home"
    And I should see "Course 1"
    And I should not see "Course 2"
    And I should not see "Course 3"
