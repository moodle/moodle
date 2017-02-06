@core @core_grades
Feature: We can use calculated grade totals
  In order to calculate grade totals
  As an teacher
  I need to add aggregate columns to the gradebook

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
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1 |
      | Sub category 2 | C1 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a5 | Test assignment five | Submit something! | Sub category 1 | 20 |
      | assign | C1 | a6 | Test assignment six | Submit something! | Sub category 1 | 10 |
      | assign | C1 | a7 | Test assignment seven | Submit nothing! | Sub category 1 | 15 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a8 | Test assignment eight | Submit something! | Sub category 2 | 20 |
      | assign | C1 | a9 | Test assignment nine | Submit something! | Sub category 2 | 10 |
      | assign | C1 | 10 | Test assignment ten | Submit nothing! | Sub category 2 | 15 |
    And I log in as "admin"
    And I set the following administration settings values:
      | grade_aggregations_visible | Mean of grades,Weighted mean of grades,Simple weighted mean of grades,Mean of grades (with extra credits),Median of grades,Lowest grade,Highest grade,Mode of grades,Natural |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I change window size to "large"
    And I give the grade "60.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "40.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment five"
    And I give the grade "5.00" to the user "Student 1" for the grade item "Test assignment six"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment eight"
    And I give the grade "5.00" to the user "Student 1" for the grade item "Test assignment nine"
    And I press "Save changes"
    And I set the following settings for grade item "Test assignment two":
      | Hidden | 1 |
    And I set the following settings for grade item "Test assignment five":
      | Hidden | 1 |
    And I set the following settings for grade item "Test assignment eight":
      | Hidden | 1 |
    And I change window size to "medium"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Grade display type" to "Real (percentage)"
    And I press "Save changes"

  Scenario: Mean of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Mean of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Mean of grades |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Mean of grades |
      | Exclude empty grades | 0              |
    And I turn editing mode off
    Then I should see "30.00 (30.00 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "30.42 (30.42 %)" in the "overview-grade" "table"

  Scenario: Weighted mean of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Weighted mean of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Weighted mean of grades |
      | Item weight          | 1                       |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Weighted mean of grades |
      | Item weight          | 1                       |
      | Exclude empty grades | 0                       |
    And I set the following settings for grade item "Test assignment one":
      | Item weight | 3 |
    And I turn editing mode off
    Then I should see "27.14 (27.14 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "26.94 (26.94 %)" in the "overview-grade" "table"

  Scenario: Simple weighted mean of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Simple weighted mean of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Simple weighted mean of grades |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Simple weighted mean of grades |
      | Exclude empty grades | 0                              |
    And I set the following settings for grade item "Test assignment one":
      | Extra credit | 1 |
    And I turn editing mode off
    Then I should see "45.19 (45.19 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "48.57 (48.57 %)" in the "overview-grade" "table"

  Scenario: Mean of grades (with extra credits) aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Mean of grades (with extra credits) |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Mean of grades (with extra credits) |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Mean of grades (with extra credits) |
      | Exclude empty grades | 0                                   |
    And I set the following settings for grade item "Test assignment one":
      | Extra credit weight  | 2 |
    And I turn editing mode off
    Then I should see "42.50 (42.50 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "47.22 (47.22 %)" in the "overview-grade" "table"

  Scenario: Median of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Median of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation | Median of grades |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Median of grades |
      | Exclude empty grades | 0                |
    And I turn editing mode off
    Then I should see "26.67 (26.67 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "25.83 (25.83 %)" in the "overview-grade" "table"

  Scenario: Lowest grade aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Lowest grade |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation | Lowest grade |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Lowest grade |
      | Exclude empty grades | 0            |
    And I set the following settings for grade item "Test assignment five":
      | Hidden | 1 |
    And I set the following settings for grade item "Test assignment four":
      | Hidden | 1 |
    And I turn editing mode off
    Then I should see "0.00 (0.00 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "0.00 (0.00 %)" in the "overview-grade" "table"

  Scenario: Highest grade aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Highest grade |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Highest grade |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Highest grade |
      | Exclude empty grades | 0             |
    And I set the following settings for grade item "Test assignment one":
      | Hidden | 1 |
    And I turn editing mode off
    Then I should see "50.00 (50.00 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "50.00 (50.00 %)" in the "overview-grade" "table"

  Scenario: Mode of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Mode of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Mode of grades |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Mode of grades |
      | Exclude empty grades | 0              |
    And I set the following settings for grade item "Test assignment one":
      | Hidden | 1 |
    And I turn editing mode off
    Then I should see "50.00 (50.00 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "50.00 (50.00 %)" in the "overview-grade" "table"

  Scenario: Natural aggregation on outcome items with natural weights
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    And the following "scales" exist:
      | name       | scale                                     |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "grade outcomes" exist:
      | fullname  | shortname | course | scale      |
      | Outcome 1 | OT1       | C1     | Test Scale |
    And the following "grade items" exist:
      | itemname              | course | outcome | gradetype | scale      |
      | Test outcome item one | C1     | OT1     | Scale     | Test Scale |
    And I expand "Setup" node
    And I follow "Gradebook setup"
    And I set the following settings for grade item "Course 1":
      | Aggregation                     | Natural |
      | Include outcomes in aggregation | 1       |
      | Exclude empty grades            | 0       |
    And I follow "Grader report"
    And I give the grade "Excellent" to the user "Student 1" for the grade item "Test outcome item one"
    And I press "Save changes"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "report_overview_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "report_user_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    Then I should see "114.82 (18.27 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And "Test outcome item one" row "Grade" column of "user-grade" table should contain "Excellent (100.00 %)"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I expand "Setup" node
    And I follow "Gradebook setup"
    And I set the following settings for grade item "Test outcome item one":
     | Extra credit     | 1   |
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    Then I should see "114.00 (18.39 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And "Test outcome item one" row "Grade" column of "user-grade" table should contain "Excellent (100.00 %)"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I expand "Setup" node
    And I follow "Gradebook setup"
    And I set the following settings for grade item "Course 1":
      | Aggregation                     | Natural |
      | Include outcomes in aggregation | 0       |
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    Then I should see "110.00 (17.74 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And "Test outcome item one" row "Grade" column of "user-grade" table should contain "Excellent (100.00 %)"

  Scenario: Natural aggregation on outcome items with modified weights
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    And the following "scales" exist:
      | name       | scale                                     |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "grade outcomes" exist:
      | fullname  | shortname | course | scale      |
      | Outcome 1 | OT1       | C1     | Test Scale |
    And the following "grade items" exist:
      | itemname              | course | outcome | gradetype | scale      |
      | Test outcome item one | C1     | OT1     | Scale     | Test Scale |
    And I navigate to "Grades" node in "Course administration"
    And I expand "Setup" node
    And I follow "Gradebook setup"
    And I set the following settings for grade item "Course 1":
      | Aggregation                     | Natural |
      | Include outcomes in aggregation | 1       |
      | Exclude empty grades            | 0       |
    And I set the following settings for grade item "Test outcome item one":
     | Weight adjusted  | 1   |
     | aggregationcoef2 | 100 |
    And I follow "Grader report"
    And I give the grade "Excellent" to the user "Student 1" for the grade item "Test outcome item one"
    And I press "Save changes"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "report_overview_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "report_user_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    Then I should see "4.00 (100.00 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And "Test outcome item one" row "Grade" column of "user-grade" table should contain "Excellent (100.00 %)"

  Scenario: Natural aggregation
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Natural |
      | Exclude empty grades | 1       |
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Test assignment six":
      | Weight adjusted  | 1   |
      | aggregationcoef2 | 50  |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    And I turn editing mode off
    Then I should see "152.68 (24.43 %)" in the ".course" "css_element"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "report_overview_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "report_user_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "Show contribution to course total" to "Show"
    And I set the field "Show weightings" to "Show"
    And I press "Save changes"
    And I select "User report" from the "Grade report" singleselect
    And I select "Myself" from the "View report as" singleselect
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Contribution to course total |
      | Test assignment five | 28.57 % | 10.00 (50.00 %) | 0–20 | 1.03 % |
      | Test assignment six | 50.00 % | 5.00 (50.00 %) | 0–10 | 1.80 % |
      | Test assignment seven | 21.43 % | - | 0–15 | 0.00 % |
      | Test assignment eight | 66.67 % | 10.00 (50.00 %) | 0–20 | 1.60 % |
      | Test assignment nine | 33.33 % | 5.00 (50.00 %) | 0–10 | 0.80 % |
      | Test assignment ten | 0.00 %( Empty ) | - | 0–15 | 0.00 % |
      | Test assignment one | 48.00 % | 60.00 (20.00 %) | 0–300 | 9.60 % |
      | Test assignment two | 16.00 % | 20.00 (20.00 %) | 0–100 | 3.20 % |
      | Test assignment three | 24.00 %( Extra credit ) | 40.00 (26.67 %) | 0–150 | 6.40 % |
      | Test assignment four | 24.00 % | - | 0–150 | 0.00 % |
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "113.75 (23.45 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Range | Contribution to course total |
      | Test assignment six | 70.00 % | 5.00 (50.00 %) | 0–10 | 1.80 % |
      | Test assignment seven | 30.00 % | - | 0–15 | 0.00 % |
      | Test assignment nine | 100.00 % | 5.00 (50.00 %) | 0–10 | 1.03 % |
      | Test assignment ten | -( Empty ) | - | 0–15 | - |
      | Test assignment one | 61.86 % | 60.00 (20.00 %) | 0–300 | 12.37 % |
      | Test assignment three | 30.93 %( Extra credit ) | 40.00 (26.67 %) | 0–150 | 8.25 % |
      | Test assignment four | 30.93 % | - | 0–150 | 0.00 % |

  Scenario: Natural aggregation with drop lowest
    When I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I navigate to "Gradebook setup" node in "Grade administration > Setup"
    And I press "Add category"
    And I set the following fields to these values:
      | Category name | Sub category 3 |
      | Aggregation | Natural |
      | Drop the lowest | 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 1 |
      | Grade category | Sub category 3 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 2 |
      | Grade category | Sub category 3 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | Manual item 3 |
      | Grade category | Sub category 3 |
    And I press "Save changes"
    And I follow "Grader report"
    And I give the grade "60.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I give the grade "40.00" to the user "Student 1" for the grade item "Manual item 3"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "250.00 (25.25 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I set the following settings for grade item "Manual item 2":
      | Extra credit | 1 |
    And I turn editing mode off
    And I should see "270.00 (27.27 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I set the following settings for grade item "Manual item 2":
      | Extra credit  | 0   |
      | Maximum grade | 200 |
      | Rescale existing grades | No |
    And I give the grade "21.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I turn editing mode off
    And I should see "270.00 (22.69 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I set the following settings for grade item "Manual item 2":
      | Extra credit  | 0   |
      | Maximum grade | 100 |
      | Rescale existing grades | No |
    And I give the grade "21.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I turn editing mode off
    And I should see "250.00 (25.25 %)" in the ".course" "css_element"
    And I navigate to "Gradebook setup" node in "Grade administration > Setup"
    And I press "Add category"
    And I set the following fields to these values:
      | Category name | Sub sub category 1 |
      | Parent category | Sub category 3 |
    And I press "Save changes"
    And I follow "Grader report"
    And I should see "270.00 (24.77 %)" in the ".course" "css_element"

  @javascript
  Scenario: Natural aggregation from the setup screen
    And I select "Gradebook setup" from the "Grade report" singleselect
    And I set the following settings for grade item "Course 1":
      | Aggregation          | Natural |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Natural |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Natural |

    And I set the field "Override weight of Test assignment one" to "1"
    And the field "Weight of Test assignment one" matches value "37.975"
    And I set the field "Weight of Test assignment one" to "10"

    And I set the field "Override weight of Test assignment two" to "1"
    And the field "Weight of Test assignment two" matches value "12.658"
    And I set the field "Override weight of Test assignment two" to "0"

    And I set the field "Override weight of Test assignment six" to "1"
    And the field "Weight of Test assignment six" matches value "22.222"
    And I set the field "Weight of Test assignment six" to "50"
    And I set the field "Override weight of Test assignment six" to "0"

    And I set the field "Override weight of Test assignment ten" to "1"
    And the field "Weight of Test assignment ten" matches value "33.333"
    And I set the field "Weight of Test assignment ten" to "50"

    And I set the field "Override weight of Sub category 1" to "1"
    And the field "Weight of Sub category 1" matches value "5.696"
    And I set the field "Weight of Sub category 1" to "15"

    When I press "Save changes"
    And I set the field "Override weight of Test assignment two" to "1"
    And I set the field "Override weight of Test assignment six" to "1"

    Then the field "Weight of Test assignment one" matches value "10.0"
    And the field "Weight of Test assignment two" matches value "16.854"
    And the field "Weight of Test assignment six" matches value "22.222"
    And the field "Weight of Test assignment ten" matches value "50.0"
    And the field "Weight of Sub category 1" matches value "15.0"
    And I set the field "Override weight of Test assignment one" to "0"
    And I set the field "Override weight of Test assignment two" to "0"
    And I set the field "Override weight of Test assignment six" to "0"
    And I set the field "Override weight of Sub category 1" to "0"
    And I press "Save changes"
    And I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Override weight of Sub category 1" to "1"
    And the field "Weight of Test assignment one" matches value "37.975"
    And the field "Weight of Sub category 1" matches value "5.696"
    And I reset weights for grade category "Sub category 2"
    And the field "Weight of Test assignment ten" matches value "33.333"

  @javascript
  Scenario: Natural aggregation with weights of zero
    When I set the following settings for grade item "Course 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Sub category 1":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I set the following settings for grade item "Sub category 2":
      | Aggregation          | Natural |
      | Exclude empty grades | 0       |
    And I turn editing mode off
    And I select "Gradebook setup" from the "Grade report" singleselect
    And I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "0"
    And I set the field "Override weight of Test assignment six" to "1"
    And I set the field "Weight of Test assignment six" to "0"
    And I set the field "Override weight of Test assignment nine" to "1"
    And I set the field "Weight of Test assignment nine" to "100"
    And I press "Save changes"
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "report_overview_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "report_user_showtotalsifcontainhidden" to "Show totals excluding hidden items"
    And I set the field "Show contribution to course total" to "Show"
    And I set the field "Show weightings" to "Show"
    And I press "Save changes"
    Then I should see "75.00 (16.85 %)" in the ".course" "css_element"
    And I select "User report" from the "Grade report" singleselect
    And I select "Myself" from the "View report as" singleselect
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item            | Calculated weight | Grade           | Contribution to course total |
      | Test assignment five  | 57.14 %           | 10.00 (50.00 %) | 2.25 %                        |
      | Test assignment six   | 0.00 %            | 5.00 (50.00 %)  | 0.00 %                        |
      | Test assignment seven | 42.86 %           | -               | 0.00 %                        |
      | Test assignment eight | 0.00 %            | 10.00 (50.00 %) | 0.00 %                        |
      | Test assignment nine  | 100.00 %          | 5.00 (50.00 %)  | 1.12 %                         |
      | Test assignment ten   | 0.00 %            | -               | 0.00 %                         |
      | Test assignment one   | 0.00 %            | 60.00 (20.00 %) | 0.00 %                         |
      | Test assignment two   | 22.47 %           | 20.00 (20.00 %) | 4.49 %                        |
      | Test assignment three | 33.71 %           | 40.00 (26.67 %) | 8.99 %                        |
      | Test assignment four  | 33.71 %           | -               | 0.00                         |
    And I log out
    And I log in as "student1"
    And I follow "Grades" in the user menu
    And I should see "45.00 (13.85 %)" in the "overview-grade" "table"
    And I follow "Course 1"
    And the following should exist in the "user-grade" table:
      | Grade item            | Calculated weight | Grade           | Contribution to course total |
      | Test assignment six   | 0.00 %            | 5.00 (50.00 %)  | 0.00 %                         |
      | Test assignment seven | 100.00 %          | -               | 0.00 %                         |
      | Test assignment nine  | 100.00 %          | 5.00 (50.00 %)  | 1.54 %                         |
      | Test assignment ten   | 0.00              | -               | 0.00 %                         |
      | Test assignment one   | 0.00 %            | 60.00 (20.00 %) | 0.00 %                         |
      | Test assignment three | 46.15 %           | 40.00 (26.67 %) | 12.31 %                        |
      | Test assignment four  | 46.15 %           | -               | 0.00 %                        |
