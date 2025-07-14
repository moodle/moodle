@core @core_grades @javascript @_file_upload
Feature: An admin can import grades into gradebook using a CSV file
  In order to import grades using a CSV file
  As a teacher
  I need to be able to upload a CSV file and see uploaded grades in gradebook

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "grade item" exists:
      | course   | C1            |
      | itemname | Manual item 1 |
      | grademin | 10            |
      | grademax | 500           |
    And the following "grade grades" exist:
      | gradeitem     | user     | grade |
      | Manual item 1 | student1 | 50.00 |
      | Manual item 1 | student2 | 50.00 |
      | Manual item 1 | student3 | 50.00 |

  Scenario: Max grade of grade item is respected when importing grades
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "CSV file" import page in the course gradebook
    And I upload "grade/tests/fixtures/grade_import_grademax.csv" file to "File" filemanager
    And I click on "Upload grades" "button"
    And I set the field "Map from" to "Email address"
    And I set the field "Map to" to "Email address"
    And I set the field "Manual item 1" to "Manual item 1"
    And I click on "Upload grades" "button"
    And I should see "One of the grade values is larger than the allowed grade maximum of 500.0"
    And I should see "Import failed. No data was imported."
    And I click on "Continue" "button"
    And I upload "grade/tests/fixtures/grade_import_grademin.csv" file to "File" filemanager
    And I click on "Upload grades" "button"
    And I set the field "Map from" to "Email address"
    And I set the field "Map to" to "Email address"
    And I set the field "Manual item 1" to "Manual item 1"
    And I click on "Upload grades" "button"
    And I should see "One of the grade values is smaller than the allowed grade minimum of 10.0"
    And I should see "Import failed. No data was imported."
    And I click on "Continue" "button"
    When I upload "grade/tests/fixtures/grade_import.csv" file to "File" filemanager
    And I click on "Upload grades" "button"
    And I set the field "Map from" to "Email address"
    And I set the field "Map to" to "Email address"
    And I set the field "Manual item 1" to "Manual item 1"
    And I click on "Upload grades" "button"
    And I should see "Grade import success"
    And I click on "Continue" "button"
    Then the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-    | -4-    |
      | Student 1          | student1@example.com | 400.00 | 400.00 |
      | Student 2          | student2@example.com | 50.00  | 50.00  |
      | Student 3          | student3@example.com | 50.00  | 50.00  |

  Scenario: Importing grades with multiple new mappings
    Given I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "CSV file" import page in the course gradebook
    And I upload "grade/tests/fixtures/grade_import_multiple_mappings.csv" file to "File" filemanager
    And I click on "Upload grades" "button"
    And I set the following fields to these values:
      | Map from | Email address  |
      | Map to   | Email address  |
      | Grade A  | New grade item |
      | Grade B  | New grade item |
      | Grade C  | New grade item |
      | Grade D  | New grade item |
    And I click on "Upload grades" "button"
    And I should see "Grade import success"
    And I click on "Continue" "button"
    Then the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3-   | -4-   | -5-   | -6-   | -7-   | -8-    |
      | Student 1 | student1@example.com | 50.00 | 11.00 | 12.00 | 13.00 | 14.00 | 100.00 |
      | Student 2 | student2@example.com | 50.00 | 21.00 | 22.00 | 23.00 | 24.00 | 140.00 |
      | Student 3 | student3@example.com | 50.00 | 31.00 | 32.00 | 33.00 | 34.00 | 180.00 |
