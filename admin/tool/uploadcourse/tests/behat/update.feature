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

  @javascript
  Scenario: Unsupported enrol methods are not updated
    Given the following config values are set as admin:
      | enrol_plugins_enabled | manual,lti |
    And the following "courses" exist:
      | fullname         | shortname | category |
      | Course 2         | C2        | 0        |
    And I am on the "C2" "enrolment methods" page
    When I select "Publish as LTI tool" from the "Add method" singleselect
    And the following fields match these values:
      | LTI version | LTI Advantage |
    And I set the following fields to these values:
      | Custom instance name | Published course     |
      | Tool to be published | Course               |
    And I press "Add method"
    And I should see "Published course"
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/unsupported_enrol_method.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    Then I should see "Enrolment method 'enrol_lti_plugin' is not supported in csv upload"
    And I should see "Courses errors: 1"
    And I am on the "C2" "enrolment methods" page
    And I should see "manualtest"
    And I should not see "Manual enrolments"
    And I should see "Published course"
    And I should not see "ltitest"

  @javascript
  Scenario: Manager can use upload course tool to update courses in course category
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
      | Cat 3 | CAT1     | CAT3     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C01       | CAT1     |
      | Course 2 | C02       | CAT2     |
      | Course 3 | C03       | CAT3     |
      | Course 4 | C04       | CAT3     |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | Category     | CAT1      |
    When I log in as "user1"
    And I am on course index
    And I follow "Cat 1"
    And I navigate to "Upload courses" in current page administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/courses_manager2.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I click on "Preview" "button"
    # Course C01 is in "our" category but can not be moved to Cat 2 because current user can not manage it.
    Then I should see "No permission to upload courses in category: Cat 2" in the "C01" "table_row"
    # Course C02 can not be updated (no capability in "Cat 2" context).
    And I should see "Course with this shortname exists and you don't have permission to use upload course tool to update it" in the "C02" "table_row"
    # Course with short name "C05" does not exist.
    And I should see "The course does not exist and creating course is not allowed" in the "C05" "table_row"
    And I click on "Upload courses" "button"
    And I should see "Course updated"
    And I should see "Courses total: 5"
    And I should see "Courses updated: 2"
    And I should see "Courses errors: 3"
    And I am on course index
    And I follow "Cat 1"
    # Course C04 was moved from Cat 3 to Cat 1.
    And I should see "Course 4"
    And I should see "Course 1"
    And I follow "Cat 3"
    And I should see "Course 3"
    And I log out
