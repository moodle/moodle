@gradereport @gradereport_singleview
Feature: We don't show hidden grades for users without the 'moodle/grade:viewhidden' capability on singleview report
  In order to show singleview report in secure way
  As a teacher without the 'moodle/grade:viewhidden' capability
  I should not see hidden grades in the singleview report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student1  | 1        | student1@example.com |
      | student2 | Student2  | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name                   | intro                   | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | C1     | 1       | Test assignment name 1 | Submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 2 | submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 3 | submit your online text | 1                                   | 0                |
    # Hidden manual grade item.
    And the following "grade items" exist:
      | itemname     | grademin | grademax | course | hidden |
      | Manual grade | 20       | 40       | C1     | 1      |
    And the following "grade grades" exist:
      | gradeitem              | user     | grade |
      | Test assignment name 1 | student1 | 80    |
      | Test assignment name 1 | student2 | 70    |
      | Test assignment name 2 | student1 | 90    |
      | Test assignment name 2 | student2 | 60    |
      | Test assignment name 3 | student1 | 10    |
      | Test assignment name 3 | student2 | 50    |
      | Manual grade           | student1 | 30    |
      | Manual grade           | student2 | 40    |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    # Hide assignment 2 activity.
    And I open "Test assignment name 2" actions menu
    And I choose "Hide" in the open action menu
    And I navigate to "View > Grader report" in the course gradebook
    # Hide grade.
    And I click on grade menu "Test assignment name 1" for user "student1"
    And I choose "Hide" in the open action menu
    # Hide assignment 3 grade item.
    And I set the following settings for grade item "Test assignment name 3" of type "gradeitem" on "grader" page:
      | Hidden          | 1 |

  @javascript
  Scenario: View singleview report containing hidden activities or grade items or grades with editing on and required capabilities
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Users" "link" in the ".page-toggler" "css_element"

    When I click on "Student1" in the "Search users" search combo box
    And the field "Grade for Test assignment name 1" matches value "80"
    And the field "Grade for Test assignment name 2" matches value "90"
    And the field "Grade for Test assignment name 3" matches value "10"
    And the field "Grade for Manual grade" matches value "30"
    And the field "Grade for Course total" matches value "210"

    And "Hidden" "icon" should exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And the field "Feedback for Test assignment name 1" matches value ""
    And the field "Feedback for Test assignment name 2" matches value ""
    And the field "Feedback for Test assignment name 3" matches value ""
    And the field "Feedback for Manual grade" matches value ""
    And the field "Feedback for Course total" matches value ""

    And "Override for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Override for Test assignment name 2" "checkbox" should exist in the "Test assignment name 2" "table_row"
    And "Override for Test assignment name 3" "checkbox" should exist in the "Test assignment name 3" "table_row"
    And "Override for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And "Override for Course total" "checkbox" should exist in the "Course total" "table_row"

    And "Exclude for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Exclude for Test assignment name 2" "checkbox" should exist in the "Test assignment name 2" "table_row"
    And "Exclude for Test assignment name 3" "checkbox" should exist in the "Test assignment name 3" "table_row"
    And "Exclude for Manual grade" "checkbox" should exist in the "Manual grade" "table_row"
    And "Exclude for Course total" "checkbox" should exist in the "Course total" "table_row"

    And I click on "Student2" in the "Search users" search combo box
    And the field "Grade for Test assignment name 1" matches value "70"
    And the field "Grade for Test assignment name 2" matches value "60"
    And the field "Grade for Test assignment name 3" matches value "50"
    And the field "Grade for Manual grade" matches value "40"
    And the field "Grade for Course total" matches value "220"

    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And the field "Feedback for Test assignment name 1" matches value ""
    And the field "Feedback for Test assignment name 2" matches value ""
    And the field "Feedback for Test assignment name 3" matches value ""
    And the field "Feedback for Manual grade" matches value ""
    And the field "Feedback for Course total" matches value ""

    And "Override for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Override for Test assignment name 2" "checkbox" should exist in the "Test assignment name 2" "table_row"
    And "Override for Test assignment name 3" "checkbox" should exist in the "Test assignment name 3" "table_row"
    And "Override for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And "Override for Course total" "checkbox" should exist in the "Course total" "table_row"

    And "Exclude for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Exclude for Test assignment name 2" "checkbox" should exist in the "Test assignment name 2" "table_row"
    And "Exclude for Test assignment name 3" "checkbox" should exist in the "Test assignment name 3" "table_row"
    And "Exclude for Manual grade" "checkbox" should exist in the "Manual grade" "table_row"
    And "Exclude for Course total" "checkbox" should exist in the "Course total" "table_row"

    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on "Test assignment name 1" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "80"
    And the field "Grade for Student2 2" matches value "70"
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 2" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "90"
    And the field "Grade for Student2 2" matches value "60"
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 3" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "10"
    And the field "Grade for Student2 2" matches value "50"
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Override for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    And I click on "Manual grade" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "30"
    And the field "Grade for Student2 2" matches value "40"
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student1 1" "checkbox" should not exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should not exist in the "Student2 2" "table_row"
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    And I click on "Course total" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "210"
    And the field "Grade for Student2 2" matches value "220"
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    Then "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

  @javascript
  Scenario: View singleview report containing hidden activities or grade items or grades with editing off and required capabilities
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode off
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on "Course total" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     | 210       |
      | Student2 2     | 220       |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    When I click on "Manual grade" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     | 30        |
      | Student2 2     | 40        |
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 3" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     | 10        |
      | Student2 2     | 50        |
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 2" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     | 90        |
      | Student2 2     | 60        |
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 1" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     | 80        |
      | Student2 2     | 70        |
    And "Hidden" "icon" should exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I click on "Student1" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
      | Grade item                 | Grade     |
      | Test assignment name 1     | 80        |
      | Test assignment name 2     | 90        |
      | Test assignment name 3     | 10        |
      | Manual grade               | 30        |
      | Course total               | 210       |
    And "Hidden" "icon" should exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I click on "Student2" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
      | Grade item                 | Grade     |
      | Test assignment name 1     | 70        |
      | Test assignment name 2     | 60        |
      | Test assignment name 3     | 50        |
      | Manual grade               | 40        |
      | Course total               | 220       |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    Then "Hidden" "icon" should not exist in the "Course total" "table_row"

  @javascript
  Scenario: View singleview report containing hidden activities or grade items or grades with editing off without required capabilities
    Given I log in as "teacher1"
    And the following "role capability" exists:
      | role                    | editingteacher  |
      | moodle/grade:viewhidden | prohibit        |
    And I am on "Course 1" course homepage with editing mode off
    And I navigate to "View > Single view" in the course gradebook
    When I click on "Users" "link" in the ".page-toggler" "css_element"
    And I click on "Student2" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
    # Total is weird!!!!!!!!!!!!!!.
      | Grade item                 | Grade     |
      | Test assignment name 1     | 70        |
      | Test assignment name 2     |           |
      | Test assignment name 3     |           |
      | Manual grade               |           |
      | Course total               | 220       |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I click on "Student1" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
      | Grade item                 | Grade     |
      | Test assignment name 1     |           |
      | Test assignment name 2     |           |
      | Test assignment name 3     |           |
      | Manual grade               |           |
      | Course total               | 210       |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on "Test assignment name 1" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     | 70        |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 2" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 3" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Manual grade" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Course total" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade        |
      | Student1 1     | 210          |
      | Student2 2     | 220          |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    Then "Hidden" "icon" should not exist in the "Student2 2" "table_row"

  @javascript
  Scenario: View singleview report containing hidden activities or grade items or grades with editing on without required capabilities
    Given I log in as "teacher1"
    And the following "role capability" exists:
      | role                    | editingteacher  |
      | moodle/grade:viewhidden | prohibit        |
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on "Course total" in the "Search items" search combo box
    And the field "Grade for Student1 1" matches value "210"
    And the field "Grade for Student2 2" matches value "220"
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"
    And the field "Feedback for Student1 1" matches value ""
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Exclude for Student1 1" "checkbox" should exist in the "Student1 1" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    When I click on "Manual grade" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 3" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 2" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
      | Student2 2     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"

    And I click on "Test assignment name 1" in the "Search items" search combo box
    And the following should exist in the "generaltable" table:
      | User full name | Grade     |
      | Student1 1     |           |
    And "Hidden" "icon" should not exist in the "Student1 1" "table_row"
    And the field "Grade for Student2 2" matches value "70"
    And "Hidden" "icon" should not exist in the "Student2 2" "table_row"
    And the field "Feedback for Student2 2" matches value ""
    And "Override for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"
    And "Exclude for Student2 2" "checkbox" should exist in the "Student2 2" "table_row"

    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I click on "Student1" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
      | Grade item                 | Grade     | Feedback |
      | Test assignment name 1     |           |          |
      | Test assignment name 2     |           |          |
      | Test assignment name 3     |           |          |
      | Manual grade               |           |          |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"
    And "Override for Test assignment name 1" "checkbox" should not exist in the "Test assignment name 1" "table_row"
    And "Exclude for Test assignment name 1" "checkbox" should not exist in the "Test assignment name 1" "table_row"
    And "Override for Test assignment name 2" "checkbox" should not exist in the "Test assignment name 2" "table_row"
    And "Exclude for Test assignment name 2" "checkbox" should not exist in the "Test assignment name 2" "table_row"
    And "Override for Test assignment name 3" "checkbox" should not exist in the "Test assignment name 3" "table_row"
    And "Exclude for Test assignment name 3" "checkbox" should not exist in the "Test assignment name 3" "table_row"
    And "Override for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And "Exclude for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And the field "Grade for Course total" matches value "210"
    And the field "Feedback for Course total" matches value ""
    And "Override for Course total" "checkbox" should exist in the "Course total" "table_row"
    And "Exclude for Course total" "checkbox" should exist in the "Course total" "table_row"

    And I click on "Student2" in the "Search users" search combo box
    And the following should exist in the "generaltable" table:
      | Grade item                 | Grade     | Feedback |
      | Test assignment name 2     |           |          |
      | Test assignment name 3     |           |          |
      | Manual grade               |           |          |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"
    And the field "Grade for Test assignment name 1" matches value "70"
    And the field "Feedback for Test assignment name 1" matches value ""
    And "Override for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Exclude for Test assignment name 1" "checkbox" should exist in the "Test assignment name 1" "table_row"
    And "Override for Test assignment name 2" "checkbox" should not exist in the "Test assignment name 2" "table_row"
    And "Exclude for Test assignment name 2" "checkbox" should not exist in the "Test assignment name 2" "table_row"
    And "Override for Test assignment name 3" "checkbox" should not exist in the "Test assignment name 3" "table_row"
    And "Exclude for Test assignment name 3" "checkbox" should not exist in the "Test assignment name 3" "table_row"
    And "Override for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And "Exclude for Manual grade" "checkbox" should not exist in the "Manual grade" "table_row"
    And the field "Grade for Course total" matches value "220"
    And the field "Feedback for Course total" matches value ""
    And "Override for Course total" "checkbox" should exist in the "Course total" "table_row"
    Then "Exclude for Course total" "checkbox" should exist in the "Course total" "table_row"
