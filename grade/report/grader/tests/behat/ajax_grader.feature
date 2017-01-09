@gradereport @gradereport_grader
Feature: Using the AJAX grading feature of Grader report to update grades and feedback
  In order to use AJAX grading
  As a teacher
  I need to be able to update and verify grades


  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
      | student2 | Student | 2 | student2@example.com | s2 |
      | student3 | Student | 3 | student3@example.com | s3 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "scales" exist:
      | name       | scale                                  |
      | Test Scale | Disappointing,Good,Very good,Excellent |
    And the following "grade categories" exist:
      | fullname  | course |
      | Grade Cat | C1     |
    And the following "grade categories" exist:
      | fullname  | course | gradecategory |
      | Grade Sub Cat  | C1 | Grade Cat |
    And the following "grade items" exist:
      | itemname | course | locked | gradetype | gradecategory |
      | Item 1  | C1 | 0 | value | Grade Cat |
      | Item VU | C1 | 0 | value | Grade Cat |
      | Item VL | C1 | 1 | value | Grade Cat |
      | Item TU | C1 | 0 | text  | Grade Cat |
      | Item TL | C1 | 1 | text  | Grade Cat |
      | Item 3  | C1 | 0 | value | Grade Cat |
      | Calc Item  | C1 | 0 | value | Grade Cat     |
      | Item VUSub | C1 | 0 | value | Grade Sub Cat |
    And the following "grade items" exist:
      | itemname   | course | locked | gradetype | scale | gradecategory |
      | Item SU    | C1 | 0 | scale | Test Scale | Grade Cat |
      | Item SL    | C1 | 1 | scale | Test Scale | Grade Cat |
    And the following config values are set as admin:
      | grade_report_showaverages | 0 |
      | grade_report_enableajax | 1 |

  @javascript
  Scenario: Use the grader report without editing, with AJAX on and quick feedback off
    When the following config values are set as admin:
      | grade_overridecat | 1 |
      | grade_report_showquickfeedback | 0 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on student "Student 2" for grade item "Item VU"
    Then I should see a grade field for "Student 2" and grade item "Item VU"
    And I should not see a feedback field for "Student 2" and grade item "Item VU"
    And I set the field "ajaxgrade" to "33"
    And I press key "13" in the field "ajaxgrade"
    And I should not see a grade field for "Student 2" and grade item "Item VU"
    And I should not see a feedback field for "Student 2" and grade item "Item VU"
    And I click on student "Student 3" for grade item "Item VU"
    And I set the field "ajaxgrade" to "50"
    And I press key "13" in the field "ajaxgrade"
    And I click on student "Student 3" for grade item "Item 1"
    And I set the field "ajaxgrade" to "80"
    And I press key "13" in the field "ajaxgrade"
    And I click on student "Student 3" for grade item "Item SU"
    And I set the field "ajaxgrade" to "Very good"
    And I press key "13" in the field "ajaxgrade"
    And the following should exist in the "user-grades" table:
      | -1-                | -6-      | -7-      | -13-      | -16-         |
      | Student 2          | -        | 33.00    | -         | 33.00        |
      | Student 3          | 80.00    | 50.00    | Very good | 133.00       |
    And I click on student "Student 3" for grade item "Item VL"
    And I should not see a grade field for "Student 3" and grade item "Item VL"
    And I should not see a feedback field for "Student 3" and grade item "Item VL"
    And I click on student "Student 3" for grade item "Item SL"
    And I should not see a grade field for "Student 3" and grade item "Item SL"
    And I should not see a feedback field for "Student 3" and grade item "Item SL"
    And I click on student "Student 3" for grade item "Item TU"
    And I should not see a grade field for "Student 3" and grade item "Item TU"
    And I should not see a feedback field for "Student 3" and grade item "Item TU"
    And I click on student "Student 1" for grade item "Course total"
    And I should see a grade field for "Student 1" and grade item "Course total"
    And I should not see a feedback field for "Student 1" and grade item "Course total"
    And I set the field "ajaxgrade" to "90"
    And I press key "13" in the field "ajaxgrade"
    And the following should exist in the "user-grades" table:
      | -1-                | -16-      |
      | Student 1          | 90.00     |
    And I navigate to "View > Grader report" in the course gradebook
    And the following should exist in the "user-grades" table:
      | -1-                | -6-   | -7-   | -13-      | -16-      |
      | Student 1          | -     | -     | -         | 90.00     |
      | Student 2          | -     | 33.00 | -         | 33.00     |
      | Student 3          | 80.00 | 50.00 | Very good | 133.00    |

  @javascript
  Scenario: Use the grader report without editing, with AJAX and quick feedback on
    When the following config values are set as admin:
      | grade_overridecat | 1 |
      | grade_report_showquickfeedback | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on student "Student 2" for grade item "Item VU"
    Then I should see a grade field for "Student 2" and grade item "Item VU"
    And I should see a feedback field for "Student 2" and grade item "Item VU"
    And I set the field "ajaxgrade" to "33"
    And I set the field "ajaxfeedback" to "Student 2 VU feedback"
    And I press key "13" in the field "ajaxfeedback"
    And I click on student "Student 3" for grade item "Item VL"
    And I should not see a grade field for "Student 3" and grade item "Item VL"
    And I should not see a feedback field for "Student 3" and grade item "Item VL"
    And I click on student "Student 3" for grade item "Item TU"
    And I should not see a grade field for "Student 3" and grade item "Item TU"
    And I should see a feedback field for "Student 3" and grade item "Item TU"
    And I set the field "ajaxfeedback" to "Student 3 TU feedback"
    And I press key "13" in the field "ajaxfeedback"
    And I click on student "Student 2" for grade item "Item SU"
    And I set the field "ajaxgrade" to "Very good"
    And I set the field "ajaxfeedback" to "Student 2 SU feedback"
    And I press key "13" in the field "ajaxfeedback"
    # Reload grader report:
    And I navigate to "View > User report" in the course gradebook
    And I navigate to "View > Grader report" in the course gradebook
    And the following should exist in the "user-grades" table:
      | -1-       | -7-   | -13-      | -16-  |
      | Student 2 | 33.00 | Very good | 36.00 |
    And I click on student "Student 3" for grade item "Item TU"
    And the field "ajaxfeedback" matches value "Student 3 TU feedback"
    And I click on student "Student 2" for grade item "Item SU"
    And the field "ajaxfeedback" matches value "Student 2 SU feedback"

  @javascript
  Scenario: Use the grader report without editing, with AJAX and quick feedback on, without category override
    When the following config values are set as admin:
      | grade_overridecat | 0 |
      | grade_report_showquickfeedback | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on student "Student 2" for grade item "Item VU"
    Then I should see a grade field for "Student 2" and grade item "Item VU"
    And I should see a feedback field for "Student 2" and grade item "Item VU"
    And I set the field "ajaxgrade" to "33"
    And I press key "13" in the field "ajaxgrade"
    And I click on student "Student 2" for grade item "Course total"
    And I should not see a grade field for "Student 3" and grade item "Course total"
    And I should not see a feedback field for "Student 3" and grade item "Course total"
    And the following should exist in the "user-grades" table:
      | -1-         | -7-      | -16-    |
      | Student 2   | 33.00    | 33.00   |

  @javascript
  Scenario: Use the grader report with editing, with AJAX and quick feedback on, with category override
    When the following config values are set as admin:
      | grade_overridecat | 1 |
      | grade_report_showquickfeedback | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    Then I should not see a grade field for "Student 2" and grade item "Item VL"
    And I should not see a feedback field for "Student 2" and grade item "Item VL"
    And I should not see a grade field for "Student 2" and grade item "Item TU"
    And I should see a feedback field for "Student 2" and grade item "Item TU"
    And I should see a grade field for "Student 2" and grade item "Course total"
    And I should see a feedback field for "Student 2" and grade item "Course total"
    And I give the grade "20.00" to the user "Student 2" for the grade item "Item VU"
    And I click away from student "Student 2" and grade item "Item VU" value
    And I give the grade "30.00" to the user "Student 2" for the grade item "Item 1"
    And I give the feedback "Some feedback" to the user "Student 2" for the grade item "Item 1"
    And I click away from student "Student 2" and grade item "Item 1" feedback
    And I give the grade "Very good" to the user "Student 2" for the grade item "Item SU"
    And I click away from student "Student 2" and grade item "Item SU" value
    And the grade for "Student 2" in grade item "Grade Cat" should match "53.00"
    And the grade for "Student 2" in grade item "Course total" should match "53.00"
    And I turn editing mode off
    And the following should exist in the "user-grades" table:
      | -1-        | -6-      | -7-     | -13-      | -15-     | -16-    |
      | Student 2  | 30.00    | 20.00   | Very good | 53.00    | 53.00   |
    And I click on student "Student 2" for grade item "Item 1"
    And the field "ajaxfeedback" matches value "Some feedback"

  @javascript
  Scenario: Use the grader report with editing, with AJAX and quick feedback on, without category override
    When the following config values are set as admin:
      | grade_overridecat | 0 |
      | grade_report_showquickfeedback | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I change window size to "large"
    And I set "=[[i1]] + [[i3]] + [[gsc]]" calculation for grade item "Calc Item" with idnumbers:
      | Item 1        | i1  |
      | Item 3        | i3  |
      | Grade Sub Cat | gsc |
    Then I should not see a grade field for "Student 2" and grade item "Course total"
    And I should not see a feedback field for "Student 2" and grade item "Course total"
    And I give the grade "20.00" to the user "Student 2" for the grade item "Item VU"
    And I click away from student "Student 2" and grade item "Item VU" value
    And the following should exist in the "user-grades" table:
      | -1-        | -15-   | -16-  |
      | Student 2  | 20.00  | 20.00 |
    And I give the grade "30.00" to the user "Student 2" for the grade item "Item 1"
    And I click away from student "Student 2" and grade item "Item 1" value
    And the following should exist in the "user-grades" table:
      | -1-        | -15-  | -16-  |
      | Student 2  | 80.00 | 80.00 |
    And the field "Student 2 Calc Item grade" matches value "30.00"
    And I give the grade "5.00" to the user "Student 2" for the grade item "Item 3"
    And I click away from student "Student 2" and grade item "Item 3" value
    And the following should exist in the "user-grades" table:
      | -1-        | -15-  | -16- |
      | Student 2  | 90.00 | 90.00 |
    And the field "Student 2 Calc Item grade" matches value "35.00"
    And I give the grade "10.00" to the user "Student 2" for the grade item "Item VUSub"
    And I click away from student "Student 2" and grade item "Item VUSub" value
    And the following should exist in the "user-grades" table:
      | -1-        | -5-   | -15-   | -16-   |
      | Student 2  | 10.00 | 110.00 | 110.00 |
    And the field "Student 2 Calc Item grade" matches value "45.00"
    And I give the feedback "Some feedback" to the user "Student 2" for the grade item "Item 1"
    And I click away from student "Student 2" and grade item "Item 1" feedback
    And I turn editing mode off
    And the following should exist in the "user-grades" table:
      | -1-        | -4-   | -6-   | -7-   | -11- | -12-  | -15-   | -16-   |
      | Student 2  | 10.00 | 30.00 | 20.00 | 5.00 | 45.00 | 110.00 | 110.00 |
    And I click on student "Student 2" for grade item "Item 1"
    And the field "ajaxfeedback" matches value "Some feedback"
