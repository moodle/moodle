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
      | teacher1 | Teacher | 1 | teacher1@asd.com | t1 | teacherbro |
      | student1 | Student | 1 | student1@asd.com | s1 | studentbro |
      | student2 | Student | 2 | student1@asd.com | s2 | studentjo |
      | student3 | Student | 3 | student1@asd.com | s3 | studentlo |
      | student4 | Student | 4 | student1@asd.com | s4 | studentawesemo |
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
    And I click on "studentawesemo (Student) 4" "option"
    And I click on "Override for Test assignment one" "checkbox"
    When I set the following fields to these values:
        | Grade for Test assignment one | 10.00 |
        | Feedback for Test assignment one | test data |
    And I click on "Exclude for Test assignment four" "checkbox"
    And I press "Update"
    Then the following should exist in the "user-grades" table:
        | Test assignment four |
        | excluded |
    And the following should exist in the "user-grades" table:
        | Test assignment one |
        | 10.00 |
    And I click on "Single view for Test assignment three" "link"
    And I click on "Override for studentbro (Student) 1" "checkbox"
    And I set the following fields to these values:
        | Grade for studentbro (Student) 1 | 12.05 |
        | Feedback for studentbro (Student) 1 | test data2 |
    And I click on "Exclude for studentjo (Student) 2" "checkbox"
    And I press "Update"
    And the following should exist in the "user-grades" table:
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
    Then I click on "studentbro (Student) 1" "option"
    Then I should see "Student 1"
    And I follow "studentjo (Student) 2"
    Then I should see "Student 2"
    And I follow "studentbro (Student) 1"
    Then I should see "Student 1"
    And I click on "Show grades for Test assignment four" "link"
    Then I should see "Test assignment four"
    And I follow "Test assignment three"
    Then I should see "Test assignment three"
    And I follow "Test assignment four"
    Then I should see "Test assignment four"
