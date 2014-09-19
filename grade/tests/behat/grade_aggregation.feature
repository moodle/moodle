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
      | teacher1 | Teacher | 1 | teacher1@asd.com | t1 |
      | student1 | Student | 1 | student1@asd.com | s1 |
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
    And I follow "Grades"
    And I turn editing mode on
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
    And I follow "Course grade settings"
    And I set the field "Grade display type" to "Real (percentage)"
    And I press "Save changes"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "30.42 (30.42 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "26.94 (26.94 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "48.57 (48.57 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "47.22 (47.22 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "25.83 (25.83 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "0.00 (0.00 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "50.00 (50.00 %)" in the "overview-grade" "table"

  @javascript
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
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "50.00 (50.00 %)" in the "overview-grade" "table"

  @javascript
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
      | aggregationcoef2 | 0.5 |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    And I turn editing mode off
    Then I should see "152.68 (24.43 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "113.75 (23.45 %)" in the "overview-grade" "table"

  @javascript
  Scenario: Natural aggregation with drop lowest
    When I log out
    And I log in as "admin"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Natural"
    And I click on "Show more..." "link"
    And I set the field "Exclude empty grades" to "0"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Natural"
    And I click on "Show more..." "link"
    And I set the field "Exclude empty grades" to "0"
    And I press "Save changes"
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Natural"
    And I click on "Show more..." "link"
    And I set the field "Exclude empty grades" to "0"
    And I press "Save changes"
    And I expand "Setup" node
    And I follow "Simple view"
    And I press "Add category"
    And I click on "Show more" "link"
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
    And I follow "Grades"
    And I turn editing mode on
    And I give the grade "60.00" to the user "Student 1" for the grade item "Manual item 1"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I give the grade "40.00" to the user "Student 1" for the grade item "Manual item 3"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "250.00 (25.25 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I follow "Edit   Manual item 2"
    And I set the field "Extra credit" to "1"
    And I press "Save changes"
    And I turn editing mode off
    And I should see "270.00 (27.27 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I follow "Edit   Manual item 2"
    And I set the field "Extra credit" to "0"
    And I set the field "Maximum grade" to "200"
    And I press "Save changes"
    And I give the grade "21.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I turn editing mode off
    And I should see "270.00 (22.69 %)" in the ".course" "css_element"
    And I turn editing mode on
    And I follow "Edit   Manual item 2"
    And I set the field "Extra credit" to "0"
    And I set the field "Maximum grade" to "100"
    And I press "Save changes"
    And I give the grade "21.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Manual item 2"
    And I press "Save changes"
    And I turn editing mode off
    And I should see "250.00 (25.25 %)" in the ".course" "css_element"
    And I expand "Setup" node
    And I follow "Simple view"
    And I press "Add category"
    And I set the following fields to these values:
      | Category name | Sub sub category 1 |
      | Parent category | Sub category 3 |
    And I press "Save changes"
    And I follow "Grades"
    And I should see "270.00 (24.77 %)" in the ".course" "css_element"
