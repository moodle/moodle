@tool @tool_uploadcourse @_file_upload
Feature: An admin can create courses using a CSV file
  In order to create courses using a CSV file
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | First course | C1 | 0 |
    And I log in as "admin"
    And I navigate to "Upload courses" node in "Site administration > Courses"

  @javascript
  Scenario: Creation of unexisting courses
    Given I upload "admin/tool/uploadcourse/tests/fixtures/courses.csv" file to "File" filemanager
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "The course exists and update is not allowed"
    And I should see "Course created"
    And I should see "Courses total: 3"
    And I should see "Courses created: 2"
    And I should see "Courses errors: 1"
    And I follow "Home"
    And I should see "Course 2"
    And I should see "Course 3"

  @javascript
  Scenario: Creation of existing courses
    Given I upload "admin/tool/uploadcourse/tests/fixtures/courses.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Create all, increment shortname if needed"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course created"
    And I should see "Course shortname incremented C1 -> C2"
    And I should see "Course shortname incremented C2 -> C3"
    And I should see "Course shortname incremented C3 -> C4"
    And I should see "Courses total: 3"
    And I should see "Courses created: 3"
    And I should see "Courses errors: 0"
    And I follow "Home"
    And I should see "Course 1"
    And I should see "Course 2"
    And I should see "Course 3"
