@core @core_grades @gradereport_singleview
Feature: We can use Single view
  As a teacher
  In order to view and edit grades
  For users and activities for a course.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber | alternatename |
      | teacher1 | Teacher | 1 | teacher1@asd.com | t1 | fred |
      | student1 | Student | 1 | student1@asd.com | s1 | james |
      | student2 | Student | 2 | student1@asd.com | s2 | holly |
      | student3 | Student | 3 | student1@asd.com | s3 | anna |
      | student4 | Student | 4 | student1@asd.com | s4 | zac |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "grade categories" exist:
      | fullname | course |
      | Sub category 1 | C1|
      | Sub category 2 | C1|
    And the following "activities" exist:
      | activity | course | idnumber | name | intro | grade |
      | assign | C1 | a1 | Test assignment one | Submit something! | 300 |
      | assign | C1 | a2 | Test assignment two | Submit something! | 100 |
      | assign | C1 | a3 | Test assignment three | Submit something! | 150 |
      | assign | C1 | a4 | Test assignment four | Submit nothing! | 150 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"

 @javascript
  Scenario: I can update grades, add feedback and exclude grades.
    Given I click on "Single view" "option"
    And I click on "Student 4" "option"
    And I click on "Override for Test assignment one" "checkbox"
    When I set the following fields to these values:
        | Grade for Test assignment one | 10.00 |
        | Feedback for Test assignment one | test data |
    And I click on "Exclude for Test assignment four" "checkbox"
    And I press "Update"
    Then the following should exist in the "generaltable" table:
        | Test assignment four |
        | excluded |
    And the following should exist in the "generaltable" table:
        | Test assignment one |
        | 10.00 |
    And I click on "Show grades for Test assignment three" "link"
    And I click on "Override for james (Student) 1" "checkbox"
    And I set the following fields to these values:
        | Grade for james (Student) 1 | 12.05 |
        | Feedback for james (Student) 1 | test data2 |
    And I click on "Exclude for holly (Student) 2" "checkbox"
    And I press "Update"
    And the following should exist in the "generaltable" table:
        | Test assignment three |
        | 12.05 |
        | Excluded |

  Scenario: Single view links work on grade report.
    Given I follow "Single view for Test assignment one"
    Then I should see "Test assignment one"
    Then I follow "Grader report"
    And I follow "Single view for Student 1"
    Then I should see "Student 1"

  @javascript
  Scenario: Navigation works in the Single view.
    Given I click on "Single view" "option"
    Then I click on "Student 1" "option"
    Then I should see "Student 1"
    And I follow "Student 2"
    Then I should see "Student 2"
    And I follow "Student 1"
    Then I should see "Student 1"
    And I click on "Show grades for Test assignment four" "link"
    Then I should see "Test assignment four"
    And I follow "Test assignment three"
    Then I should see "Test assignment three"
    And I follow "Test assignment four"
    Then I should see "Test assignment four"
