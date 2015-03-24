@core @core_grades
Feature: View gradebook when single item scales are used
  In order to use single item scales to grade activities
  As an teacher
  I need to be able to view gradebook with single item scales

  Background:
    Given I log in as "admin"
    And I set the following administration settings values:
      | grade_report_showranges    | 1 |
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
    And I navigate to "Scales" node in "Site administration > Grades"
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Singleitem |
      | Scale | Ace!       |
    And I press "Save changes"
    And I log out
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email            | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@asd.com | t1       |
      | student1 | Student   | 1        | student1@asd.com | s1       |
      | student2 | Student   | 2        | student2@asd.com | s2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "grade categories" exist:
      | fullname       | course |
      | Sub category 1 | C1     |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro             | gradecategory  |
      | assign   | C1     | a1       | Test assignment one | Submit something! | Sub category 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment one"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Scale"
    And I set the field "grade[modgrade_scale]" to "Singleitem"
    And I press "Save and display"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 1" "link" in the "Student 1" "table_row"
    And I set the field "Grade" to "A"
    And I press "Save changes"
    And I follow "Course 1"
    And I follow "Grades"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"
    And I follow "Grader report"
    And I turn editing mode on

  @javascript
  Scenario: Test displaying single item scales in gradebook in aggregation method Natural
    When I turn editing mode off
    Then the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-            | -6-          |
      | Student 1          | Ace!      | 1.00           | 1.00         |
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-            | -4-          |
      | Range              | Ace!–Ace! | 0.00–1.00      | 0.00–1.00    |
      | Overall average    | Ace!      | 1.00           | 1.00         |
    And I follow "User report"
    And I set the field "Select all or one user" to "Student 1"
    And the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range     | Contribution to course total |
      | Test assignment one | Ace!  | Ace!–Ace! | 100.00 %                     |
      | Sub category 1 total| 1.00  | 0–1       | -                            |
      | Course total        | 1.00  | 0–1       | -                            |
    And I set the field "Select all or one user" to "Student 2"
    And the following should exist in the "user-grade" table:
      | Grade item          | Grade | Range     | Contribution to course total |
      | Test assignment one | -     | Ace!–Ace! | -                            |
      | Sub category 1 total| -     | 0–1       | -                            |
      | Course total        | -     | 0–1       | -                            |
    And I set the field "jump" to "Categories and items"
    And the following should exist in the "grade_edit_tree_table" table:
      | Name                | Max grade |
      | Test assignment one | 1.00      |
      | Sub category 1 total| 1.00      |
      | Course total        | 1.00      |

  @javascript
  Scenario Outline: Test displaying single item scales in gradebook in all other aggregation methods
    When I follow "Edit   Course 1"
    And I set the field "Aggregation" to "<aggregation>"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I expand all fieldsets
    And I set the field "Aggregation" to "<aggregation>"
    And I set the field "Category name" to "Sub category (<aggregation>)"
    # And I set the field "Maximum grade" to "5"
    # And I set the field "Minimum grade" to "1"
    And I press "Save changes"
    And I turn editing mode off
    Then the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-            | -6-            |
      | Student 1          | Ace!      | <cattotal1>    | <coursetotal1> |
      | Student 2          | -         | -              | -              |
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-            | -4-            |
      | Range              | Ace!–Ace! | 0.00–100.0     | 0.00–100.00    |
      | Overall average    | Ace!      | <catavg>       | <overallavg>   |
    And I follow "User report"
    And I set the field "Select all or one user" to "Student 1"
    And I click on "Select all or one user" "select"
    And the following should exist in the "user-grade" table:
      | Grade item                                       | Grade          | Range       | Contribution to course total |
      | Test assignment one                              | Ace!           | Ace!–Ace!   | <contrib1>                   |
      | Sub category (<aggregation>) total<aggregation>. | <cattotal1>    | 0–100       | -                            |
      | Course total<aggregation>.                       | <coursetotal1> | 0–100       | -                            |
    And I set the field "jump" to "Categories and items"
    And the following should exist in the "grade_edit_tree_table" table:
      | Name                         | Max grade |
      | Test assignment one          | Ace! (1)  |
      | Sub category (<aggregation>) total<aggregation>. | 100.00    |
      | Course total<aggregation>.   | 100.00    |

    Examples:
      | aggregation                         | contrib1 | cattotal1 | coursetotal1 | catavg | overallavg |
      | Mean of grades                      | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
      | Weighted mean of grades             | 0.00 %   | 100.00    | -            | 100.00 | -          |
      | Simple weighted mean of grades      | 0.00 %   | -         | -            | -      | -          |
      | Mean of grades (with extra credits) | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
      | Median of grades                    | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
      | Lowest grade                        | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
      | Highest grade                       | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
      | Mode of grades                      | 100.00 % | 100.00    | 100.00       | 100.00 | 100.00     |
