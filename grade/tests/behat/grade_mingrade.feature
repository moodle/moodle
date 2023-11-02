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
      | fullname       | course | aggregateonlygraded |
      | Sub category 1 | C1     | 0                   |
      | Sub category 2 | C1     | 0                   |
    And the following "grade items" exist:
      | itemname      | grademin | course |
      | Manual item 1 | -100     | C1     |
      | Manual item 2 | 50       | C1     |
    And the following "grade items" exist:
      | itemname      | grademin | grademax | course | gradecategory  |
      | Manual item 3 | -100     | 50       | C1     | Sub category 1 |
    And the following "grade items" exist:
      | itemname      | grademin | course | gradecategory  |
      | Manual item 4 | -100     | C1     | Sub category 1 |
      | Manual item 5 | 50       | C1     | Sub category 2 |
      | Manual item 6 | 50       | C1     | Sub category 2 |
    And I log in as "admin"
    And I am on the "Course 1" "grades > gradebook setup" page
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"

  @javascript
  Scenario: Natural aggregation with negative and positive grade
    Given I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "Course 1" of type "course" on "setup" page:
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    When I give the grade "-25.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "50.00" to the user "Student 1" for the grade item "Manual item 2"
    And I give the grade "-80.00" to the user "Student 1" for the grade item "Manual item 3"
    And I give the grade "-10.00" to the user "Student 1" for the grade item "Manual item 4"
    And I give the grade "50.00" to the user "Student 1" for the grade item "Manual item 5"
    And I give the grade "75.00" to the user "Student 1" for the grade item "Manual item 6"
    And I give the grade "0.00" to the user "Student 2" for the grade item "Manual item 1"
    And I give the grade "50.00" to the user "Student 2" for the grade item "Manual item 2"
    And I give the grade "-10.00" to the user "Student 2" for the grade item "Manual item 3"
    And I give the grade "50.00" to the user "Student 2" for the grade item "Manual item 4"
    And I give the grade "50.00" to the user "Student 2" for the grade item "Manual item 5"
    And I give the grade "50.00" to the user "Student 2" for the grade item "Manual item 6"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    Then the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 18.18 %           | -25.00 | -4.55 %                      |
      | Manual item 2 | 18.18 %           | 50.00  | 9.09 %                       |
      | Manual item 3 | 33.33 %           | -80.00 | -14.55 %                     |
      | Manual item 4 | 66.67 %           | -10.00 | -1.82 %                      |
      | Manual item 5 | 50.00 %           | 50.00  | 9.09 %                       |
      | Manual item 6 | 50.00 %           | 75.00  | 13.64 %                      |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | 18.18 %           | 0.00   | 0.00 %                       |
      | Manual item 2 | 18.18 %           | 50.00  | 9.09 %                       |
      | Manual item 3 | 33.33 %           | -10.00 | -1.82 %                      |
      | Manual item 4 | 66.67 %           | 50.00  | 9.09 %                       |
      | Manual item 5 | 50.00 %           | 50.00  | 9.09 %                       |
      | Manual item 6 | 50.00 %           | 50.00  | 9.09 %                       |
