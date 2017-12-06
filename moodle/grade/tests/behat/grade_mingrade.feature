@core @core_grades
Feature: We can use a minimum grade different than zero
  In order to use a minimum grade different than zero
  As an teacher
  I need to set up a minimum grade different than zero

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
      | student2 | Student | 2 | student2@example.com | s2 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1 |
      | Sub category 2 | C1 |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
    And I am on "Course 1" course homepage
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 1 |
      | Minimum grade | -100 |
      | Grade category | Course 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 2 |
      | Minimum grade | 50 |
      | Grade category | Course 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 3 |
      | Maximum grade | 50 |
      | Minimum grade | -100 |
      | Grade category | Sub category 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 4 |
      | Minimum grade | -100 |
      | Grade category | Sub category 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 5 |
      | Minimum grade | 50 |
      | Grade category | Sub category 2 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 6 |
      | Minimum grade | 50 |
      | Grade category | Sub category 2 |
    And I press "Save changes"
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"

  @javascript
  Scenario: Natural aggregation with negative and positive grade
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    When I give the grade "-25.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "50.00" to the user "Student 1" for the grade item "Manual item 2"
    And I give the grade "-80.00" to the user "Student 1" for the grade item "Manual item 3"
    And I give the grade "-10.00" to the user "Student 1" for the grade item "Manual item 4"
    And I give the grade "50.00" to the user "Student 1" for the grade item "Manual item 5"
    And I give the grade "75.00" to the user "Student 1" for the grade item "Manual item 6"
    And I give the grade "0.00" to the user "Student 2" for the grade item "Manual item 1"
    And I give the grade "0.00" to the user "Student 2" for the grade item "Manual item 2"
    And I give the grade "-10.00" to the user "Student 2" for the grade item "Manual item 3"
    And I give the grade "50.00" to the user "Student 2" for the grade item "Manual item 4"
    And I give the grade "0.00" to the user "Student 2" for the grade item "Manual item 5"
    And I give the grade "0.00" to the user "Student 2" for the grade item "Manual item 6"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 18.18 %           | -25.00 | -4.55 %                      |
      | Manual item 2 | 18.18 %           | 50.00  | 9.09 %                       |
      | Manual item 3 | 33.33 %           | -80.00 | -14.55 %                     |
      | Manual item 4 | 66.67 %           | -10.00 | -1.82 %                      |
      | Manual item 5 | 50.00 %           | 50.00  | 9.09 %                       |
      | Manual item 6 | 50.00 %           | 75.00  | 13.64 %                      |
    And I set the field "Select all or one user" to "Student 2"
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 18.18 %           | 0.00   | 0.00 %                       |
      | Manual item 2 | 18.18 %           | 50.00  | 9.09 %                       |
      | Manual item 3 | 33.33 %           | -10.00 | -1.82 %                      |
      | Manual item 4 | 66.67 %           | 50.00  | 9.09 %                       |
      | Manual item 5 | 50.00 %           | 50.00  | 9.09 %                       |
      | Manual item 6 | 50.00 %           | 50.00  | 9.09 %                       |
