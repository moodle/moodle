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
      | Sub category 1 | C1|
      | Sub category 2 | C1|
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a5 | Test assignment five | Submit something! | Sub category 1 | 200
      | assign | C1 | a6 | Test assignment six | Submit something! | Sub category 1 | 100
      | assign | C1 | a7 | Test assignment seven | Submit nothing! | Sub category 1 | 150
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | gradecategory | grade |
      | assign | C1 | a8 | Test assignment eight | Submit something! | Sub category 2 | 200
      | assign | C1 | a9 | Test assignment nine | Submit something! | Sub category 2 | 100
      | assign | C1 | 10 | Test assignment ten | Submit nothing! | Sub category 2 | 150
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
    And I press "Update"
    And I click on "Edit  assign Test assignment two" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment five" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment eight" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I follow "Course grade settings"
    And I set the field "Grade display type" to "Real (percentage)"
    And I press "Save changes"

  @javascript
  Scenario: Mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mean of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Mean of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Mean of grades"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I expand all fieldsets
    And I set the field "Item weight" to "1"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I expand all fieldsets
    And I set the field "Item weight" to "1"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I expand all fieldsets
    And I set the field "Item weight" to "3"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Simple weighted mean of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Simple weighted mean of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Simple weighted mean of grades"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I expand all fieldsets
    And I click on "Extra credit" "checkbox"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mean of grades (with extra credits)"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Mean of grades (with extra credits)"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Mean of grades (with extra credits)"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I expand all fieldsets
    And I set the field "Extra credit weight" to "2"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Median of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Median of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Median of grades"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Lowest grade"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Lowest grade"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Lowest grade"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment five" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment four" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Highest grade"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Highest grade"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Highest grade"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
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
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mode of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Mode of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Mode of grades"
    And I click on "Show more..." "link"
    And I click on "Aggregate only non-empty grades" "checkbox"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
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
  Scenario: Sum of grades aggregation
    And I follow "Edit   Sub category 1"
    And I set the field "Aggregation" to "Sum of grades"
    And I press "Save changes"
    And I follow "Edit   Sub category 2"
    And I set the field "Aggregation" to "Sum of grades"
    And I press "Save changes"
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Sum of grades"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment nine" "link"
    And I set the field "Extra credit" to "1"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "150.00 (19.23 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "110.00 (17.19 %)" in the "overview-grade" "table"
