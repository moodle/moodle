@core @core_grades
Feature: Regrading grades does not unnecessarily mark some as overriden
  In order to regrade a grade item
  As an admin
  I need to keep the overridden status of all grades

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | s1 |
      | student2 | Student | 2 | student2@example.com | s2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | assign1 | Assignment 1 | Assignment 1 intro |
    And I log in as "admin"
    And I navigate to "Grades > Grade category settings" in site administration
    And I set the field "Available aggregation types" to "Weighted mean of grades"
    And I press "Save changes"
    And I am on the "Assignment 1" "assign activity" page
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "80"
    And I press "Save and show next"
    And I set the field "Grade out of 100" to "60"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And the following should exist in the "gradereport-grader-table" table:
      |                      |              |              |
      | First name / Surname | Assignment 1 | Course total |
      | Student 1            | 80.00        | 80.00        |
      | Student 2            | 60.00        | 60.00        |
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 2" for the grade item "Course total"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I click on "Edit" "link" in the ".coursecategory" "css_element"
    And I click on "Edit settings" "link" in the ".coursecategory" "css_element"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I set the field "Rescale overridden grades" to "Yes"
    And I set the field "Maximum grade" to "200"
    And I press "Save changes"

  @javascript
  Scenario: Confirm regrading did not overwrite overridden status
    Given I navigate to "View > Grader report" in the course gradebook
    Then "td.overridden" "css_element" should not exist in the "Student 1" "table_row"
    And "td.overridden" "css_element" should exist in the "Student 2" "table_row"

  @javascript
  Scenario: Confirm overridden course total does not get regraded when activity grade is changed
    Given I am on the "Assignment 1" "assign activity" page
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "90"
    And I press "Save and show next"
    And I set the field "Grade out of 100" to "70"
    And I press "Save changes"
    When I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    Then the following should exist in the "gradereport-grader-table" table:
      |                      |              |              |
      | First name / Surname | Assignment 1 | Course total |
      | Student 1            | 90.00        | 180.00       |
      | Student 2            | 70.00        | 160.00       |
