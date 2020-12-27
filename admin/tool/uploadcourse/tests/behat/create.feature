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
    And I navigate to "Courses > Upload courses" in site administration

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
    And I am on site homepage
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
    And I am on site homepage
    And I should see "Course 1"
    And I should see "Course 2"
    And I should see "Course 3"

  @javascript
  Scenario: Creation of new courses with custom fields
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
    And I set the field "Upload mode" to "Create new courses only, skip existing ones"
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    Then I should see "Course created"
    And I should see "Courses created: 1"
    And I am on site homepage
    And I should see "Course fields 1"
    And I should see "Field 1: Yes"
    And I should see "Field 2: Tuesday, 1 October 2019, 2:00"
    And I should see "Field 3: b"
    And I should see "Field 4: Hello"
    And I should see "Field 5: Goodbye"

  @javascript
  Scenario: Creation of new courses with custom fields using defaults
    Given the following "custom field categories" exist:
      | name  | component   | area   | itemid |
      | Other | core_course | course | 0      |
    And the following "custom fields" exist:
      | name    | category | type     | shortname | configdata                                          |
      | Field 1 | Other    | checkbox | checkbox  | {"checkbydefault":1}                                |
      | Field 2 | Other    | date     | date      | {"includetime":0}                                   |
      | Field 3 | Other    | select   | select    | {"options":"a\nb\nc","defaultvalue":"b"}            |
      | Field 4 | Other    | text     | text      | {"defaultvalue":"Hello"}                            |
      | Field 5 | Other    | textarea | textarea  | {"defaultvalue":"Some text","defaultvalueformat":1} |
    When I upload "admin/tool/uploadcourse/tests/fixtures/courses.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Create all, increment shortname if needed"
    And I click on "Preview" "button"
    And I expand all fieldsets
    And the field "Field 1" matches value "1"
    And the field "Field 3" matches value "b"
    And the field "Field 4" matches value "Hello"
    And the field "Field 5" matches value "Some text"
    # We have to enable the date field manually.
    And I set the following fields to these values:
      | customfield_date[enabled] | 1    |
      | customfield_date[day]     | 1    |
      | customfield_date[month]   | June |
      | customfield_date[year]    | 2020 |
    And I click on "Upload courses" "button"
    Then I should see "Course created"
    And I should see "Courses created: 3"
    And I am on site homepage
    And I should see "Course 1"
    And I should see "Field 1: Yes"
    And I should see "Field 2: 1 June 2020"
    And I should see "Field 3: b"
    And I should see "Field 4: Hello"
    And I should see "Field 5: Some text"
