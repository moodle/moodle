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
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 | fred |
      | student1 | Student | 1 | student1@example.com | s1 | james |
      | student2 | Student | 2 | student1@example.com | s2 | holly |
      | student3 | Student | 3 | student1@example.com | s3 | anna |
      | student4 | Student | 4 | student1@example.com | s4 | zac |
    And the following "scales" exist:
      | name | scale |
      | Test Scale | Disappointing, Good, Very good, Excellent |
    And the following "grade items" exist:
      | itemname | course | gradetype | scale |
      | new grade item 1 | C1 | Scale | Test Scale |
    And the following "scales" exist:
      | name       | scale                                     |
      | Test Scale | Disappointing, Good, Very good, Excellent |
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
    And the following "grade items" exist:
      | itemname | course | gradetype |
      | Test grade item | C1 | Scale |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"

  @javascript
  Scenario: I can update grades, add feedback and exclude grades.
    Given I click on "Single view" "option"
    And I click on "Student 4" "option"
    And I click on "Override for Test assignment one" "checkbox"
    When I set the following fields to these values:
        | Grade for Test assignment one | 10.00 |
        | Feedback for Test assignment one | test data |
    And I click on "Exclude for Test assignment four" "checkbox"
    And I press "Save"
    Then I should see "Grades were set for 2 items"
    And I press "Continue"
    And the field "Exclude for Test assignment four" matches value "1"
    And the field "Grade for Test assignment one" matches value "10.00"
    And I set the following fields to these values:
        | Test grade item | 45 |
    And I press "Save"
    Then I should see "Grades were set for 1 items"
    And I press "Continue"
    And the field "Grade for Test grade item" matches value "45.00"
    And the field "Grade for Course total" matches value "55.00"
    And I click on "Show grades for Test assignment three" "link"
    And I click on "Override for james (Student) 1" "checkbox"
    And I set the following fields to these values:
        | Grade for james (Student) 1 | 12.05 |
        | Feedback for james (Student) 1 | test data2 |
    And I click on "Exclude for holly (Student) 2" "checkbox"
    And I press "Save"
    Then I should see "Grades were set for 2 items"
    And I press "Continue"
    And the field "Grade for james (Student) 1" matches value "12.05"
    And the field "Exclude for holly (Student) 2" matches value "1"
    And I click on "Single view" "link"
    And I click on "new grade item 1" "option"
    And I click on "Very good" "option"
    And I press "Save"
    Then I should see "Grades were set for 1 items"
    And I press "Continue"
    And the following should exist in the "generaltable" table:
        | First name (Alternate name) Surname | Grade |
        | james (Student) 1 | Very good |

  Scenario: Single view links work on grade report.
    Given I follow "Single view for Test assignment one"
    Then I should see "Test assignment one"
    Then I follow "Grader report"
    And I follow "Single view for Student 1"
    Then I should see "Student 1"

  @javascript
  Scenario: I can bulk update grades.
    Given I follow "Single view for Student 1"
    Then I should see "Student 1"
    When I click on "All grades" "option"
    And I set the field "Insert value" to "1.0"
    And I click on "Perform bulk insert" "checkbox"
    And I press "Save"
    Then I should see "Grades were set for 9 items"

  Scenario: Navigation works in the Single view.
    Given I follow "Single view for Student 1"
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

  Scenario: Activities are clickable only when
    it has a valid activity page.
    Given I follow "Single view for Student 1"
    And "new grade item 1" "link" should not exist in the "//tbody//tr[position()=1]//td[position()=2]" "xpath_element"
    Then "Category total" "link" should not exist in the "//tbody//tr[position()=2]//td[position()=2]" "xpath_element"
    And "Course total" "link" should not exist in the "//tbody//tr[position()=last()]//td[position()=2]" "xpath_element"
