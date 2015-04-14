@core @core_grades @gradereport_singleview
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

  @javascript
  Scenario: I can bulk insert grades and check their override flags for grade view.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment one"
    And I follow "View/grade all submissions"
    And I follow "Grade Student 1"
    And I set the following fields to these values:
      | Grade out of 100 | 50 |
    And I press "Save changes"
    And I press "Continue"
    And I follow "View gradebook"
    And I follow "Single view for Test assignment one"
    Then the field "Grade for james (Student) 1" matches value "50.00"
    And the field "Override for james (Student) 1" matches value "0"
    And I click on "Perform bulk insert" "checkbox"
    And I set the field "Insert value" to "1.0"
    And I press "Save"
    And I press "Continue"
    And the field "Grade for james (Student) 1" matches value "50.00"
    And the field "Override for james (Student) 1" matches value "0"
    And the field "Grade for holly (Student) 2" matches value "1.00"
    And the field "Override for holly (Student) 2" matches value "1"
    And the field "Grade for anna (Student) 3" matches value "1.00"
    And the field "Override for anna (Student) 3" matches value "1"
    And the field "Grade for zac (Student) 4" matches value "1.00"
    And the field "Override for zac (Student) 4" matches value "1"
    And I click on "All grades" "option"
    And I click on "Perform bulk insert" "checkbox"
    And I set the field "Insert value" to "2.0"
    And I press "Save"
    And I press "Continue"
    And the field "Grade for james (Student) 1" matches value "2.00"
    And the field "Override for james (Student) 1" matches value "1"
    And the field "Grade for holly (Student) 2" matches value "2.00"
    And the field "Override for holly (Student) 2" matches value "1"
    And the field "Grade for anna (Student) 3" matches value "2.00"
    And the field "Override for anna (Student) 3" matches value "1"
    And the field "Grade for zac (Student) 4" matches value "2.00"
    And the field "Override for zac (Student) 4" matches value "1"

  @javascript
  Scenario: I can bulk insert grades and check their override flags for user view.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment two"
    And I follow "View/grade all submissions"
    And I follow "Grade Student 1"
    And I set the following fields to these values:
      | Grade out of 100 | 50 |
    And I press "Save changes"
    And I press "Continue"
    And I follow "View gradebook"
    And I follow "Single view for Test assignment two"
    And I click on "Student 1" "option"
    Then the field "Grade for Test assignment two" matches value "50.00"
    And the field "Override for Test assignment two" matches value "0"
    And I click on "Perform bulk insert" "checkbox"
    And I click on "Empty grades" "option"
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
