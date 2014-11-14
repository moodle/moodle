@core @core_course
Feature: Managers can create courses
  In order to group users and contents
  As a manager
  I need to create courses and set default values on them

  @javascript
  Scenario: Courses are created with the default forum and blocks
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And I log in as "admin"
    And I create a course with:
      | Course full name | Course 1 |
      | Course short name | C1 |
    And I enrol "Teacher 1" user as "Teacher"
    And I enrol "Student 1" user as "Student"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    Then "Latest news" "block" should exist
    And I follow "News forum"
    And "Add a new topic" "button" should exist
    And "Forced subscription" "link" should not exist
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "News forum"
    And "Add a new topic" "button" should not exist
    And I should see "Forced subscription" in the "Administration" "block"
