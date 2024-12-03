@tool @tool_uploadcourse @_file_upload
Feature: An admin can update courses using a CSV file
  In order to update courses using a CSV file
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Some random name | C1  | 0 |
      | Another course   | CF1 | 0 |
    And I log in as "admin"
    And I navigate to "Courses > Upload courses" in site administration

  @javascript
  Scenario: Updating a course fullname
    Given I upload "admin/tool/uploadcourse/tests/fixtures/courses.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I should see "The course does not exist and creating course is not allowed"
    And I should see "Courses total: 3"
    And I should see "Courses updated: 1"
    And I should see "Courses created: 0"
    And I should see "Courses errors: 2"
    And I am on site homepage
    And I should see "Course 1"
    And I should not see "Course 2"
    And I should not see "Course 3"

  @javascript
  Scenario: Updating a course with custom fields
    Given the following "custom field categories" exist:
      | name  | component   | area   | itemid |
      | Other | core_course | course | 0      |
    And the following "custom fields" exist:
      | name    | category | type     | shortname | configdata            |
      | Field 1 | Other    | checkbox | checkbox  |                       |
      | Field 2 | Other    | date     | date      |                       |
      | Field 3 | Other    | select   | select    | {"options":"a\nb\nc"} |
      | Field 4 | Other    | text     | text      |                       |
      | Field 5 | Other    | textarea | textarea  |                       |
    When I upload "admin/tool/uploadcourse/tests/fixtures/courses_custom_fields.csv" file to "File" filemanager
    And I set the following fields to these values:
      | Upload mode | Only update existing courses |
      | Update mode | Update with CSV data only    |
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I should see "Courses updated: 1"
    And I am on site homepage
    And I should see "Course fields 1"
    And I should see "Field 1: Yes"
    And I should see "Field 2: Tuesday, 1 October 2019, 2:00"
    And I should see "Field 3: b"
    And I should see "Field 4: Hello"
    And I should see "Field 5: Goodbye"
