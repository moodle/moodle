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
  Scenario: Show all users in a course that match a single filter value
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "24 participants found"
    And I should see "Show all 24"
    And I should not see "Show 20 per page"
    And I should not see "of the following"
    And I click on "Show all 24" "link"
    And I should see "Show 20 per page"
    And I should not see "Show all 24"

  @javascript
  Scenario: Apply one value for more than one filter and show all matching users
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Add condition" "button"
    And I set the field "Match" to "All"
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Active" "list_item"
    When I click on "Apply filters" "button"
    And I click on "Show all 23" "link"
    Then I should see "23 participants found"
    And I should see "Show 20 per page"
    And I should see "of the following"
    And I should see "Student 1"
    And I should not see "Student 24"
    And I should not see "Show all 23"
