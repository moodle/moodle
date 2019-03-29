@core @core_message @javascript
Feature: Create conversations for course's groups
  In order to manage a course group in a course
  As a user
  I need to be able to ensure group conversations reflect the memberships of course groups

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student0 | Student   | 0        | student0@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role |
      | teacher1 | C1     | editingteacher |
      | student0 | C1     | student |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | student3 | C1     | student |
      | student4 | C1     | student |
    And the following "groups" exist:
      | name    | course | idnumber | enablemessaging |
      | Group 1 | C1     | G1       | 1               |
      | Group 2 | C1     | G2       | 1               |
      | Group 3 | C1     | G3       | 0               |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1 |
      | student0 | G1 |
      | student1 | G1 |
      | student2 | G1 |
      | student3 | G1 |
      | teacher1 | G2 |
      | teacher1 | G3 |
      | student0 | G3 |

  Scenario: Group conversations are restricted to members
    Given I log in as "teacher1"
    Then I open messaging
    And "Group 1" "group_message" should exist
    And "Group 2" "group_message" should exist
    And "Group 3" "group_message" should not exist
    And I log out
    And I log in as "student1"
    And I open messaging
    And "Group 1" "group_message" should exist
    And "Group 2" "group_message" should not exist
    And "Group 3" "group_message" should not exist
