@core @core_course
Feature: Students can view their grades and activity reports
  In order for students to view their grades and activity reports
  As a teacher
  I should be able to change the report display settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |

  Scenario Outline: Grade reports can be displayed or hidden to students
    Given the following "courses" exist:
      | fullname | shortname | showgrades         |
      | Course 1 | C1        | <gradevisibility> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    When I am on the "student1" "user > profile" page logged in as student1
    And I click on "Course 1" "link"
    Then I <gradelinkvisibility> see "Grades overview"

    Examples:
      | gradevisibility | gradelinkvisibility |
      | 1               | should         |
      | 0               | should not     |

  Scenario Outline: Activity reports can be displayed or hidden to students
    Given the following "courses" exist:
      | fullname | shortname | showreports        |
      | Course 1 | C1        | <reportvisibility> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    When I am on the "student1" "user > profile" page logged in as student1
    And I click on "Course 1" "link"
    Then I <reportlinkvisibility> see "Today's logs"
    And I <reportlinkvisibility> see "All logs"
    And I <reportlinkvisibility> see "Outline report"
    And I <reportlinkvisibility> see "Complete report"

    Examples:
      | reportvisibility | reportlinkvisibility |
      | 1                | should               |
      | 0                | should not           |
