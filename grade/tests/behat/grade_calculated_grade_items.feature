@core @core_grades
Feature: Calculated grade items can be used in the gradebook
  In order to use calculated grade items in the gradebook
  As a teacher
  I need setup calculated grade items in the 'Gradebook setup' page.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
      | student2 | Student   | 2        | student2@example.com | s2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I navigate to "Gradebook setup" node in "Grade administration > Setup"

  @javascript
  Scenario: The max grade for a category item, with a calculation using Natural aggregation, can be changed
    Given I press "Add category"
    And I set the following fields to these values:
      | Category name | Calc cat |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | grade item 1 |
      | Grade category | Calc cat |
    And I press "Save changes"
    And I set "=[[gi1]]/2" calculation for grade category "Calc cat" with idnumbers:
      | grade item 1 | gi1 |
    And I set the following settings for grade item "Calc cat":
      | Maximum grade | 50 |
    And I follow "Grader report"
    And I turn editing mode on
    And I give the grade "75.00" to the user "Student 1" for the grade item "grade item 1"
    And I press "Save changes"
    And I follow "User report"
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 75.00  | 0–100 | 75.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 37.50  | 0–50  | 75.00 %    | -                            |
      | Course total                        | -                 | 37.50  | 0–50  | 75.00 %    | -                            |

  @javascript
  Scenario: Changing max grade for a category item with a calculation that has existing grades will display the same points with the new max grade values immediately.
  Given I press "Add category"
    And I set the following fields to these values:
      | Category name | Calc cat |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | grade item 1 |
      | Grade category | Calc cat |
    And I press "Save changes"
    And I set "=[[gi1]]/2" calculation for grade category "Calc cat" with idnumbers:
      | grade item 1 | gi1 |
    And I set the following settings for grade item "Calc cat":
      | Maximum grade | 50 |
    And I follow "Grader report"
    And I turn editing mode on
    And I give the grade "75.00" to the user "Student 1" for the grade item "grade item 1"
    And I press "Save changes"
    And I follow "User report"
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 75.00  | 0–100 | 75.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 37.50  | 0–50  | 75.00 %    | -                            |
      | Course total                        | -                 | 37.50  | 0–50  | 75.00 %    | -                            |
    And I navigate to "Gradebook setup" node in "Grade administration > Setup"
    And I set the following settings for grade item "Calc cat":
      | Maximum grade | 40 |
    And I follow "Grader report"
    And I give the grade "65.00" to the user "Student 2" for the grade item "grade item 1"
    And I press "Save changes"
    And I follow "User report"
    When I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 75.00  | 0–100 | 75.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 37.50  | 0–40  | 93.75 %    | -                            |
      | Course total                        | -                 | 37.50  | 0–40  | 93.75 %    | -                            |
    And I select "Student 2" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 65.00  | 0–100 | 65.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 32.50  | 0–40  | 81.25 %    | -                            |
      | Course total                        | -                 | 32.50  | 0–40  | 81.25 %    | -                            |
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the following fields to these values:
      | Min and max grades used in calculation | Initial min and max grades |
    And I press "Save changes"
    And I follow "User report"
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 75.00  | 0–100 | 75.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 37.50  | 0–40  | 93.75 %    | -                            |
      | Course total                        | -                 | 37.50  | 0–40  | 93.75 %    | -                            |
    And I select "Student 2" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item                          | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1                        | -                 | 65.00  | 0–100 | 65.00 %    | -                            |
      | Calc cat totalInclude empty grades. | 100.00 %          | 32.50  | 0–40  | 81.25 %    | -                            |
      | Course total                        | -                 | 32.50  | 0–40  | 81.25 %    | -                            |

  @javascript
  Scenario: Values in calculated grade items are not always out of one hundred
    Given I press "Add grade item"
    And I set the following fields to these values:
      | Item name | grade item 1 |
    And I press "Save changes"
    And I press "Add grade item"
    And I set the following fields to these values:
      | Item name | calc item |
    And I press "Save changes"
    And I set "=[[gi1]]/2" calculation for grade item "calc item" with idnumbers:
      | grade item 1 | gi1 |
    And I set the following settings for grade item "calc item":
      | Maximum grade | 50 |
    And I navigate to "Course grade settings" node in "Grade administration > Setup"
    And I set the following fields to these values:
      | Min and max grades used in calculation | Initial min and max grades |
    And I press "Save changes"
    And I follow "Grader report"
    And I turn editing mode on
    And I give the grade "75.00" to the user "Student 1" for the grade item "grade item 1"
    And I press "Save changes"
    And I follow "User report"
    When I select "Student 1" from the "Select all or one user" singleselect
    Then the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1 | 66.67 %           | 75.00  | 0–100 | 75.00 %    | 50.00 %                      |
      | calc item    | 33.33 %           | 37.50  | 0–50  | 75.00 %    | 25.00 %                      |
      | Course total | -                 | 112.50 | 0–150 | 75.00 %    | -                            |
    And I navigate to "Gradebook setup" node in "Grade administration > Setup"
    And I set the following settings for grade item "calc item":
      | Rescale existing grades | No |
      | Maximum grade | 40 |
    And I follow "Grader report"
    And I give the grade "65.00" to the user "Student 2" for the grade item "grade item 1"
    And I press "Save changes"
    And I follow "User report"
    And I select "Student 1" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1 | 71.43 %           | 75.00  | 0–100 | 75.00 %    | 53.57 %                      |
      | calc item    | 28.57 %           | 37.50  | 0–40  | 93.75 %    | 26.79 %                      |
      | Course total | -                 | 112.50 | 0–140 | 80.36 %    | -                            |
    And I select "Student 2" from the "Select all or one user" singleselect
    And the following should exist in the "user-grade" table:
      | Grade item   | Calculated weight | Grade  | Range | Percentage | Contribution to course total |
      | grade item 1 | 71.43 %           | 65.00  | 0–100 | 65.00 %    | 46.43 %                      |
      | calc item    | 28.57 %           | 32.50  | 0–40  | 81.25 %    | 23.21 %                      |
      | Course total | -                 | 97.50  | 0–140 | 69.64 %    | -                            |