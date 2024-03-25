@core @core_grades @gradereport_singleview @javascript
Feature: We can use Single view
  As a teacher
  In order to view and edit grades
  For users and activities for a course.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname    | email                | idnumber | middlename | alternatename | firstnamephonetic | lastnamephonetic |
      | teacher1 | Teacher   | 1           | teacher1@example.com | t1       |            | fred          |                   |                  |
      | teacher2 | No edit   | 1           | teacher2@example.com | t2       |            | nick          |                   |                  |
      | teacher3 | Teacher   | 3           | teacher3@example.com | t3       |            | jack          |                   |                  |
      | student1 | Grainne   | Beauchamp   | student1@example.com | s1       | Ann        | Jill          | Gronya            | Beecham          |
      | student2 | Niamh     | Cholmondely | student2@example.com | s2       | Jane       | Nina          | Nee               | Chumlee          |
      | student3 | Siobhan   | Desforges   | student3@example.com | s3       | Sarah      | Sev           | Shevon            | De-forjay        |
      | student4 | Student   | 4           | student4@example.com | s4       |            | zac           |                   |                  |
    And the following "scales" exist:
      | name | scale |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "grade items" exist:
      | itemname | course | gradetype | scale |
      | new grade item 1 | C1 | Scale | Test Scale |
    And the following "scales" exist:
      | name       | scale                                     |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | teacher3 | C1 | teacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1|
      | Sub category 2 | C1|
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And the following "grade items" exist:
      | itemname | course | gradetype |
      | Test grade item | C1 | Scale |
    And the following "permission overrides" exist:
      | capability                  | permission | role     | contextlevel  | reference |
      | moodle/grade:edit           | Allow      | teacher  | Course        | C1        |
      | gradereport/singleview:view | Allow      | teacher  | Course        | C1        |
    And the following config values are set as admin:
      | fullnamedisplay | firstnamephonetic,lastnamephonetic |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"

  Scenario: I can update grades, add feedback and exclude grades.
    Given I navigate to "View > Single view" in the course gradebook
    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I click on "Student" in the "user" search widget
    And I turn editing mode on
    And I set the field "Override for Test assignment one" to "1"
    When I set the following fields to these values:
        | Grade for Test assignment one | 10.00 |
        | Feedback for Test assignment one | test data |
    And I set the field "Exclude for Test assignment four" to "1"
    And I press "Save"
    Then I should see "Grades were set for 2 items"
    And the field "Exclude for Test assignment four" matches value "1"
    And the field "Grade for Test assignment one" matches value "10.00"
    And I set the following fields to these values:
        | Test grade item | 45 |
    And I press "Save"
    Then I should see "Grades were set for 1 items"
    And the field "Grade for Test grade item" matches value "45.00"
    And the field "Grade for Course total" matches value "55.00"
    And I open the action menu in "Test assignment three" "table_row"
    And I choose "Show all grades" in the open action menu
    And I click on "Override for Ann, Jill, Grainne, Beauchamp" "checkbox"
    And I set the following fields to these values:
        | Grade for Ann, Jill, Grainne, Beauchamp | 12.05 |
        | Feedback for Ann, Jill, Grainne, Beauchamp | test data2 |
    And I set the field "Exclude for Jane, Nina, Niamh, Cholmondely" to "1"
    And I press "Save"
    Then I should see "Grades were set for 2 items"
    And the field "Grade for Ann, Jill, Grainne, Beauchamp" matches value "12.05"
    And the field "Exclude for Jane, Nina, Niamh, Cholmondely" matches value "1"
    And I click on "new grade item 1" in the "grade" search widget
    And I set the field "Grade for Ann, Jill, Grainne, Beauchamp" to "Very good"
    And I press "Save"
    Then I should see "Grades were set for 1 items"
    And the following should exist in the "generaltable" table:
        | First name (Alternate name) Last name | Grade |
        | Ann, Jill, Grainne, Beauchamp | Very good |
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher2"
    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I click on "Student" in the "user" search widget
    And I turn editing mode on
    And the "Exclude for Test assignment one" "checkbox" should be disabled
    And the "Override for Test assignment one" "checkbox" should be enabled

  Scenario: Single view links work on grade report.
    Given I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    And I should see "Test assignment one"
    When I navigate to "View > Grader report" in the course gradebook
    And I click on user menu "Grainne Beauchamp"
    And I choose "Single view for this user" in the open action menu
    Then I should see "Gronya,Beecham"

  Scenario: I can bulk update grades.
    Given I click on user menu "Grainne Beauchamp"
    And I choose "Single view for this user" in the open action menu
    And I should see "Gronya,Beecham"
    When I turn editing mode on
    And I click on "Actions" "link"
    And I click on "Bulk insert" "link"
    And I click on "I understand that my unsaved changes will be lost." "checkbox"
    And I click on "All grades" "radio"
    And I set the field "Insert new grade" to "1.0"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    Then I should see "Grades were set for 6 items"

  Scenario: I can bulk update grades with custom decimal separator
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I click on user menu "Grainne Beauchamp"
    And I choose "Single view for this user" in the open action menu
    And I should see "Gronya,Beecham"
    When I turn editing mode on
    And I click on "Actions" "link"
    And I click on "Bulk insert" "link"
    And I click on "I understand that my unsaved changes will be lost." "checkbox"
    And I click on "All grades" "radio"
    And I set the field "Insert new grade" to "1#25"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    Then I should see "Grades were set for 6 items"
    # Custom scale, cast to int
    And the field "Grade for new grade item 1" matches value "Disappointing"
    # Value grade, float with custom decsep.
    And the field "Grade for Test assignment one" matches value "1#25"
    # Numerical scale, cast to int, showing as float with custom decsep.
    And the field "Grade for Test grade item" matches value "1#00"

  Scenario: Navigation works in the Single view.
    Given I click on user menu "Grainne Beauchamp"
    And I choose "Single view for this user" in the open action menu
    Then I should see "Gronya,Beecham"
    And I click on "Nee,Chumlee" "link" in the ".stickyfooter" "css_element"
    Then I should see "Nee,Chumlee"
    And I click on "Gronya,Beecham" "link" in the ".stickyfooter" "css_element"
    Then I should see "Gronya,Beecham"
    And I open the action menu in "Test assignment four" "table_row"
    And I choose "Show all grades" in the open action menu
    Then I should see "Test assignment four"
    And I click on "Test assignment three" in the "grade" search widget
    Then I should see "Test assignment three"
    And I click on "Test assignment four" in the "grade" search widget
    Then I should see "Test assignment four"

  Scenario: Activities are clickable only when it has a valid activity page.
    Given I click on user menu "Grainne Beauchamp"
    And I choose "Single view for this user" in the open action menu
    And "new grade item 1" "link" should not exist in the "//tbody//tr[position()=1]//td[position()=2]" "xpath_element"
    Then "Category total" "link" should not exist in the "//tbody//tr[position()=2]//td[position()=2]" "xpath_element"
    And "Course total" "link" should not exist in the "//tbody//tr[position()=last()]//td[position()=2]" "xpath_element"

  Scenario: Teacher sees his last viewed singleview report type when navigating back to the gradebook singleview report.
    Given I navigate to "View > Single view" in the course gradebook
    And I should see "Search for a user to view all their grades" in the "region-main" "region"
    And I click on "Grade items" "link"
    And I should see "Select a grade item above" in the "region-main" "region"
    When I am on the "Course 1" "grades > Single view > View" page
    Then I should see "Select a grade item above" in the "region-main" "region"
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher3"
    And I should see "Search for a user to view all their grades" in the "region-main" "region"

  Scenario: Teacher sees his last viewed user report when navigating back to the gradebook singleview report.
    Given I navigate to "View > Single view" in the course gradebook
    And I click on "Gronya,Beecham" in the "user" search widget
    And I should see "Gronya,Beecham" in the "region-main" "region"
    When I am on the "Course 1" "grades > Single view > View" page
    Then I should not see "Search for a user to view all their grades" in the "region-main" "region"
    And I should see "Gronya,Beecham" in the "region-main" "region"
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher3"
    And I should see "Search for a user to view all their grades" in the "region-main" "region"

  Scenario: Teacher sees his last viewed grade item report when navigating back to the gradebook singleview report.
    Given I navigate to "View > Single view" in the course gradebook
    And I click on "Grade items" "link"
    And I click on "Test assignment one" in the "grade" search widget
    And I should see "Test assignment one" in the "region-main" "region"
    When I am on the "Course 1" "grades > Single view > View" page
    Then I should not see "Select a grade item above" in the "region-main" "region"
    And I should see "Test assignment one" in the "region-main" "region"
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher3"
    And I should see "Search for a user to view all their grades" in the "region-main" "region"

  Scenario: Teacher sees his last viewed user report if the user is a part of the the current group.
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    And I set the field "Group mode" to "Visible groups"
    And I press "Save and display"
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Nee,Chumlee" in the "user" search widget
    And I navigate to "View > Grader report" in the course gradebook
    And I click on "Group 1" in the "group" search widget
    When I navigate to "View > Single view" in the course gradebook
    Then I should see "Nee,Chumlee" in the "region-main" "region"
    And I should not see "Search for a user to view all their grades" in the "region-main" "region"

  Scenario: Teacher does not see his last viewed user report if the user is not a part of the the current group.
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
    And I am on the "Course 1" "course editing" page
    And I expand all fieldsets
    And I set the field "Group mode" to "Visible groups"
    And I press "Save and display"
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Gronya,Beecham" in the "user" search widget
    And I navigate to "View > Grader report" in the course gradebook
    And I click on "Group 1" in the "group" search widget
    When I navigate to "View > Single view" in the course gradebook
    Then I should see "Search for a user to view all their grades" in the "region-main" "region"
    And I should not see "Gronya,Beecham" in the "region-main" "region"

  Scenario: Teacher does not see his last viewed user report if that user is no longer enrolled in the course.
    Given I navigate to "View > Single view" in the course gradebook
    And I click on "Gronya,Beecham" in the "user" search widget
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "Gronya,Beecham" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    When I am on the "Course 1" "grades > Single view > View" page
    Then I should see "Search for a user to view all their grades" in the "region-main" "region"
    And I should not see "Gronya,Beecham" in the "region-main" "region"

  Scenario: Teacher does not see his last viewed grade item report if the item no longer exists in the course.
    Given I navigate to "View > Single view" in the course gradebook
    And I click on "Grade items" "link"
    And I click on "Test assignment four" in the "grade" search widget
    And I am on "Course 1" course homepage with editing mode on
    And I delete "Test assignment four" activity
    And I run all adhoc tasks
    When I navigate to "View > Single view" in the course gradebook
    Then I should see "Select a grade item above" in the "region-main" "region"
    And I should not see "Test grade item" in the "region-main" "region"
