@enrol @enrol_cohort
Feature: Cohort enrolment management

  Background:
    Given the following "users" exist:
      | username    | firstname | lastname | email                   |
      | teacher001  | Teacher   | 001      | teacher001@example.com  |
    And the following "cohorts" exist:
      | name         | idnumber | visible |
      | Alpha1       | A1       | 1       |
      | Beta2        | B1       | 1       |
    And the following "courses" exist:
      | fullname   | shortname | format | startdate       |
      | Course 001 | C001      | weeks  | ##1 month ago## |
    And the following "course enrolments" exist:
      | user       | course | role           | timestart       |
      | teacher001 | C001   | editingteacher | ##1 month ago## |

  @javascript
  Scenario: Add multiple cohorts to the course
    When I log in as "teacher001"
    And I am on the "Course 001" "enrolment methods" page
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    And I click on "Alpha1" item in the autocomplete list
    And "Alpha1" "autocomplete_selection" should exist
    And I click on "Beta2" item in the autocomplete list
    And "Alpha1" "autocomplete_selection" should exist
    And "Beta2" "autocomplete_selection" should exist
    And I press "Add method"
    Then I should see "Cohort sync (Beta2 - Student)"
    And I should see "Cohort sync (Alpha1 - Student)"

  @javascript
  Scenario: Edit cohort enrolment
    When I log in as "teacher001"
    And I add "Cohort sync" enrolment method in "Course 001" with:
      | Cohort | Alpha1 |
    And I should see "Cohort sync (Alpha1 - Student)"
    And I click on "Edit" "link" in the "Alpha1" "table_row"
    And I set the field "Assign role" to "Non-editing teacher"
    And I click on "Save" "button"
    And I should see "Cohort sync (Alpha1 - Non-editing teacher)"

  @javascript
  Scenario: Course cohort enrolment sync cohorts members
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Sandra    | Cole     | s1@example.com |
      | s2       | John      | Smith    | s2@example.com |
      | s4       | Jane      | Doe      | s4@example.com |
    And the following "cohort members" exist:
      | user | cohort |
      | s1   | A1     |
      | s2   | A1     |
    When I log in as "teacher001"
    And I add "Cohort sync" enrolment method in "Course 001" with:
      | Cohort      | A1 |
      | customint2  | -1 |
    Then I should see "Cohort sync (Alpha1 - Student)"
    And I select "Groups" from the "jump" singleselect
    # Confirm that group was created and corresponding group members are present
    And I set the field "groups[]" to "Alpha1 cohort (2)"
    And the "members" select box should contain "Sandra Cole (s1@example.com)"
    And the "members" select box should contain "John Smith (s2@example.com)"
    And I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I press "Assign" action in the "Alpha1" report row
    And I should see "Cohort 'Alpha1' members"
    And I should see "Removing users from a cohort may result in unenrolling of users from multiple courses which includes deleting of user settings, grades, group membership and other user information from affected courses."
    # Remove user s4 from cohort
    And I set the field "removeselect[]" to "John Smith (s2@example.com)"
    And I click on "Remove" "button"
    # Add user s4 to the cohort.
    And I set the field "addselect_searchtext" to "s4"
    And I set the field "addselect[]" to "Jane Doe (s4@example.com)"
    And I click on "Add" "button"
    And the "removeselect[]" select box should contain "Sandra Cole (s1@example.com)"
    And the "removeselect[]" select box should contain "Jane Doe (s4@example.com)"
    And the "removeselect[]" select box should not contain "John Smith (s2@example.com)"
    And I trigger cron
    And I am on "Course 001" course homepage
    And I navigate to course participants
    # Verifies students 1 and 4 are in the cohort and student 2 is not any more.
    And the following should exist in the "participants" table:
      | First name / Last name | Email address  | Roles   | Groups        |
      | Sandra Cole            | s1@example.com | Student | Alpha1 cohort |
      | Jane Doe               | s4@example.com | Student | Alpha1 cohort |
    And the following should not exist in the "participants" table:
      | First name / Last name | Email address  | Roles   | Groups        |
      | John Smith             | s2@example.com | Student | Alpha1 cohort |

  @javascript
  Scenario: Course cohort enrolment creates a new group
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s3       | Bianca    | McAfee   | s3@example.com |
      | s5       | Abigail   | Wyatt    | s5@example.com |
    And the following "cohort members" exist:
      | user | cohort |
      | s3   | B1     |
      | s5   | B1     |
    When I log in as "teacher001"
    And I add "Cohort sync" enrolment method in "Course 001" with:
      | Cohort      | B1 |
    And I click on "Edit" "link" in the "Beta2" "table_row"
    And I set the field "Add to group" to "Create new group"
    And I click on "Save changes" "button"
    And I select "Groups" from the "jump" singleselect
    And I set the field "groups[]" to "Beta2 cohort (2)"
    Then the "members" select box should contain "Bianca McAfee (s3@example.com)"
    And the "members" select box should contain "Abigail Wyatt (s5@example.com)"
