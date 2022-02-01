@core @core_course @javascript
Feature: Test if displaying the course other users works correctly:
  As a user I need to see the other users who have permissions in a course without being enrolled.

  Background:
    Given the following "categories" exist:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | CAT1     | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Test other users list in a course
    Given I log in as "admin"
    And I am on the "Course 1" "other users" page
    And I should see "Course 1: 0 other users"
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And I am on the "Course 1" "other users" page
    And I should see "Course 1: 1 other users"
    And I should see "Manager 1"
