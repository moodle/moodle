@mod @mod_data
Feature: Enable activity rating according to chosen grading scale
  In order to have ratings appear in the course gradebook
  As a teacher
  I need to enable activity rating according to chosen grading scale

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name          | idnumber |
      | data     | C1     | DB activity 1 | db1      |
    And the following "mod_data > fields" exist:
      | database | type | name     |
      | db1      | text | DB field |

  @javascript
  Scenario: View ratings in the course gradebook
    Given I am on the "DB activity 1" "data activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type        | Count of ratings |
      | scale[modgrade_type]  | Point            |
      | scale[modgrade_point] | 10               |
    And I press "Save and display"
    And the following "mod_data > entries" exist:
      | database | user     | DB field   |
      | db1      | student1 | S1 entry 1 |
      | db1      | student1 | S1 entry 2 |
      | db1      | student2 | S2 entry 1 |
    And I am on the "DB activity 1" "data activity" page
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "5"
    And I follow "Next page"
    And I set the field "rating" to "7"
    And I follow "Next page"
    And I set the field "rating" to "10"
    When I am on the "Course 1" "grades > Grader report > View" page
    Then the following should exist in the "user-grades" table:
      | -1-         | -2-                  | -3-  |
      | Student One | student1@example.com | 2.00 |
      | Student Two | student2@example.com | 1.00 |
