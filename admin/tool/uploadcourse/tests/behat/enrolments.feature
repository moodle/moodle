@tool @tool_uploadcourse @_file_upload
Feature: An admin can update courses enrolments using a CSV file
  In order to update courses enrolments using a CSV file
  As an admin
  I need to be able to upload a CSV file with enrolment methods for the courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I log in as "admin"

  @javascript
  Scenario: Creating enrolment method by enable it
    Given I am on the "Course 1" "enrolment methods" page
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I click on "Continue" "button"
    And I should not see "Guest access" in the "generaltable" "table"
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_enable.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And "Disable" "icon" should exist in the "Guest access" "table_row"

  @javascript
  Scenario: Creating enrolment method by disabling it
    Given I am on the "Course 1" "enrolment methods" page
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I click on "Continue" "button"
    And I should not see "Guest access" in the "generaltable" "table"
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_disable.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And "Enable" "icon" should exist in the "Guest access" "table_row"

  @javascript
  Scenario: Enabling enrolment method
    Given I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_enable.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And "Disable" "icon" should exist in the "Guest access" "table_row"

  @javascript
  Scenario: Disable an enrolment method
    Given I am on the "Course 1" "enrolment methods" page
    And I click on "Enable" "link" in the "Guest access" "table_row"
    And "Disable" "icon" should exist in the "Guest access" "table_row"
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_disable.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And "Enable" "icon" should exist in the "Guest access" "table_row"

  @javascript
  Scenario: Delete an enrolment method
    Given I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_delete.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And I should not see "Guest access" in the "generaltable" "table"

  @javascript
  Scenario: Delete an unexistent enrolment method (nothing should change)
    Given I am on the "Course 1" "enrolment methods" page
    And I click on "Delete" "link" in the "Guest access" "table_row"
    And I click on "Continue" "button"
    And I should not see "Guest access" in the "generaltable" "table"
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_delete.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    When I click on "Upload courses" "button"
    Then I should see "Course updated"
    And I am on the "Course 1" "enrolment methods" page
    And I should not see "Guest access" in the "generaltable" "table"

  @javascript
  Scenario: Re-upload a file using CSV data only after deleting the enrolments method
    Given I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And the following "cohort" exists:
      | name     | Cohort1  |
      | idnumber | Cohort1  |
    And the following "category" exists:
      | name     | Cat 1 |
      | category | 0     |
      | idnumber | CAT1  |
    And I navigate to "Courses > Upload courses" in site administration
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_multiple.csv" file to "File" filemanager
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I am on the "Course 2" "enrolment methods" page
    And I should see "Self enrolment (Student)" in the "generaltable" "table"
    And I should see "Cohort sync (Cohort1 - Student)" in the "generaltable" "table"
    And I should see "Guest access" in the "generaltable" "table"
    And I should see "Manual enrolments" in the "generaltable" "table"
    And I should see "Course meta link (Course 1)" in the "generaltable" "table"
    And I navigate to "Courses > Upload courses" in site administration
    # Delete all enrolment methods.
    And I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_multiple_delete.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I should see "Course updated"
    And I am on the "Course 2" "enrolment methods" page
    And I should not see "Self enrolment (Student)" in the "generaltable" "table"
    And I should not see "Cohort sync (Cohort1 - Student)" in the "generaltable" "table"
    And I should not see "Guest access" in the "generaltable" "table"
    And I should not see "Manual enrolments" in the "generaltable" "table"
    And I should not see "Course meta link (Course 1)" in the "generaltable" "table"
    # Re-upload again the CSV file, to add again the enrolment methods.
    And I navigate to "Courses > Upload courses" in site administration
    When I upload "admin/tool/uploadcourse/tests/fixtures/enrolment_multiple.csv" file to "File" filemanager
    And I set the field "Upload mode" to "Only update existing courses"
    And I set the field "Update mode" to "Update with CSV data only"
    And I set the field "Allow deletes" to "Yes"
    And I click on "Preview" "button"
    And I click on "Upload courses" "button"
    And I am on the "Course 2" "enrolment methods" page
    Then I should see "Self enrolment (Student)" in the "generaltable" "table"
    And I should see "Cohort sync (Cohort1 - Student)" in the "generaltable" "table"
    And I should see "Guest access" in the "generaltable" "table"
    And I should see "Manual enrolments" in the "generaltable" "table"
    And I should see "Course meta link (Course 1)" in the "generaltable" "table"
    And I navigate to "Courses > Upload courses" in site administration
