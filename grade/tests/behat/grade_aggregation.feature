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
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | a1 | Test assignment one | Submit something! |
      | assign | C1 | a2 | Test assignment two | Submit something! |
      | assign | C1 | a3 | Test assignment three | Submit something! |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on
    And I give the grade "60.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "20.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "40.00" to the user "Student 1" for the grade item "Test assignment three"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment two" "link"
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
    And I turn editing mode off
    Then I should see "40.00 (40.00 %)" in the ".course" "css_element"
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
  Scenario: Weighted mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I set the field "Item weight" to "3"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "48.00 (48.00 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "55.00 (55.00 %)" in the "overview-grade" "table"

  @javascript
  Scenario: Simple weighted mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Simple weighted mean of grades"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "40.00 (40.00 %)" in the ".course" "css_element"
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
  Scenario: Mean of grades (with extra credits) aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mean of grades (with extra credits)"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "40.00 (40.00 %)" in the ".course" "css_element"
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
  Scenario: Median of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Median of grades"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "40.00 (40.00 %)" in the ".course" "css_element"
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
  Scenario: Lowest grade aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Lowest grade"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "20.00 (20.00 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "40.00 (40.00 %)" in the "overview-grade" "table"

  @javascript
  Scenario: Highest grade aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Highest grade"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "60.00 (60.00 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "40.00 (40.00 %)" in the "overview-grade" "table"

  @javascript
  Scenario: Mode of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mode of grades"
    And I press "Save changes"
    And I click on "Edit  assign Test assignment one" "link"
    And I click on "Hidden" "checkbox"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "60.00 (60.00 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "40.00 (40.00 %)" in the "overview-grade" "table"

  @javascript
  Scenario: Sum of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Sum of grades"
    And I press "Save changes"
    And I turn editing mode off
    Then I should see "120.00 (40.00 %)" in the ".course" "css_element"
    And I follow "Course grade settings"
    And I set the field "Hide totals if they contain hidden items" to "Show totals excluding hidden items"
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Grades"
    And I set the field "Grade report" to "Overview report"
    And I should see "100.00 (50.00 %)" in the "overview-grade" "table"
