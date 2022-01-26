@gradereport @gradereport_history @_bug_phantomjs
Feature: A teacher checks the grade history report in a course
  In order to check the history of the grades
  As a teacher
  I need to check that the history report is correctly displaying changes

  @javascript
  Scenario: Check the history report displays results correctly
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | food       | Favourite food |
    And the following "users" exist:
      | username | firstname | lastname | email                | profile_field_food |
      | teacher1 | Teacher   | 1        | teacher1@example.com |                    |
      | teacher2 | Teacher   | 2        | teacher2@example.com |                    |
      | student1 | Student   | 1        | student1@example.com | apple              |
      | student2 | Student   | 2        | student2@example.com | orange             |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name                         | intro                                         |
      | assign   | C1     | 1       | The greatest assignment ever | Write a behat test for Moodle - it's amazing  |
      | assign   | C1     | 1       | Rewarding assignment         | After writing your behat test go grab a beer! |
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_food |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "View > Grader report" in the course gradebook
    And I should see "apple" in the "student1" "table_row"
    And I should see "orange" in the "student2" "table_row"
    And I turn editing mode on
    And I give the grade "50.00" to the user "Student 1" for the grade item "The greatest assignment ever"
    And I give the grade "60.00" to the user "Student 1" for the grade item "Rewarding assignment"
    And I give the grade "50.00" to the user "Student 2" for the grade item "The greatest assignment ever"
    And I give the grade "60.00" to the user "Student 2" for the grade item "Rewarding assignment"
    And I press "Save changes"
    And I log out
    And I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I should see "apple" in the "student1" "table_row"
    And I should see "orange" in the "student2" "table_row"
    And I turn editing mode on
    And I give the grade "70.00" to the user "Student 1" for the grade item "The greatest assignment ever"
    And I give the grade "80.00" to the user "Student 1" for the grade item "Rewarding assignment"
    And I give the grade "70.00" to the user "Student 2" for the grade item "The greatest assignment ever"
    And I give the grade "80.00" to the user "Student 2" for the grade item "Rewarding assignment"
    And I press "Save changes"
    And I navigate to "View > Grade history" in the course gradebook
    When I press "Submit"
    Then the following should exist in the "gradereport_history" table:
      | First name/Surname | Email address        | Favourite food | Grade item                   | Original grade | Revised grade | Grader    |
      | Student 1          | student1@example.com | apple          | The greatest assignment ever |                | 50.00         | Teacher 1 |
      | Student 1          | student1@example.com | apple          | Rewarding assignment         |                | 60.00         | Teacher 1 |
      | Student 2          | student2@example.com | orange         | The greatest assignment ever |                | 50.00         | Teacher 1 |
      | Student 2          | student2@example.com | orange         | Rewarding assignment         |                | 60.00         | Teacher 1 |
      | Student 1          | student1@example.com | apple          | The greatest assignment ever | 50.00          | 70.00         | Teacher 2 |
      | Student 1          | student1@example.com | apple          | Rewarding assignment         | 60.00          | 80.00         | Teacher 2 |
      | Student 2          | student2@example.com | orange         | The greatest assignment ever | 50.00          | 70.00         | Teacher 2 |
      | Student 2          | student2@example.com | orange         | Rewarding assignment         | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by student - display of several users.
    And I press "Select users"
    And I click on "Student 1" "checkbox"
    And I click on "Student 2" "checkbox"
    And I press "Finish selecting users"
    And I should see "Student 1, Student 2"
    And I press "Submit"
    And I should see "Student 1, Student 2"
    # Test filtering by student.
    And I press "Select users"
    And I set the field with xpath "//form/input[@class='usp-search-field']" to "Student 2"
    And I click on "Search" "button" in the "Select users" "dialogue"
    And I should see "Student 2" in the "Select users" "dialogue"
    And I should not see "Student 1" in the "Select users" "dialogue"
    # Deselect.
    And I click on "Student 2" "checkbox"
    And I press "Finish selecting users"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 1          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 1          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test for seeing custom fields contents in the rows.
    And I should see "apple" in the "student1" "table_row"
    And I should not see "orange"
    And the following should not exist in the "gradereport_history" table:
      | Student 2          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 2          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 2          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 2          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by assignment.
    And I set the field "Grade item" to "The greatest assignment ever"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
    And the following should not exist in the "gradereport_history" table:
      | Student 1          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 1          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by grader.
    And I set the field "Grader" to "Teacher 1"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Email address        | Favourite food | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | student1@example.com | apple          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
    And the following should not exist in the "gradereport_history" table:
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
    # Test filtering by revised grades.
    And I click on "id_revisedonly" "checkbox"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Email address        | Favourite food | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | student1@example.com | apple          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
