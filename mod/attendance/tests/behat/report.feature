@javascript @mod @uon @mod_attendance
Feature: Visiting reports
  As a teacher I visit the reports

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary                             | category | timecreated   | timemodified  |
      | Course 1 | C1        | Prove the attendance activity works | 0        | ##yesterday## | ##yesterday## |
    And the following "users" exist:
      | username    | firstname | lastname | email            | idnumber | department       | institution |
      | student1    | Student   | 1  | student1@asd.com | 1234     | computer science | University of Nottingham |
      | teacher1    | Teacher   | 1  | teacher1@asd.com | 5678     | computer science | University of Nottingham |
    And the following "course enrolments" exist:
      | course | user     | role           | timestart     |
      | C1     | student1 | student        | ##yesterday## |
      | C1     | teacher1 | editingteacher | ##yesterday## |
    And the following config values are set as admin:
      | enablewarnings | 1 | attendance |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Attendance" to section "1" and I fill the form with:
      | Name        | Attendance       |
    And I follow "Attendance"
    And I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 01 |
      | id_sestime_endhour   | 02 |
    And I click on "id_submitbutton" "button"
    And I follow "Warnings set"
    And I press "Add warning"
    And I set the following fields to these values:
      | id_warningpercent | 84 |
      | id_warnafter   | 2 |
    And I click on "id_submitbutton" "button"
    And I log out

  Scenario: Teacher takes attendance
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Attendance"
    And I navigate to "Edit settings" in current page administration
    Then I set the following fields to these values:
      | id_grade_modgrade_type  | Point |
      | id_grade_modgrade_point | 50   |
    And I press "Save and display"

    When I follow "Report"
    Then "0 / 0" "text" should exist in the "Student 1" "table_row"
    And "0.0%" "text" should exist in the "Student 1" "table_row"

    When I follow "Grades" in the user menu
    And I follow "Course 1"
    And "-" "text" should exist in the "Student 1" "table_row"

    When I follow "Attendance"
    Then I click on "Take attendance" "link" in the "1AM - 2AM" "table_row"
    # Late
    And I click on "td.cell.c4 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Report"
    Then "1 / 2" "text" should exist in the "Student 1" "table_row"
    And "50.0%" "text" should exist in the "Student 1" "table_row"

    When I follow "Grades" in the user menu
    And I follow "Course 1"
    And "25.00" "text" should exist in the "Student 1" "table_row"

    And I log out

  Scenario: Teacher take attendance of group session
    Given the following "groups" exist:
      | course | name   | idnumber |
      | C1     | Group1 | Group1   |
    And the following "group members" exist:
      | group  | user     |
      | Group1 | student1 |

    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Attendance"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_grade_modgrade_type  | Point |
      | id_grade_modgrade_point | 50   |
      | id_groupmode            | Visible groups |
    And I press "Save and display"

    When I follow "Attendance"
    Then I click on "Take attendance" "link" in the "1AM - 2AM" "table_row"
    # Excused
    And I click on "td.cell.c4 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 03 |
      | id_sestime_endhour   | 04 |
      | id_sessiontype_1     | 1  |
      | id_groups            | Group1 |
    And I click on "id_submitbutton" "button"
    Then I should see "3AM - 4AM"
    And "Group: Group1" "text" should exist in the "3AM - 4AM" "table_row"

    When I click on "Take attendance" "link" in the "3AM - 4AM" "table_row"
    # Present
    And I click on "td.cell.c3 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Report"
    Then "3 / 4" "text" should exist in the "Student 1" "table_row"
    And "75.0%" "text" should exist in the "Student 1" "table_row"

    When I follow "Grades" in the user menu
    And I follow "Course 1"
    Then "37.50" "text" should exist in the "Student 1" "table_row"

    And I log out

  Scenario: Teacher visit summary report and absentee report
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Attendance"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
       | id_grade_modgrade_type  | Point |
       | id_grade_modgrade_point | 50   |
    And I press "Save and display"

    When I click on "Take attendance" "link" in the "1AM - 2AM" "table_row"
    # Late
    And I click on "td.cell.c4 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 03 |
      | id_sestime_endhour   | 04 |
    And I click on "id_submitbutton" "button"
    Then I should see "3AM - 4AM"

    When I click on "Take attendance" "link" in the "3AM - 4AM" "table_row"
    # Present
    And I click on "td.cell.c3 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 05 |
      | id_sestime_endhour   | 06 |
    And I click on "id_submitbutton" "button"
    Then I should see "5AM - 6AM"

    When I follow "Report"
    And I click on "Summary" "link" in the "All" "table_row"

    Then "3 / 6" "text" should exist in the "Student 1" "table_row"
    And "50.0%" "text" should exist in the "Student 1" "table_row"
    And "5 / 6" "text" should exist in the "Student 1" "table_row"
    And "83.3%" "text" should exist in the "Student 1" "table_row"

    And I follow "Absentee report"
    And I should see "Student 1"

    And I log out

  Scenario: Student visit user report
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Attendance"
    And I navigate to "Edit settings" in current page administration
    Then I set the following fields to these values:
      | id_grade_modgrade_type  | Point |
      | id_grade_modgrade_point | 50   |
    And I press "Save and display"

    When I click on "Take attendance" "link" in the "1AM - 2AM" "table_row"
    # Late
    And I click on "td.cell.c4 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 03 |
      | id_sestime_endhour   | 04 |
    And I click on "id_submitbutton" "button"

    When I click on "Take attendance" "link" in the "3AM - 4AM" "table_row"
    # Present
    And I click on "td.cell.c3 input" "css_element" in the "Student 1" "table_row"
    And I press "Save attendance"

    When I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 05 |
      | id_sestime_endhour   | 06 |
    And I click on "id_submitbutton" "button"

    Then I log out

    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Attendance"
    And I click on "All" "link" in the ".attfiltercontrols" "css_element"

    Then "2" "text" should exist in the "Taken sessions" "table_row"
    And "3 / 4" "text" should exist in the "Points over taken sessions:" "table_row"
    And "75.0%" "text" should exist in the "Percentage over taken sessions:" "table_row"
    And "3" "text" should exist in the "Total number of sessions:" "table_row"
    And "3 / 6" "text" should exist in the "Points over all sessions:" "table_row"
    And "50.0%" "text" should exist in the "Percentage over all sessions:" "table_row"
    And "5 / 6" "text" should exist in the "Maximum possible points:" "table_row"
    And "83.3%" "text" should exist in the "Maximum possible percentage:" "table_row"

    And I log out
