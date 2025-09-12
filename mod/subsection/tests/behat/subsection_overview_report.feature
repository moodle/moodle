@mod @mod_subsection
Feature: The course overview report should show activities in order within subsections
  In order to have a better overview of the activities in a course
  As a teacher
  I want to see the activities in subsections in the same order as they appear on the course page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 2           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                     | course | idnumber    | section |
      | assign     | Assignment 1             | C1     | assignment1 | 1       |
      | subsection | Subsection 1             | C1     | subsection1 | 1       |
      | assign     | Assignment in subsection | C1     | assignment3 | 3       |
      | assign     | Assignment 3             | C1     | assignment3 | 1       |
    And I log in as "teacher1"

  Scenario: Activities in subsections appear in the course activities report in the same order of the course page.
    When  I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then "Assignment in subsection" "text" should appear after "Assignment 1" "text" in the "Table listing all Assignment activities" table
    And "Assignment in subsection" "text" should appear before "Assignment 3" "text" in the "Table listing all Assignment activities" table
