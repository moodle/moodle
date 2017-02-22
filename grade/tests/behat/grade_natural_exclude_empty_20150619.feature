@core @core_grades
Feature: Gradebook calculations for extra credit items before the fix 20150619
  In order to make sure the grades are not changed after upgrade
  As a teacher
  I need to be able to freeze gradebook calculations

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And gradebook calculations for the course "C1" are frozen at version "20150619"
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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I set the following settings for grade item "Test assignment four (extra)":
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment five (extra)":
      | Extra credit | 1 |

  @javascript
  Scenario: No weights are overridden and student has all grades present (before the fix 20150619)
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
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
  Scenario: No weights are overridden, student has some grades present (before the fix 20150619)
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
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
  Scenario: No weights are overridden, student has none grades present except for extra credit (before the fix 20150619)
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
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
  Scenario: Make sure there are no errors when all items are marked as extra credit (before the fix 20150619)
    And I set the following settings for grade item "Test assignment one":
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment two":
      | Extra credit | 1 |
    And I set the following settings for grade item "Test assignment three":
      | Extra credit | 1 |
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
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
  Scenario: Weights are overridden and student has all grades present (before the fix 20150619)
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 50.00 %                | 80.00  | 0–100 | 80.00 %    | 40.00 %                      |
      | Test assignment two          | 10.00 %                | 30.00  | 0–50  | 60.00 %    | 6.00 %                       |
      | Test assignment three        | 40.00 %                | 150.00 | 0–200 | 75.00 %    | 30.00 %                      |
      | Test assignment four (extra) | 4.00 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 2.00 %                       |
      | Test assignment five (extra) | 2.00 %( Extra credit ) | 8.00   | 0–10  | 80.00 %    | 1.60 %                       |
      | Course total                 | -                      | 278.60 | 0–350 | 79.60 %    | -                            |
    # Contributions of extra credit "four" should be 20/350=5.71% and "five" 10/350=2.86% (350 is max grade for the course, 20 and 10 are max grades of "four" and "five")
    And I log out

  @javascript
  Scenario: Weights are overridden and student has some grades present (before the fix 20150619)
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 83.33 %                 | 80.00  | 0–100 | 80.00 %    | 66.67 %                      |
      | Test assignment two          | 16.67 %                 | 30.00  | 0–50  | 60.00 %    | 10.00 %                      |
      | Test assignment three        | 0.00 %( Empty )         | -      | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 6.67 %( Extra credit )  | 10.00  | 0–20  | 50.00 %    | 3.33 %                       |
      | Test assignment five (extra) | 3.33 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 2.67 %                       |
      | Course total                 | -                       | 124.00 | 0–150 | 82.67 %    | -                            |
    # Contributions of extra credit "four" should be 20/150=13.33% and "five" 10/150=6.67% (150 is max grade for the course, 20 and 10 are max grades of "four" and "five")
    And I log out

  @javascript
  Scenario: Weights are overridden, student has none grades present except for extra credit (before the fix 20150619)
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
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
  Scenario: Weights are overridden, including extra credit, and student has all grades present (before the fix 20150619)
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I set the field "Override weight of Test assignment four (extra)" to "1"
    And I set the field "Weight of Test assignment four (extra)" to "10"
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment one"
    And I give the grade "30.00" to the user "Student 1" for the grade item "Test assignment two"
    And I give the grade "150.00" to the user "Student 1" for the grade item "Test assignment three"
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 50.00 %                 | 80.00  | 0–100 | 80.00 %    | 40.00 %                      |
      | Test assignment two          | 8.70 %                  | 30.00  | 0–50  | 60.00 %    | 5.22 %                       |
      | Test assignment three        | 34.78 %                 | 150.00 | 0–200 | 75.00 %    | 26.09 %                      |
      | Test assignment four (extra) | 10.00 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 5.00 %                       |
      | Test assignment five (extra) | 1.74 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 1.39 %                       |
      | Course total                 | -                       | 271.93 | 0–350 | 77.70 %    | -                            |
    # Which is absolutely terrible because weights of normal items do not add up to 100%
    And I log out

  @javascript
  Scenario: Weights are overridden, including extra credit, and student has some grades present (before the fix 20150619)
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
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight       | Grade  | Range | Percentage | Contribution to course total |
      | Test assignment one          | 83.33 %                 | 80.00  | 0–100 | 80.00 %    | 66.67 %                      |
      | Test assignment two          | 0.00 %                  | 30.00  | 0–50  | 60.00 %    | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )         | -      | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 16.67 %( Extra credit ) | 10.00  | 0–20  | 50.00 %    | 8.33 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit )  | 8.00   | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                       | 112.50 | 0–150 | 75.00 %    | -                            |
    # This is just ridiculous, the grades for "two" and "five" are 0 without any reason, and sum weight of normal items is not 100% again.
    And I log out

  @javascript
  Scenario: Weights are overridden, including extra credit, student has none grades present except for extra credit (before the fix 20150619)
    When I set the field "Override weight of Test assignment one" to "1"
    And I set the field "Weight of Test assignment one" to "50"
    And I set the field "Override weight of Test assignment four (extra)" to "1"
    And I set the field "Weight of Test assignment four (extra)" to "10"
    And I press "Save changes"
    When I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "10.00" to the user "Student 1" for the grade item "Test assignment four (extra)"
    And I give the grade "8.00" to the user "Student 1" for the grade item "Test assignment five (extra)"
    And I press "Save changes"
    And I navigate to "View > User report" in the course gradebook
    And I set the field "Select all or one user" to "Student 1"
    Then the following should exist in the "user-grade" table:
      | Grade item                   | Calculated weight      | Grade | Range | Percentage | Contribution to course total |
      | Test assignment one          | 0.00 %( Empty )        | -     | 0–100 | -          | 0.00 %                       |
      | Test assignment two          | 0.00 %( Empty )        | -     | 0–50  | -          | 0.00 %                       |
      | Test assignment three        | 0.00 %( Empty )        | -     | 0–200 | -          | 0.00 %                       |
      | Test assignment four (extra) | 0.00 %( Extra credit ) | 10.00 | 0–20  | 50.00 %    | 0.00 %                       |
      | Test assignment five (extra) | 0.00 %( Extra credit ) | 8.00  | 0–10  | 80.00 %    | 0.00 %                       |
      | Course total                 | -                      | 0.00  | 0–0   |            | -                            |
    And I log out
