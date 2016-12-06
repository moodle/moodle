@gradereport @gradereport_history @_bug_phantomjs
Feature: A teacher checks the grade history report in a course
  In order to check the history of the grades
  As a teacher
  I need to check that the history report is correctly displaying changes

  @javascript
  Scenario: Check the history report displays results correctly
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | The greatest assignment ever |
      | Description | Write a behat test for Moodle - it's amazing! |
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Rewarding assignment |
      | Description | After writing your behat test go grab a beer! |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "50.00" to the user "Student 1" for the grade item "The greatest assignment ever"
    And I give the grade "60.00" to the user "Student 1" for the grade item "Rewarding assignment"
    And I give the grade "50.00" to the user "Student 2" for the grade item "The greatest assignment ever"
    And I give the grade "60.00" to the user "Student 2" for the grade item "Rewarding assignment"
    And I press "Save changes"
    And I log out
    And I log in as "teacher2"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "70.00" to the user "Student 1" for the grade item "The greatest assignment ever"
    And I give the grade "80.00" to the user "Student 1" for the grade item "Rewarding assignment"
    And I give the grade "70.00" to the user "Student 2" for the grade item "The greatest assignment ever"
    And I give the grade "80.00" to the user "Student 2" for the grade item "Rewarding assignment"
    And I press "Save changes"
    And I follow "Grade history"
    When I press "Submit"
    Then the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 1          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 2          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 2          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 1          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
      | Student 2          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 2          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by student.
    And I press "Select users"
    And I set the field with xpath "//form/input[@class='usp-search-field']" to "Student 1"
    And I press "Search"
    And I set the field with xpath "//div[@class='usp-checkbox']/input" to "1"
    And I press "Finish selecting users"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 1          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 1          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    And the following should not exist in the "gradereport_history" table:
      | Student 2          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 2          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 2          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
      | Student 2          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by assignment.
    And I click on "The greatest assignment ever" "option" in the "#id_itemid" "css_element"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
    And the following should not exist in the "gradereport_history" table:
      | Student 1          | Rewarding assignment          |                | 60.00         | Teacher 1 |
      | Student 1          | Rewarding assignment          | 60.00          | 80.00         | Teacher 2 |
    # Test filtering by grader.
    And I click on "Teacher 1" "option" in the "#id_grader" "css_element"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
    And the following should not exist in the "gradereport_history" table:
      | Student 1          | The greatest assignment ever  | 50.00          | 70.00         | Teacher 2 |
    # Test filtering by revised grades.
    And I click on "id_revisedonly" "checkbox"
    And I press "Submit"
    And the following should exist in the "gradereport_history" table:
      | First name/Surname | Grade item                    | Original grade | Revised grade | Grader    |
      | Student 1          | The greatest assignment ever  |                | 50.00         | Teacher 1 |
