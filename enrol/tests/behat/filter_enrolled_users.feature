@core_enrol @core_group
Feature: Enrolled users can be filtered by group
  In order to filter the list of enrolled users
  As a teacher
  I need to visit the enrolled users page and select a group to filter by

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
      | teacher1 | Teacher   | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
      | student3 | C2     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group 3 | C2     | G3       |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
      | student2 | G2    |
      | student3 | G2    |
      | student1 | G3    |

  Scenario Outline:
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Enrolled users" node in "Course administration > Users"

    When I set the field "Group" to "<group>"
    And I press "Filter"

    Then I should see "<expected1>"
    And I should see "<expected2>"
    And I should see "<expected3>"
    And I should not see "<notexpected1>"
    And I should not see "<notexpected2>"
    And I should see "<expected4>"

    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.
    Examples:
      | group            | expected1 | expected2 | expected3 | expected4        | notexpected1 | notexpected2 |
      | All participants | Student 1 | Student 2 | Student 3 | 4 enrolled users | XX-IGNORE-XX | XX-IGNORE-XX |
      | No group         | Student 1 |           |           | 2 enrolled users | Student 2    | Student 3    |
      | Group 1          | Student 2 |           |           | 1 enrolled users | Student 1    | Student 3    |
      | Group 2          | Student 2 | Student 3 |           | 2 enrolled users | Student 1    | XX-IGNORE-XX |
