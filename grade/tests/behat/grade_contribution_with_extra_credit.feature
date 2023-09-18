@core @core_grades @javascript
Feature: Extra credit contributions are normalised when going out of bounds
  In order to use extra credit
  As a teacher
  I need to add some extra credit items.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_aggregations_visible | Simple weighted mean of grades,Mean of grades (with extra credits),Natural |
    And I am on the "Course 1" "grades > gradebook setup" page
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | Manual item 1 |
      | Maximum grade | 150 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | Manual item 2 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | Manual item 3 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | Manual item 4 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the field "Show weighting" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Manual item 2"
    And I give the grade "70.00" to the user "Student 1" for the grade item "Manual item 3"
    And I give the grade "90.00" to the user "Student 1" for the grade item "Manual item 4"
    And I press "Save changes"

  Scenario Outline: The contribution of extra credit items is normalised
    Given I navigate to "Setup > Gradebook setup" in the course gradebook
    When I set the following settings for grade item "Course 1" of type "course" on "setup" page:
      | Aggregation | <aggregation> |
    And I set the following settings for grade item "Manual item 2" of type "gradeitem" on "setup" page:
      | aggregationcoef | 1 |
    And I set the following settings for grade item "Manual item 3" of type "gradeitem" on "setup" page:
      | aggregationcoef | 1 |
    And I set the following settings for grade item "Manual item 4" of type "gradeitem" on "setup" page:
      | aggregationcoef | 1 |
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    Then the following should exist in the "user-grade" table:
      | Grade item    | Calculated weight | Grade  | Contribution to course total |
      | Manual item 1 | <m1w>             | 80.00  | <m1c>                        |
      | Manual item 2 | <m2w>             | 10.00  | <m2c>                        |
      | Manual item 3 | <m3w>             | 70.00  | <m3c>                        |
      | Manual item 4 | 0.00 %            | 90.00  | 0.00 %                       |

    Examples:
      | aggregation                         | m1w      | m1c   | m2w      | m2c   | m3w     | m3c   |
      | Natural                             | 100.00 % | 53.33 % | 66.67 %  | 6.67 % | 57.14 % | 40.00 % |
      | Simple weighted mean of grades      | 100.00 % | 53.33 % | 66.67 %  | 6.67 % | 57.14 % | 40.00 % |
      | Mean of grades (with extra credits) | 100.00 % | 53.33 % | 100.00 % | 10.00 % | 52.38 % | 36.67 % |
