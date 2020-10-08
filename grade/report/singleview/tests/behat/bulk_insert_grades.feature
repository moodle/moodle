@core @core_grades @gradereport_singleview @javascript
Feature: We can bulk insert grades for students in a course
  As a teacher
  In order to quickly grade items
  I can bulk insert values for all or empty grades.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber | alternatename |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 | fred |
      | student1 | Student | 1 | student1@example.com | s1 | james |
      | student2 | Student | 2 | student1@example.com | s2 | holly |
      | student3 | Student | 3 | student1@example.com | s3 | anna |
      | student4 | Student | 4 | student1@example.com | s4 | zac |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | a1 | Test assignment one | Submit something!   |
      | assign | C1 | a2 | Test assignment two | Submit something!   |
      | assign | C1 | a3 | Test assignment three | Submit something! |
      | assign | C1 | a4 | Test assignment four | Submit nothing!    |

  Scenario: I can bulk insert grades and check their override flags for grade view.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment one"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I follow "Single view for Test assignment one"
    Then the field "Grade for Student 1" matches value "50.00"
    And the field "Override for Student 1" matches value "0"
    And I set the field "Perform bulk insert" to "1"
    And I set the field "Insert value" to "1.0"
    And I press "Save"
    And I press "Continue"
    And the field "Grade for Student 1" matches value "50.00"
    And the field "Override for Student 1" matches value "0"
    And the field "Grade for Student 2" matches value "1.00"
    And the field "Override for Student 2" matches value "1"
    And the field "Grade for Student 3" matches value "1.00"
    And the field "Override for Student 3" matches value "1"
    And the field "Grade for Student 4" matches value "1.00"
    And the field "Override for Student 4" matches value "1"
    And I set the field "For" to "All grades"
    And I set the field "Perform bulk insert" to "1"
    And I set the field "Insert value" to "2.0"
    And I press "Save"
    And I press "Continue"
    And the field "Grade for Student 1" matches value "2.00"
    And the field "Override for Student 1" matches value "1"
    And the field "Grade for Student 2" matches value "2.00"
    And the field "Override for Student 2" matches value "1"
    And the field "Grade for Student 3" matches value "2.00"
    And the field "Override for Student 3" matches value "1"
    And the field "Grade for Student 4" matches value "2.00"
    And the field "Override for Student 4" matches value "1"

  Scenario: I can bulk insert grades and check their override flags for user view.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment two"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    # And I click on "input[title='Dock Navigation block']" "css_element"
    # And I click on "input[title='Dock Administration block']" "css_element"
    And I follow "Single view for Test assignment two"
    And I select "Student 1" from the "Select user..." singleselect
    Then the field "Grade for Test assignment two" matches value "50.00"
    And the field "Override for Test assignment two" matches value "0"
    And I set the field "For" to "Empty grades"
    And I set the field "Perform bulk insert" to "1"
    And I set the field "Insert value" to "1.0"
    And I press "Save"
    And I press "Continue"
    And the field "Grade for Test assignment two" matches value "50.00"
    And the field "Override for Test assignment two" matches value "0"
    And the field "Grade for Test assignment one" matches value "1.00"
    And the field "Override for Test assignment one" matches value "1"
    And the field "Grade for Test assignment three" matches value "1.00"
    And the field "Override for Test assignment three" matches value "1"
    And the field "Grade for Test assignment four" matches value "1.00"
    And the field "Override for Test assignment four" matches value "1"

  Scenario: I can not update grades if the value is out of bounds.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I follow "Single view for Test assignment one"
    And I set the field "Perform bulk insert" to "1"
    When I set the field "Insert value" to "-1"
    And I press "Save"
    Then I should see "The grade entered for Test assignment one for Student 1 is less than the minimum allowed"
    And I should see "The grade entered for Test assignment one for Student 2 is less than the minimum allowed"
    And I should see "The grade entered for Test assignment one for Student 3 is less than the minimum allowed"
    And I should see "The grade entered for Test assignment one for Student 4 is less than the minimum allowed"
    And I should see "Grades were set for 0 items"
