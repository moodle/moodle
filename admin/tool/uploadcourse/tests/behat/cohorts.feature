@tool @tool_uploadcourse @_file_upload
Feature: An admin can create courses with cohort enrolments using a CSV file
  In order to create courses using a CSV file with cohort enrolment
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 0 | 0        | CAT0     |
      | Cat 1 | CAT0     | CAT1     |
      | Cat 1 | CAT0     | CAT2     |
    And the following "cohorts" exist:
      | name            | idnumber | contextlevel | reference | visible |
      | Cohort 1        | CV1      | Category     | CAT1      | 1       |
      | Cohort 2        | CV2      | Category     | CAT2      | 1       |
      | Cohort 3        | CV3      | Category     | CAT2      | 1       |
      | Cohort 4        | CV4      | Category     | CAT1      | 1       |
      | Cohort 5        | CV5      | Category     | CAT1      | 1       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And I log in as "admin"
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"

  @javascript
  Scenario: Upload cohort enrolment if plugin is disabled
    Given the following config values are set as admin:
      | enrol_plugins_enabled | manual,guest,self |
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort.csv" file to "File" filemanager
    When I click on "Preview" "button"
    Then I should see "Cohort sync plugin is disabled"

  @javascript
  Scenario: Validation of cohorts for uploaded courses
    Given I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Unknown cohort (Not exist)!"
    And I should see "Cohort CV3 not allowed in this context."
    When I click on "Upload courses" "button"
    And I should see "Unknown cohort (Not exist)!"
    And I should see "Cohort CV3 not allowed in this context."
    And I should see "Cohort CV4 not allowed in this context."
    And I should see "Invalid role names: student1"
    And I should see "Courses created: 2"
    And I should see "Courses updated: 0"
    And I should see "Courses errors: 4"
    And I am on the "Course 1" "enrolment methods" page
    Then I should not see "Cohort sync (Cohort 3 - Student)"
    And I am on the "Course 2" "enrolment methods" page
    And I should not see "Cohort sync (Cohort 4 - Student)"
    And I am on the "Course 3" "enrolment methods" page
    And I should see "Cohort sync (Cohort 5 - Student)"
    And I click on "Edit" "link" in the "Cohort 5" "table_row"
    And the field "Add to group" matches value "None"
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort_missing_fields.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Missing value for mandatory fields: cohortidnumber, role"
    And "Upload courses" "button" should exist

  @javascript
  Scenario: Validation of groups for uploaded courses with cohort enrolments
    Given the following "groups" exist:
      | name    | course | idnumber |
      | group1  | C1     | G1       |
    # Test that groupname can't be set when addtogroup is used.
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort_addtogroup_groupname.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "You cannot specify groupname when addtogroup is set."
    # Test creating a new group when uploading a course.
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort_addtogroup.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I should see "Courses created: 2"
    And I should see "Courses errors: 0"
    And I am on the "Course 2" "enrolment methods" page
    And I should see "Cohort sync (Cohort 2 - Student)"
    And I click on "Edit" "link" in the "Cohort 2" "table_row"
    And the field "Add to group" matches value "Cohort 2 cohort"
    And I am on the "Course 2" "groups" page
    And I should see "Cohort 2 cohort"
    And I am on the "Course 3" "enrolment methods" page
    And I should see "Cohort sync (Cohort 1 - Student)"
    And I click on "Edit" "link" in the "Cohort 1" "table_row"
    And the field "Add to group" matches value "None"
    And I am on the "Course 3" "groups" page
    And I should not see "Cohort 1 cohort"

    # Test assigning to an existing group when uploading a course.
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort_groups.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Error, invalid group notexist"
    When I click on "Upload courses" "button"
    And I should see "Error, invalid group notexist"
    And I should see "Courses updated: 1"
    And I should see "Courses errors: 1"
    And I am on the "Course 1" "enrolment methods" page
    Then I should see "Cohort sync (Cohort 1 - Student)"
    And I click on "Edit" "link" in the "Cohort 1" "table_row"
    And the field "Add to group" matches value "group1"
    And I am on the "Course 1" "groups" page
    And I should see "group1"

  @javascript
  Scenario: Upload multiple enrolment methods of same type to the same course
    Given I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_cohort_multiple.csv" file to "File" filemanager
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    And I should see "Courses updated: 2"
    And I am on the "Course 1" "enrolment methods" page
    Then I should see "Cohort sync (Cohort 1 - Student)"
    And I should see "Cohort sync (Cohort 4 - Non-editing teacher)"
    And I click on "Edit" "link" in the "Cohort 1" "table_row"
    And the field "Assign role" matches value "Student"
    And I press "Cancel"
    And I click on "Edit" "link" in the "Cohort 4" "table_row"
    And the field "Assign role" matches value "Non-editing teacher"
