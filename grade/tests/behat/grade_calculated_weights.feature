@core @core_grades
Feature: We can understand the gradebook user report
  In order to understand the gradebook user report
  As an teacher
  I need to see the calculated weights for each type of aggregation

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
      | assign | C1 | a4 | Test assignment four | Submit something! |
      | assign | C1 | a5 | Test assignment five | Submit something! |
      | assign | C1 | a6 | Test assignment six | Submit something! |
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
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four"
    And I give the grade "70.00" to the user "Student 1" for the grade item "Test assignment five"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment six"
    And I press "Save changes"
    And I follow "Course grade settings"
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"
    And I set the field "Grade report" to "Simple view"
    And I press "Add category"
    And I set the field "Category name" to "Sub category"
    And I press "Save changes"
    And I click on "Move" "link" in the "Test assignment six" "table_row"
    # This xpath finds the forth last row in the table.
    And I click on "Move to here" "link" in the "//tbody//tr[position()=last()-3]" "xpath_element"
    And I click on "Move" "link" in the "Test assignment five" "table_row"
    And I click on "Move to here" "link" in the "//tbody//tr[position()=last()-3]" "xpath_element"
    And I click on "Move" "link" in the "Test assignment four" "table_row"
    And I click on "Move to here" "link" in the "//tbody//tr[position()=last()-3]" "xpath_element"

  @javascript
  Scenario: Mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mean of grades"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Weighted mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Weighted mean of grades"
    And I press "Save changes"
    And I set the field "Extra credit value for Test assignment one" to "2.0"
    And I set the field "Extra credit value for Test assignment two" to "1.0"
    And I set the field "Extra credit value for Test assignment three" to "1.0"
    And I press "Save changes"
    And I follow "Edit   Sub category"
    And I expand all fieldsets
    And I set the field "Item weight" to "1.0"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '40.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '20.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '20.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '40.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '20.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '20.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '6.67 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '6.67 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '6.67 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Simple weighted mean of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Simple weighted mean of grades"
    And I press "Save changes"
    And I click on "Extra credit value for Test assignment three" "checkbox"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., 'Extra credit')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '-')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Mean of grades (with extra credits) aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mean of grades (with extra credits)"
    And I press "Save changes"
    And I set the field "Extra credit value for Test assignment three" to "1.0"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., 'Extra credit')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '-')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '11.11 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Median of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Median of grades"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '25.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '8.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Lowest grade aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Lowest grade"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"


  @javascript
  Scenario: Highest grade aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Highest grade"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Mode of grades aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Mode of grades"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '100.00 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '0.00 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"

  @javascript
  Scenario: Natural aggregation
    And I follow "Edit   Course 1"
    And I set the field "Aggregation" to "Natural"
    And I press "Save changes"
    And I click on "Extra credit value for Test assignment three" "checkbox"
    And I press "Save changes"
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then "//td[contains(@headers,'weight') and contains(., '38.30 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '12.77 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'weight') and contains(., 'Extra credit')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'weight') and contains(., '33.33 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
    # Check the values in the contributions column.
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '38.30 %')]" "xpath_element" should exist in the "Test assignment one" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '12.77 %')]" "xpath_element" should exist in the "Test assignment two" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '-')]" "xpath_element" should exist in the "Test assignment three" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '7.80 %')]" "xpath_element" should exist in the "Test assignment four" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '7.80 %')]" "xpath_element" should exist in the "Test assignment five" "table_row"
    And "//td[contains(@headers,'contributiontocoursetotal') and contains(., '7.80 %')]" "xpath_element" should exist in the "Test assignment six" "table_row"
