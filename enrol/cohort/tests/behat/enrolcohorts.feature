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
