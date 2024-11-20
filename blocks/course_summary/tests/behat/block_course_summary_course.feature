@block @block_course_summary
Feature: Course summary block used in a course
  In order to help particpants know the summary of a course
  As a teacher
  I can add the course summary block to a course page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary | category |
      | Course 1 | C101      | Proved the course summary block works! |0        |
    And the following "users" exist:
      | username    | firstname | lastname | email            |
      | student1    | Sam       | Student  | student1@example.com |
      | teacher1    | Teacher   | One      | teacher1@example.com |
    And the following "course enrolments" exist:
      | user        | course | role    |
      | student1    | C101   | student |
      | teacher1    | C101   | editingteacher |
    And I enable "course_summary" "block" plugin
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | course_summary | Course       | C101      | course-view-*   | side-pre      |

  Scenario: Student can view course summary
    When I am on the "Course 1" course page logged in as student1
    Then "Course summary" "block" should exist
    And I should see "Course summary" in the "Course summary" "block"
    And I should see "Proved the course summary block works!" in the "Course summary" "block"

  Scenario: Teacher can not see edit icon when edit mode is off
    When I am on the "Course 1" course page logged in as teacher1
    Then I should see "Proved the course summary block works!" in the "Course summary" "block"
    And I should see "Course summary" in the "Course summary" "block"
    And "Edit" "link" should not exist in the "Course summary" "block"
