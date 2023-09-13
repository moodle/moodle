@core @core_grades @javascript
Feature: We can choose what min or max grade to use when aggregating grades.
  In order to what min or max grade to use
  As an teacher
  I can update modify a course setting

  Scenario: Changing the min or max grade to use updates the grades accordingly
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | C1 | C1 | 0 | 1 |
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
      | CAT1 | C1 |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_minmaxtouse | Min and max grades as specified in grade item settings |
    And I am on the "C1" "grades > gradebook setup" page
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | MI 1 |
      | Grade category | C1 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | MI 2 |
      | Grade category | C1 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | MI 3 |
      | Grade category | CAT1 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | MI 4 |
      | Grade category | CAT1 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I choose the "Add grade item" item in the "Add" action menu
    And I set the following fields to these values:
      | Item name | MI 5 |
      | Grade category | C1 |
    And I click on "Save" "button" in the "New grade item" "dialogue"
    And I set "=[[mi1]]+[[mi2]]+[[mi3]]" calculation for grade item "MI 5" with idnumbers:
      | MI 1 | mi1 |
      | MI 2 | mi2 |
      | MI 3 | mi3 |
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the field "Min and max grades used in calculation" to "Default (Min and max grades as specified in grade item settings)"
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "CAT1" of type "category" on "setup" page:
      | Aggregation          | Natural |
    And I am on the "C1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I give the grade "75.00" to the user "Student 1" for the grade item "MI 1"
    And I give the grade "25.00" to the user "Student 1" for the grade item "MI 2"
    And I give the grade "50.00" to the user "Student 1" for the grade item "MI 3"
    And I give the grade "100.00" to the user "Student 1" for the grade item "MI 4"
    And I give the grade "20.00" to the user "Student 2" for the grade item "MI 1"
    And I give the grade "10.00" to the user "Student 2" for the grade item "MI 3"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 20.00 %           | 75.00  | 0–100 | 75.00 %    | 15.00 %                         |
      | MI 2         | 20.00 %           | 25.00  | 0–100 | 25.00 %    | 5.00 %                          |
      | MI 3         | 50.00 %           | 50.00  | 0–100 | 50.00 %    | 10.00 %                         |
      | MI 4         | 50.00 %           | 100.00 | 0–100 | 100.00 %   | 20.00 %                         |
      | MI 5         | 20.00 %           | 100.00 | 0–100 | 100.00 %   | 20.00 %                         |
      | CAT1 total   | 40.00 %           | 150.00 | 0–200 | 75.00 %    | -                               |
      | Course total | -                 | 350.00 | 0–500 | 70.00 %    | -                               |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 33.33 %           | 20.00  | 0–100 | 20.00 %    | 6.67 %                      |
      | MI 2         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 3         | 100.00 %          | 10.00  | 0–100 | 10.00 %    | 3.33 %                       |
      | MI 4         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 5         | 33.33 %           | 30.00  | 0–100 | 30.00 %    | 10.00 %                      |
      | CAT1 total   | 33.33 %           | 10.00  | 0–100 | 10.00 %    | -                            |
      | Course total | -                 | 60.00  | 0–300 | 20.00 %    | -                            |
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "MI 1" of type "gradeitem" on "setup" page:
      | Rescale existing grades | No    |
      | Maximum grade           | 50.00 |
      | Minimum grade           | 5.00  |
    And I set the following settings for grade item "MI 3" of type "gradeitem" on "setup" page:
      | Rescale existing grades | No    |
      | Maximum grade           | 50.00 |
      | Minimum grade           | 5.00  |
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 12.50 %           | 75.00  | 5–50  | 100.00 %   | 18.75 %                      |
      | MI 2         | 25.00 %           | 25.00  | 0–100 | 25.00 %    | 6.25 %                       |
      | MI 3         | 33.33 %           | 50.00  | 5–50  | 100.00 %   | 12.50 %                      |
      | MI 4         | 66.67 %           | 100.00 | 0–100 | 100.00 %   | 25.00 %                      |
      | MI 5         | 25.00 %           | 100.00 | 0–100 | 100.00 %   | 25.00 %                      |
      | CAT1 total   | 37.50 %           | 150.00 | 0–150 | 100.00 %   | -                            |
      | Course total | -                 | 350.00 | 0–400 | 87.50 %    | -                            |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 25.00 %           | 20.00  | 5–50  | 33.33 %    | 10.00 %                      |
      | MI 2         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 3         | 100.00 %          | 10.00  | 5–50  | 11.11 %    | 5.00 %                       |
      | MI 4         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 5         | 50.00 %           | 30.00  | 0–100 | 30.00 %    | 15.00 %                      |
      | CAT1 total   | 25.00 %           | 10.00  | 0–50  | 20.00 %    | -                            |
      | Course total | -                 | 60.00  | 0–200 | 30.00 %    | -                            |
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "MI 5" of type "gradeitem" on "setup" page:
      | Rescale existing grades | No    |
      | Maximum grade          | 200.00 |
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 5         | 40.00 %           | 150.00 | 0–200 | 75.00 %   | 30.00 %                      |
      | Course total | -                 | 400.00 | 0–500 | 80.00 %    | -                            |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 5         | 66.67 %           | 30.00 | 0–200  | 15.00 %    | 10.00 %                      |
      | Course total | -                 | 60.00 | 0–300  | 20.00 %    | -                            |
    And I navigate to "Setup > Course grade settings" in the course gradebook
    When I set the field "Min and max grades used in calculation" to "Initial min and max grades"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "user" search widget
    Then the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 16.67 %           | 75.00  | 0–100 | 75.00 %    | 12.50 %                      |
      | MI 2         | 16.67 %           | 25.00  | 0–100 | 25.00 %    | 4.17 %                       |
      | MI 3         | 50.00 %           | 50.00  | 0–100 | 50.00 %    | 8.33 %                       |
      | MI 4         | 50.00 %           | 100.00 | 0–100 | 100.00 %   | 16.67 %                      |
      | MI 5         | 33.33 %           | 150.00 | 0–200 | 75.00 %    | 25.00 %                      |
      | CAT1 total   | 33.33 %           | 150.00 | 0–200 | 75.00 %    | -                            |
      | Course total | -                 | 400.00 | 0–600 | 66.67 %    | -                            |
    And I click on "Student 2" in the "user" search widget
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | MI 1         | 25.00 %           | 20.00  | 0–100 | 20.00 %    | 5.00 %                       |
      | MI 2         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 3         | 100.00 %          | 10.00  | 0–100 | 10.00 %    | 2.50 %                       |
      | MI 4         | 0.00 %            | -      | 0–100 | -          | 0.00 %                       |
      | MI 5         | 50.00 %           | 30.00  | 0–200 | 15.00 %    | 7.50 %                       |
      | CAT1 total   | 25.00 %           | 10.00  | 0–100 | 10.00 %    | -                            |
      | Course total | -                 | 60.00  | 0–400 | 15.00 %    | -                            |
