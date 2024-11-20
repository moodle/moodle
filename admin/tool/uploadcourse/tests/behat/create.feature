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

  @javascript
  Scenario: Validation of role for uploaded courses
    Given I navigate to "Users > Permissions > Define roles" in site administration
    And I click on "Add a new role" "button"
    And I click on "Continue" "button"
    And I set the following fields to these values:
      | Short name | notallowed |
      | Custom full name | notallowed |
      | contextlevel80 | 1 |
    And I click on "Create this role" "button"
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_role.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Invalid role names: notexist"
    And I should see "Role notallowed not allowed in this context."
    When I click on "Upload courses" "button"
    And I should see "Course created"
    And I should see "Courses total: 3"
    And I should see "Courses created: 1"
    And I should see "Courses errors: 2"
    And I should see "Invalid role names: notexist"
    And I should see "Role notallowed not allowed in this context."
    And I am on site homepage
    And I should see "coursez"

  @javascript
  Scenario: Unsupported enrol methods are not created
    Given the following config values are set as admin:
      | enrol_plugins_enabled | manual,guest,lti |
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/unsupported_enrol_method.csv" file to "File" filemanager
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course created"
    And I should see "Enrolment method 'enrol_lti_plugin' is not supported in csv upload"
    And I am on the "C2" "enrolment methods" page
    And I should see "manualtest"
    And I should not see "ltitest"

  @javascript
  Scenario: Manager can use upload course tool in course category
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
      | Cat 3 | CAT1     | CAT3     |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | Category     | CAT1      |
    When I log in as "user1"
    And I am on course index
    And I follow "Cat 1"
    And I navigate to "Upload courses" in current page administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/courses_manager1.csv" file to "File" filemanager
    And I click on "Preview" "button"
    Then I should see "The course exists and update is not allowed" in the "C1" "table_row"
    And I should see "No permission to upload courses in category: Cat 2" in the "C2" "table_row"
    And I set the field "Course category" to "Cat 1 / Cat 3"
    And I click on "Upload courses" "button"
    And I should see "Course created"
    And I should see "Courses total: 5"
    And I should see "Courses created: 3"
    And I should see "Courses errors: 2"
    And I am on course index
    And I follow "Cat 1"
    And I should see "Course 4"
    And I follow "Cat 3"
    And I should see "Course 5"
    # Course 3 did not have category specified in CSV file and it was uploaded to the current category.
    And I should see "Course 3"
    And I log out
