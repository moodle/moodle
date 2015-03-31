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
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the field "Show weightings" to "Show"
    And I set the field "Show contribution to course total" to "Show"
    And I press "Save changes"
    And I set the field "Grade report" to "Categories and items"
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
    And I set the following settings for grade item "Course 1":
      | Aggregation | Mean of grades |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 25.00 % | 60.00 | 15.00 % |
      | Test assignment two | 25.00 % | 20.00 | 5.00 % |
      | Test assignment three | 25.00 % | 40.00 | 10.00 % |
      | Test assignment four | 33.33 % | 10.00 | 0.83 % |
      | Test assignment five | 33.33 % | 70.00 | 5.83 % |
      | Test assignment six | 33.33 % | 30.00 | 2.50 % |

  @javascript
  Scenario: Weighted mean of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Weighted mean of grades |
    And I set the following settings for grade item "Test assignment one":
      | Item weight | 2.0 |
    And I set the following settings for grade item "Test assignment two":
      | Item weight | 1.0 |
    And I set the following settings for grade item "Test assignment three":
      | Item weight | 1.0 |
    And I set the following settings for grade item "Sub category":
      | Item weight | 1.0 |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 40.00 % | 60.00 | 24.00 % |
      | Test assignment two | 20.00 % | 20.00 | 4.00 % |
      | Test assignment three | 20.00 % | 40.00 | 8.00 % |
      | Test assignment four | 33.33 % | 10.00 | 0.67 % |
      | Test assignment five | 33.33 % | 70.00 | 4.67 % |
      | Test assignment six | 33.33 % | 30.00 | 2.00 % |

  @javascript
  Scenario: Simple weighted mean of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Simple weighted mean of grades |
    And I set the following settings for grade item "Sub category":
      | Aggregation | Simple weighted mean of grades |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 33.33 % | 60.00 | 20.00 % |
      | Test assignment two | 33.33 % | 20.00 | 6.67 % |
      | Test assignment three | 33.33 %( Extra credit ) | 40.00 | 13.33 % |
      | Test assignment four | 33.33 % | 10.00 | 1.11 % |
      | Test assignment five | 33.33 % | 70.00 | 7.78 % |
      | Test assignment six | 33.33 % | 30.00 | 3.33 % |

  @javascript
  Scenario: Mean of grades (with extra credits) aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Mean of grades (with extra credits) |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit weight | 1.0 |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 33.33 % | 60.00 | 20.00 % |
      | Test assignment two | 33.33 % | 20.00 | 6.67 % |
      | Test assignment three | 33.33 %( Extra credit ) | 40.00 | 13.33 % |
      | Test assignment four | 33.33 % | 10.00 | 1.11 % |
      | Test assignment five | 33.33 % | 70.00 | 7.78 % |
      | Test assignment six | 33.33 % | 30.00 | 3.33 % |

  @javascript
  Scenario: Median of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Median of grades |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 0.00 % | 60.00 | 0.00 % |
      | Test assignment two | 0.00 % | 20.00 | 0.00 % |
      | Test assignment three | 50.00 % | 40.00 | 20.00 % |
      | Test assignment four | 33.33 % | 10.00 | 1.67 % |
      | Test assignment five | 33.33 % | 70.00 | 11.67 % |
      | Test assignment six | 33.33 % | 30.00 | 5.00 % |

  @javascript
  Scenario: Lowest grade aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Lowest grade |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 0.00 % | 60.00 | 0.00 % |
      | Test assignment two | 100.00 % | 20.00 | 20.00 % |
      | Test assignment three | 0.00 % | 40.00 | 0.00 % |
      | Test assignment four | 33.33 % | 10.00 | 0.00 % |
      | Test assignment five | 33.33 % | 70.00 | 0.00 % |
      | Test assignment six | 33.33 % | 30.00 | 0.00 % |

  @javascript
  Scenario: Highest grade aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Highest grade |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 100.00 % | 60.00 | 60.00 % |
      | Test assignment two | 0.00 % | 20.00 | 0.00 % |
      | Test assignment three | 0.00 % | 40.00 | 0.00 % |
      | Test assignment four | 33.33 % | 10.00 | 0.00 % |
      | Test assignment five | 33.33 % | 70.00 | 0.00 % |
      | Test assignment six | 33.33 % | 30.00 | 0.00 % |

  @javascript
  Scenario: Mode of grades aggregation
    And I set the following settings for grade item "Course 1":
      | Aggregation | Mode of grades |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 100.00 % | 60.00 | 60.00 % |
      | Test assignment two | 0.00 % | 20.00 | 0.00 % |
      | Test assignment three | 0.00 % | 40.00 | 0.00 % |
      | Test assignment four | 33.33 % | 10.00 | 0.00 % |
      | Test assignment five | 33.33 % | 70.00 | 0.00 % |
      | Test assignment six | 33.33 % | 30.00 | 0.00 % |

  @javascript
  Scenario: View user report with mixed aggregation methods
    And I set the following settings for grade item "Course 1":
      | Aggregation | Natural |
    And I set the following settings for grade item "Sub category":
      | Aggregation | Weighted mean of grades |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 33.33 % | 60.00 | 20.00 % |
      | Test assignment two | 33.33 % | 20.00 | 6.67 % |
      | Test assignment three | 33.33 %( Extra credit ) | 40.00 | 13.33 % |
      | Test assignment four | 33.33 % | 10.00 | 1.11 % |
      | Test assignment five | 33.33 % | 70.00 | 7.78 % |
      | Test assignment six | 33.33 % | 30.00 | 3.33 % |
      | Sub category totalWeighted mean of grades. | 33.33 % | 36.67 | - |
      | Course total | - | 156.67 | - |

  @javascript
  Scenario: View user report with natural aggregation
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    And I set the field "Grade report" to "User report"
    And I set the field "Select all or one user" to "Student 1"

    # Check the values in the weights column.
    Then the following should exist in the "user-grade" table:
      | Grade item | Calculated weight | Grade | Contribution to course total |
      | Test assignment one | 20.00 % | 60.00 | 12.00 % |
      | Test assignment two | 20.00 % | 20.00 | 4.00 % |
      | Test assignment three | 20.00 %( Extra credit ) | 40.00 | 8.00 % |
      | Test assignment four | 33.33 % | 10.00 | 2.00 % |
      | Test assignment five | 33.33 % | 70.00 | 14.00 % |
      | Test assignment six | 33.33 % | 30.00 | 6.00 % |
      | Sub category total | 60.00 % | 110.00 | - |
      | Course total | - | 230.00 | - |
