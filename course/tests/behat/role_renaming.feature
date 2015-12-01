@core @core_course
Feature: Rename roles in a course
  In order to account for course-level differences
  As a teacher
  I need to be able to rename roles

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Teacher can rename roles
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I should see "Role renaming"
    When I set the following fields to these values:
      | Your word for 'Teacher' | Lecturer |
      | Your word for 'Student' | Learner  |
    And I press "Save and display"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    Then I should see "Lecturer" in the "Teacher 1" "table_row"
    And I should see "Learner" in the "Student 1" "table_row"
