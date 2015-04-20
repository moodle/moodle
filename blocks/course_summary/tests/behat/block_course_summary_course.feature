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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Course/site summary" block
    And I log out

  Scenario: Student can view course summary
    When I log in as "student1"
    And I follow "Course 1"
    Then "Course/site summary" "block" should exist
    And I should see "Proved the course summary block works!" in the "Course/site summary" "block"

  Scenario: Teacher can see an edit icon when edit mode is on and follow it to the course edit page
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    Then I should see "Proved the course summary block works!" in the "Course/site summary" "block"
    And I click on "Edit" "link" in the "Course/site summary" "block"
    Then I should see "Edit course settings" in the "h2" "css_element"

  Scenario: Teacher can not see edit icon when edit mode is off
    When I log in as "teacher1"
    And I follow "Course 1"
    Then I should see "Proved the course summary block works!" in the "Course/site summary" "block"
    And "Edit" "link" should not exist in the "Course/site summary" "block"
