@gradereport @gradereport_singleview @javascript
Feature: Singleview report pagination
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
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro             | grade |
      | assign   | C1     | a1       | Test assignment one | Submit something! | 300   |

  Scenario: Default is used when teachers have no preference yet on singleview report
    Given "41" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "41" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    Then the field "perpage" matches value "20"
    # There is also 1 header row.
    And I should see "21" node occurrences of type "tr" in the "singleview-grades" "table"
    And I should see "3" in the ".stickyfooter .pagination" "css_element"
    And I should not see "4" in the ".stickyfooter .pagination" "css_element"

  Scenario: Teachers can have their preference for the number of students on singleview report
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro             | grade |
      | assign   | C2     | a2       | Test assignment two | Submit something! | 300   |
    When I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    And I set the field "perpage" to "100"
    And I am on the "Course 2" "grades > Grader report > View" page
    And I click on grade item menu "Test assignment two" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    Then the field "perpage" matches value "100"

  Scenario: Teachers can change the number of students shown on singleview report
    Given "101" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "101" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    And I set the field "perpage" to "100"
    # There is also 1 header row.
    Then I should see "101" node occurrences of type "tr" in the "singleview-grades" "table"
    And I should see "2" in the ".stickyfooter .pagination" "css_element"
    And I should not see "3" in the ".stickyfooter .pagination" "css_element"

  @javascript
  Scenario: The pagination bar is only displayed when there is more than one page on singleview report
    Given "21" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "21" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   |student         |
    When I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I click on grade item menu "Test assignment one" of type "gradeitem" on "grader" page
    And I choose "Single view for this item" in the open action menu
    # By default, we have 20 students per page.
    And ".stickyfooter .pagination" "css_element" should exist
    And I set the field "perpage" to "100"
    Then ".stickyfooter .pagination" "css_element" should not exist
