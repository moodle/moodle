@tool @tool_uploadcourse @_file_upload
Feature: An admin can create courses with meta enrolments using a CSV file
  In order to create courses using a CSV file with meta enrolment
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 0 | 0        | CAT0     |
      | Cat 1 | CAT0     | CAT1     |
      | Cat 1 | CAT0     | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
      | Course 2 | C2        | CAT1     |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"

  @javascript
  Scenario: Validation of meta link course shortname for uploaded courses
    Given I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_meta.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Unknown meta course shortname"
    And I should see "You can't add a meta link to the same course."
    When I click on "Upload courses" "button"
    And I should see "Unknown meta course shortname"
    And I should see "You can't add a meta link to the same course."
    And I should see "Courses created: 1"
    And I should see "Courses updated: 0"
    And I should see "Courses errors: 2"
    And I am on the "Course 4" "enrolment methods" page
    Then I should see "Course meta link (Course 1)"
    And I click on "Edit" "link" in the "Course meta link" "table_row"
    And the field "Add to group" matches value "None"
    And I am on the "Course 1" "enrolment methods" page
    And I should not see "Course meta link (Course 1)"

  @javascript
  Scenario: Validation of groups for uploaded courses with meta enrolments
    Given the following "groups" exist:
      | name    | course | idnumber |
      | group1  | C2     | G1       |
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_meta_addtogroup_groupname.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "You cannot specify groupname when addtogroup is set."

    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_meta_addtogroup.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I should see "Courses created: 2"
    And I should see "Courses errors: 0"
    And I am on the "Course 3" "enrolment methods" page
    And I should see "Course meta link (Course 1)"
    And I click on "Edit" "link" in the "Course meta link" "table_row"
    And the field "Add to group" matches value "Course 1 course"
    And I am on the "Course 3" "groups" page
    And I should see "Course 1 course"
    And I am on the "Course 4" "enrolment methods" page
    And I should see "Course meta link (Course 1)"
    And I click on "Edit" "link" in the "Course meta link" "table_row"
    And the field "Add to group" matches value "None"
    And I am on the "Course 4" "groups" page
    And I should not see "Course 1 course"

    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_meta_groups.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Error, invalid group notexist"
    When I click on "Upload courses" "button"
    And I should see "Error, invalid group notexist"
    And I should see "Courses updated: 1"
    And I should see "Courses errors: 1"
    And I am on the "Course 2" "enrolment methods" page
    Then I should see "Course meta link (Course 1)"
    And I click on "Edit" "link" in the "Course meta link" "table_row"
    And the field "Add to group" matches value "group1"
    And I am on the "Course 2" "groups" page
    And I should see "group1"
