@core @core_grades
Feature: Student and teacher's view of aggregated grade items is consistent when hidden grade items are present
  In order to calculate grade totals
  As an teacher
  I need to add aggregate columns to the gradebook

  Scenario: Natural aggregation of course categories with hidden items calculates correctly for teacher and student
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
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1 |
      | Sub category 2 | C1 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory| grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | Sub category 1 | 100 |
      | assign | C1 | a2 | Test assignment two | Submit something! | Sub category 1 | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | Sub category 2 | 100 |
      | assign | C1 | a4 | Test assignment four | Submit something! | Sub category 2 | 100 |
    And I log in as "admin"
    And I navigate to "Overview report" node in "Site administration > Grades > Report settings"
    And I set the field "s__grade_report_overview_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I navigate to "User report" node in "Site administration > Grades > Report settings"
    And I set the field "s__grade_report_user_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I press "Save changes"
    When I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "50.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "50.00" to the user "Student 1" for the grade item "Test assignment three"
    And I press "Save changes"
    And I set the following settings for grade item "Test assignment four":
      | Hidden | 1 |
    And I press "Save changes"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I select "User report" from the "Grade report" singleselect
    And I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one | 100.00 % | 50.00 | 0–100 | 50.00 % | 25.00 % |
      | Test assignment two | 0.00 %( Empty ) | - | 0–100 | - | 0.00 % |
      | Test assignment three | 100.00 % | 50.00 | 0–100 | 50.00 % | 25.00 % |
      | Course total | - | 100.00 | 0–200 | 50.00 % | - |
    When I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I navigate to "User report" node in "Grade administration"
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one | 100.00 % | 50.00 | 0–100 | 50.00 % |  25.00 % |
      | Test assignment two | -( Empty ) | - | 0–100 | - | - |
      | Test assignment three | 100.00 % | 50.00 | 0–100 | 50.00 % | 25.00 % |
      | Course total | - | 100.00 | 0–200 | 50.00 % | - |
    And I should not see "Test assignment four"
