@core @core_grades @javascript
Feature: We can change the maximum and minimum number of points for manual items with existing grades
  In order to verify existing grades are modified as expected
  As an teacher
  I need to modify a grade item with exiting grades
  I need to ensure existing grades are modified in an expected manner

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
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 1 |
      | Minimum grade | 0 |
      | Maximum grade | 100 |
    And I press "Save changes"
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"

  Scenario: Change maximum number of points on a graded item.
    Given I am on the "Course 1" "grades > Grader report > View" page
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "8.00" to the user "Student 2" for the grade item "Manual item 1"
    And I press "Save changes"
    When I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "Manual item 1" of type "gradeitem" on "setup" page:
      | Rescale existing grades | No |
      | Maximum grade | 10 |
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    Then the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 10.00  | 100.00 %                     |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 8.00   | 80.00 %                      |
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I click on grade item menu "Manual item 1" of type "gradeitem" on "setup" page
    And I choose "Edit grade item" in the open action menu
    And I set the following fields to these values:
      | Rescale existing grades | Yes |
      | Maximum grade | 20 |
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 20.00  | 100.00 %                     |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 16.00   | 80.00 %                     |
