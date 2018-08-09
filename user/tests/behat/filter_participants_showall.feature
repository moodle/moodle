@core @core_user
Feature: Course participants can be filtered to display all the users
  In order to filter the list of course participants
  As a user
  I need to visit the course participants page, apply the appropriate filters and show all users per page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        |     1     |
      | Course 2 | C2        |     0     |
    And the following "users" exist:
      | username  | firstname | lastname | email                 |
      | student1  | Student   | 1        | student1@example.com  |
      | student2  | Student   | 2        | student2@example.com  |
      | student3  | Student   | 3        | student3@example.com  |
      | student4  | Student   | 4        | student4@example.com  |
      | student5  | Student   | 5        | student5@example.com  |
      | student6  | Student   | 6        | student6@example.com  |
      | student7  | Student   | 7        | student7@example.com  |
      | student8  | Student   | 8        | student8@example.com  |
      | student9  | Student   | 9        | student9@example.com  |
      | student10 | Student   | 10       | student10@example.com |
      | student11 | Student   | 11       | student11@example.com |
      | student12 | Student   | 12       | student12@example.com |
      | student13 | Student   | 13       | student13@example.com |
      | student14 | Student   | 14       | student14@example.com |
      | student15 | Student   | 15       | student15@example.com |
      | student16 | Student   | 16       | student16@example.com |
      | student17 | Student   | 17       | student17@example.com |
      | student18 | Student   | 18       | student18@example.com |
      | student19 | Student   | 19       | student19@example.com |
      | student20 | Student   | 20       | student20@example.com |
      | student21 | Student   | 21       | student21@example.com |
      | student22 | Student   | 22       | student22@example.com |
      | student23 | Student   | 23       | student23@example.com |
      | student24 | Student   | 24       | student24@example.com |
      | teacher1  | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user      | course | role           | status | timeend       |
      | student1  | C1     | student        |    0   |               |
      | student2  | C1     | student        |    0   |               |
      | student3  | C1     | student        |    0   |               |
      | student4  | C1     | student        |    0   |               |
      | student5  | C1     | student        |    0   |               |
      | student6  | C1     | student        |    0   |               |
      | student7  | C1     | student        |    0   |               |
      | student8  | C1     | student        |    0   |               |
      | student9  | C1     | student        |    0   |               |
      | student10 | C1     | student        |    0   |               |
      | student11 | C1     | student        |    0   |               |
      | student12 | C1     | student        |    0   |               |
      | student13 | C1     | student        |    0   |               |
      | student14 | C1     | student        |    0   |               |
      | student15 | C1     | student        |    0   |               |
      | student16 | C1     | student        |    0   |               |
      | student17 | C1     | student        |    0   |               |
      | student18 | C1     | student        |    0   |               |
      | student19 | C1     | student        |    0   |               |
      | student20 | C1     | student        |    0   |               |
      | student21 | C1     | student        |    0   |               |
      | student22 | C1     | student        |    0   |               |
      | student23 | C1     | student        |    0   |               |
      | student24 | C1     | student        |    1   |               |
      | student1  | C2     | student        |    0   |               |
      | student2  | C2     | student        |    0   |               |
      | student3  | C2     | student        |    0   |               |
      | teacher1  | C1     | editingteacher |    0   |               |
      | teacher1  | C2     | editingteacher |    0   |               |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
      | student2 | G2    |
      | student3 | G2    |

  @javascript
  Scenario: Show all filtered users for a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I open the autocomplete suggestions list
    And I click on "Role: Student" item in the autocomplete list
    And I click on "Show all 24" "link"
    Then I should see "Role: Student"
    And I should see "Number of participants: 24" in the "//div[@class='userlist']" "xpath_element"
    And I should see "Show 20 per page"

  @javascript
  Scenario: Apply more than one filter and show all users
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I open the autocomplete suggestions list
    And I click on "Role: Student" item in the autocomplete list
    And I open the autocomplete suggestions list
    And I click on "Status: Active" item in the autocomplete list
    And I click on "Show all 23" "link"
    Then I should see "Role: Student"
    And I should see "Status: Active"
    And I should see "Number of participants: 23" in the "//div[@class='userlist']" "xpath_element"
    And I should see "Student 1"
    And I should not see "Student 24"
    And I should see "Show 20 per page"
