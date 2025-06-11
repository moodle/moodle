@core @core_grades
Feature: Custom scales can be used to rate forum discussions
  In order to ensure custom scales are displayed in the gradebook
  As a teacher
  I need to create a new custom scale, rate a student post in a forum

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name    | idnumber |
      | forum    | C1     | Forum 1 | forum1   |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name         | message              |
      | student1 | forum1 | Discussion 1 | Discussion 1 message |
    And the following "scales" exist:
      | name        | scale                   |
      | CustomScale | Bad,OK,Pretty Good,Good |
    And I am on the "Forum 1" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "assessed" to "Average of ratings"
    And I set the field "scale[modgrade_type]" to "Scale"
    And I set the field "scale[modgrade_scale]" to "CustomScale"
    And I press "Save and display"

  @javascript
  Scenario Outline: Verify that scales are displayed in the gradebook after rating a forum discussion
    Given I follow "Discussion 1"
    And I set the field "rating" to "<scalescores>"
    When I am on the "Course 1" "grades > Grader report > View" page
    Then the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3-           |
      | Student 1 | student1@example.com | <scalescores> |
    And I am on the "Course 1" "grades > User report > View" page logged in as student1
    And "Forum 1 rating" row "Grade" column of "user-grade" table should contain "<scalescores>"

    Examples:
      | scalescores |
      | Bad         |
      | OK          |
      | Pretty Good |
      | Good        |
