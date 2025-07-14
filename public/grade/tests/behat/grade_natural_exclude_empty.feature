@core @core_grades
Feature: Weights in natural aggregation are adjusted if the items are excluded from user report
  In order to correctly display user report
  As a teacher
  I need to be able to exclude hidden grades.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name                         | intro | grade |
      | assign   | C1     | a1       | Test assignment one          | x     | 100   |
      | assign   | C1     | a2       | Test assignment two          | x     | 50    |
      | assign   | C1     | a3       | Test assignment three        | x     | 200   |
      | assign   | C1     | a4       | Test assignment four (extra) | x     | 20    |
      | assign   | C1     | a5       | Test assignment five (extra) | x     | 10    |
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I set the following settings for grade item "Test assignment four (extra)" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment five (extra)" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |

  @javascript
  Scenario: No weights are overridden and student has all grades present
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 28.57 %                | 80.00  | 0–100 | 80.00 %    | 22.86 %                      |
      | Test assignment two          | 14.29 %                | 30.00  | 0–50  | 60.00 %    | 8.57 %                       |
      | Test assignment three        | 57.14 %                | 150.00 | 0–200 | 75.00 %    | 42.86 %                      |
      | Test assignment four (extra) | 5.71 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 2.86 %                       |
      | Test assignment five (extra) | 2.86 %( Extra credit ) | 8.00   | 0–10  | 80.00 %    | 2.29 %                       |
      | Course total                 | -                      | 278.00 | 0–350 | 79.43 %    | -                            |
    And I log out

  @javascript
  Scenario: No weights are overridden, student has some grades present
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 66.67 %                 | 80.00  | 0–100 | 80.00 %    | 53.33 %                      |
      | Test assignment two          | 33.33 %                 | 30.00  | 0–50  | 60.00 %    | 20.00 %                      |
      | Test assignment three        | 0.00 %( Empty )         | -      | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 13.33 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 6.67 %                       |
      | Test assignment five (extra) | 6.67 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 5.33 %                       |
      | Course total                 | -                       | 128.00 | 0–150 | 85.33 %    | -                            |
    And I log out

  @javascript
  Scenario: No weights are overridden, student has none grades present except for extra credit
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one          | 0.00 %( Empty )        | -     | 0–100 | -          | 0.00 %                       |
      | Test assignment two          | 0.00 %( Empty )        | -     | 0–50  | -          | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )        | -     | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 0.00 %( Extra credit ) | 10.00 | 0–20  | 50.00 %    | 0.00 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit ) | 8.00  | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                      | 0.00  | 0–0   |            | -                            |
    And I log out

  @javascript
  Scenario: Make sure there are no errors when all items are marked as extra credit
    And I set the following settings for grade item "Test assignment one" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment two" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment three" of type "gradeitem" on "setup" page:
      | Extra credit | 1 |
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one          | 0.00 %( Extra credit ) | 80.00 | 0–100 | 80.00 %    | 0.00 %                       |
      | Test assignment two          | 0.00 %( Extra credit ) | 30.00 | 0–50  | 60.00 %    | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )        | -     | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 0.00 %( Extra credit ) | 10.00 | 0–20  | 50.00 %    | 0.00 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit ) | 8.00  | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                      | 0.0   | 0–0   |            | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden and student has all grades present
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 50.00 %                | 80.00  | 0–100 | 80.00 %    | 40.00 %                      |
      | Test assignment two          | 10.00 %                | 30.00  | 0–50  | 60.00 %    | 6.00 %                       |
      | Test assignment three        | 40.00 %                | 150.00 | 0–200 | 75.00 %    | 30.00 %                      |
      | Test assignment four (extra) | 5.71 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 2.86 %                       |
      | Test assignment five (extra) | 2.86 %( Extra credit ) | 8.00   | 0–10  | 80.00 %    | 2.29 %                       |
      | Course total                 | -                      | 284.00 | 0–350 | 81.14 %    | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden and student has some grades present
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 83.33 %                 | 80.00  | 0–100 | 80.00 %    | 66.67 %                      |
      | Test assignment two          | 16.67 %                 | 30.00  | 0–50  | 60.00 %    | 10.00 %                      |
      | Test assignment three        | 0.00 %( Empty )         | -      | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 13.33 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 6.67 %                       |
      | Test assignment five (extra) | 6.67 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 5.33 %                       |
      | Course total                 | -                       | 133.00 | 0–150 | 88.67 %    | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden, student has none grades present except for extra credit
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one          | 0.00 %( Empty )        | -     | 0–100 | -          | 0.00 %                       |
      | Test assignment two          | 0.00 %( Empty )        | -     | 0–50  | -          | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )        | -     | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 0.00 %( Extra credit ) | 10.00 | 0–20  | 50.00 %    | 0.00 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit ) | 8.00  | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                      | 0.00  | 0–0   |            | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden, including extra credit, and student has all grades present
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I set the field "Override weight of Test assignment four (extra)" to "1"
    And I set the field "Weight of Test assignment four (extra)" to "10"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 50.00 %                 | 80.00  | 0–100 | 80.00 %    | 40.00 %                      |
      | Test assignment two          | 10.00 %                 | 30.00  | 0–50  | 60.00 %    | 6.00 %                       |
      | Test assignment three        | 40.00 %                 | 150.00 | 0–200 | 75.00 %    | 30.00 %                      |
      | Test assignment four (extra) | 10.00 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 5.00 %                       |
      | Test assignment five (extra) | 2.86 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 2.29 %                       |
      | Course total                 | -                       | 291.50 | 0–350 | 83.29 %    | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden, including extra credit, and student has some grades present
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I set the field "Override weight of Test assignment four (extra)" to "1"
    And I set the field "Weight of Test assignment four (extra)" to "10"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 83.33 %                 | 80.00  | 0–100 | 80.00 %    | 66.67 %                      |
      | Test assignment two          | 16.67 %                 | 30.00  | 0–50  | 60.00 %    | 10.00 %                      |
      | Test assignment three        | 0.00 %( Empty )         | -      | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 16.67 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 8.33 %                       |
      | Test assignment five (extra) | 6.67 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 5.33 %                       |
      | Course total                 | -                       | 135.50 | 0–150 | 90.33 %    | -                            |
    And I log out

  @javascript
  Scenario: Weights are overridden, including extra credit, student has none grades present except for extra credit
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I set the field "Override weight of Test assignment four (extra)" to "1"
    And I set the field "Weight of Test assignment four (extra)" to "10"
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one          | 0.00 %( Empty )        | -     | 0–100 | -          | 0.00 %                       |
      | Test assignment two          | 0.00 %( Empty )        | -     | 0–50  | -          | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )        | -     | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 0.00 %( Extra credit ) | 10.00 | 0–20  | 50.00 %    | 0.00 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit ) | 8.00  | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                      | 0.00  | 0–0   |            | -                            |
    And I log out
