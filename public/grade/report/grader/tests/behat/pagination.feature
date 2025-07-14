@gradereport @gradereport_grader
Feature: grader report pagination
  In order to consume the content of the report better
  As a teacher
  I need the report to be paginated

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Default is used when teachers have no preference yet
    Given "41" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "41" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "View > Grader report" in the course gradebook
    Then the field "perpage" matches value "20"
    # Add 3 to the expected number because there are 2 header and 1 footer rows.
    And I should see "23" node occurrences of type "tr" in the "user-grades" "table"
    And I should see "3" in the ".stickyfooter .pagination" "css_element"
    And I should not see "4" in the ".stickyfooter .pagination" "css_element"

  @javascript
  Scenario: Teachers can have their preference for the number of students
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
    When I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "View > Grader report" in the course gradebook
    And I set the field "perpage" to "100"
    And I am on the "Course 2" "Course" page
    And I navigate to "View > Grader report" in the course gradebook
    Then the field "perpage" matches value "100"

  @javascript
  Scenario: Teachers can change the number of students shown on the report
    Given "101" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "101" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "View > Grader report" in the course gradebook
    And I set the field "perpage" to "100"
    # Add 3 to the expected number because there are 2 header and 1 footer rows.
    Then I should see "103" node occurrences of type "tr" in the "user-grades" "table"
    And I should see "2" in the ".stickyfooter .pagination" "css_element"
    And I should not see "3" in the ".stickyfooter .pagination" "css_element"
    And the url should match "/grade/report/grader/index\.php\?id=[0-9]+&report=grader&perpage=100$"

  @javascript
  Scenario: The pagination bar is only displayed when there is more than one page
    Given "21" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "21" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "View > Grader report" in the course gradebook
    # By default, we have 20 students per page.
    Then ".stickyfooter .pagination" "css_element" should exist
    And I set the field "perpage" to "100"
    Then ".stickyfooter .pagination" "css_element" should not exist
