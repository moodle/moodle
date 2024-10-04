@tool @tool_uploadcourse @_file_upload
Feature: An admin can create courses with guest enrolments using a CSV file
  In order to create courses using a CSV file with guest enrolment
  As an admin
  I need to be able to upload a CSV file and navigate through the import process

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 0 | 0        | CAT0     |
      | Cat 1 | CAT0     | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And I log in as "admin"
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"

  @javascript
  Scenario: Validation of password for uploaded courses with guest enrolments
    # usepasswordpolicy is not set.
    Given I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_guest.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I should see "Courses created: 2"
    And I should see "Courses updated: 1"
    And I should see "Courses errors: 0"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value "test"
    And I press "Cancel"
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I press "Continue"
    And I am on the "Course 2" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value ""
    And I press "Cancel"
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I press "Continue"
    And I am on the "Course 3" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value "Test123@"
    And I press "Cancel"
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I press "Continue"

    # Policy is used, but password not required so it will not be generated if omitted.
    And the following config values are set as admin:
      | config            | value | plugin       |
      | usepasswordpolicy | 1     | enrol_guest   |
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_guest.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Passwords must be at least 8 characters long."
    And I should see "Passwords must have at least 1 digit(s)."
    And I should see "Passwords must have at least 1 upper case letter(s)."
    And I should see "The password must have at least 1 special character(s) such as *, -, or #."
    And I click on "Upload courses" "button"
    And I should see "Courses created: 0"
    And I should see "Courses updated: 2"
    And I should see "Courses errors: 1"
    And I am on the "Course 1" "enrolment methods" page
    And "Guest access" "table_row" should not exist
    And I am on the "Course 2" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value ""
    And I press "Cancel"
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I press "Continue"
    And I am on the "Course 3" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value "Test123@"
    And I press "Cancel"
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I press "Continue"

    # Policy is used and password not required so it will be generated if omitted.
    And the following config values are set as admin:
      | config          | value | plugin       |
      | requirepassword | 1     | enrol_guest   |
    And I navigate to "Courses > Upload courses" in site administration
    And I set the field "Upload mode" to "Create new courses, or update existing ones"
    And I set the field "Update mode" to "Update with CSV data only"
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_guest.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I should see "Passwords must be at least 8 characters long."
    And I should see "Passwords must have at least 1 digit(s)."
    And I should see "Passwords must have at least 1 upper case letter(s)."
    And I should see "The password must have at least 1 special character(s) such as *, -, or #."
    And I click on "Upload courses" "button"
    And I should see "Courses created: 0"
    And I should see "Courses updated: 2"
    And I should see "Courses errors: 1"
    And I am on the "Course 1" "enrolment methods" page
    And "Guest access" "table_row" should not exist
    And I am on the "Course 2" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" does not match value ""
    And I am on the "Course 3" "enrolment methods" page
    And I click on "Edit" "link" in the "Guest access" "table_row"
    And the field "Password" matches value "Test123@"
