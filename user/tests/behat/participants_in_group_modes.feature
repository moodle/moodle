@core @core_course @javascript
Feature: Viewing participants page in different group modes
  In order to view my peers
  As a student
  I need to be able to browse participants

  Background:
    Given the following "courses" exist:
      | fullname     | shortname | summary | groupmode | category |
      | C1 nogroups  | C1        |         | 0         | 0        |
      | C2 visgroups | C2        |         | 2         | 0        |
      | C3 sepgroups | C3        |         | 1         | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |
      | student2 | C1     | student |
      | student2 | C2     | student |
      | student2 | C3     | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | G1   | C2     | C2G1      |
      | G2   | C2     | C2G2      |
      | G1   | C3     | C3G1      |
      | G2   | C3     | C3G2      |
    And the following "group members" exist:
      | user     | group |
      | student1 | C2G1   |
      | student1 | C3G1   |
      | student2 | C2G2   |
      | student2 | C3G2   |

  Scenario: Viewing participants page in a course without group mode
    When I log in as "student1"
    And I am on "C1 nogroups" course homepage
    And I navigate to course participants
    Then "Student 1" row "Groups" column of "participants" table should contain "No groups"
    And "Student 2" row "Groups" column of "participants" table should contain "No groups"

  Scenario: Viewing participants page in a course in visible groups mode
    When I log in as "student1"
    And I am on "C2 visgroups" course homepage
    And I navigate to course participants
    Then "Student 1" row "Groups" column of "participants" table should contain "G1"
    And I should not see "Student 2"
    And I click on "Clear filters" "button"
    And "Student 1" row "Groups" column of "participants" table should contain "G1"
    And "Student 2" row "Groups" column of "participants" table should contain "G2"

  Scenario: Viewing participants page in a course in separate groups mode
    When I log in as "student1"
    And I am on "C3 sepgroups" course homepage
    And I navigate to course participants
    Then "Student 1" row "Groups" column of "participants" table should contain "G1"
    And I should not see "Student 2"
    And I click on "Clear filters" "button"
    And "Student 1" row "Groups" column of "participants" table should contain "G1"
    And I should not see "Student 2"
