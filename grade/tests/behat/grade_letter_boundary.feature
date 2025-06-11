@core @core_grades
Feature: We can customise the letter boundary of a course.
  In order to change the letter boundary of a course
  As a teacher
  I need to add assessments to the gradebook.

  @javascript
  Scenario: I edit the letter boundaries of a course and grade a student.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber | alternatename |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 | Terry         |
      | student1 | Student | 1 | student1@example.com | s1 | Sally         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 100 |
    And I am on the "Course 1" "grades > course grade settings" page logged in as "teacher1"
    And I set the following fields to these values:
      | Grade display type | Letter |
    And I press "Save changes"
    And I navigate to "More > Grade letters" in the course gradebook
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Override site defaults   | 1  |
      | Letter grade boundary 10 | 57 |
    And I press "Save changes"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "57" to the user "Student 1" for the grade item "Test assignment one"
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode off
    And I navigate to "View > Grader report" in the course gradebook
    Then the following should exist in the "user-grades" table:
      | -1-       |  -2-                    |-3-  | -4- |
      | Student 1 |  student1@example.com   | D   | D   |
