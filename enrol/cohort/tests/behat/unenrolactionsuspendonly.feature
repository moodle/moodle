@enrol @enrol_cohort
Feature: Unenrol action to disable course enrolment

  Background:
    Given the following "users" exist:
      | username    | firstname | lastname | email                   |
      | teacher001  | Teacher   | 001      | teacher001@example.com  |
      | student001  | Student   | 001      | student001@example.com  |
      | student002  | Student   | 002      | student002@example.com  |
      | student003  | Student   | 003      | student003@example.com  |
      | student004  | Student   | 004      | student004@example.com  |
    And the following "cohorts" exist:
      | name                 | idnumber | visible |
      | System cohort        | CVO      | 1       |
    And the following "cohort members" exist:
      | user       | cohort     |
      | student001 | CVO        |
      | student002 | CVO        |
      | student003 | CVO        |
      | student004 | CVO        |
    And the following "courses" exist:
      | fullname   | shortname | format | startdate       |
      | Course 001 | C001      | weeks  | ##1 month ago## |
    And the following "course enrolments" exist:
      | user       | course | role           | timestart       |
      | teacher001 | C001   | editingteacher | ##1 month ago## |

  @javascript @skip_chrome_zerosize
  Scenario: Removing the user from the cohort will suspend the enrolment but keep the role
    When I log in as "teacher001"
    And I am on the "Course 001" "enrolment methods" page
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    Then "System cohort" "autocomplete_suggestions" should exist
    And I set the field "Cohort" to "System cohort"
    And I press "Add method"
    And I am on the "Course 001" "enrolled users" page
    And I should see "student001@example.com"
    And I should see "student002@example.com"
    And I should see "student003@example.com"
    And I should see "student004@example.com"
    And I log out
    When I log in as "admin"
    Then I navigate to "Plugins > Enrolments > Cohort sync" in site administration
    And I select "Disable course enrolment" from the "External unenrol action" singleselect
    And I press "Save changes"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    When I press "Assign" action in the "System cohort" report row
    And I set the field "removeselect_searchtext" to "Student 001"
    And I set the field "Current users" to "Student 001 (student001@example.com)"
    And I wait "1" seconds
    And I press "Remove"
    And I am on "Course 001" course homepage
    And I navigate to course participants
    And I should see "Suspended" in the "Student 001" "table_row"
    And I should see "Active" in the "Student 002" "table_row"
    And I should see "Active" in the "Student 003" "table_row"
    And I should see "Active" in the "Student 004" "table_row"

  @javascript @skip_chrome_zerosize
  Scenario: Deleting non-empty cohort will suspend the enrolment but keep the role
    When I log in as "teacher001"
    And I am on the "Course 001" "enrolment methods" page
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    Then "System cohort" "autocomplete_suggestions" should exist
    And I set the field "Cohort" to "System cohort"
    And I press "Add method"
    And I am on the "Course 001" "enrolled users" page
    And I should see "student001@example.com"
    And I should see "student002@example.com"
    And I should see "student003@example.com"
    And I should see "student004@example.com"
    And I log out
    When I log in as "admin"
    Then I navigate to "Plugins > Enrolments > Cohort sync" in site administration
    And I select "Disable course enrolment" from the "External unenrol action" singleselect
    And I press "Save changes"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    When I press "Delete" action in the "System cohort" report row
    And I click on "Delete" "button" in the "Delete selected" "dialogue"
    And I am on "Course 001" course homepage
    And I navigate to course participants
    And I should see "Suspended" in the "Student 001" "table_row"
    And I should see "Suspended" in the "Student 002" "table_row"
    And I should see "Suspended" in the "Student 003" "table_row"
    And I should see "Suspended" in the "Student 004" "table_row"
