@core @core_grades
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
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Setup > Gradebook setup" in the course gradebook
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
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "8.00" to the user "Student 2" for the grade item "Manual item 1"
    And I press "Save changes"
    When I navigate to "Setup > Gradebook setup" in the course gradebook
    And I click on "Edit" "link" in the "Manual item 1" "table_row"
    And I click on "Edit settings" "link" in the "Manual item 1" "table_row"
    And I set the following fields to these values:
      | Maximum grade | 10 |
      | Rescale existing grades | No |
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 10.00  | 100.00 %                     |
    And I select "Student 2" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 8.00   | 80.00 %                      |
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I click on "Edit" "link" in the "Manual item 1" "table_row"
    And I click on "Edit settings" "link" in the "Manual item 1" "table_row"
    And I set the following fields to these values:
      | Maximum grade | 20 |
      | Rescale existing grades | Yes |
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 20.00  | 100.00 %                     |
    And I select "Student 2" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 100.00 %          | 16.00   | 80.00 %                     |
