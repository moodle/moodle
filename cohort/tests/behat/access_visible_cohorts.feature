@core @core_cohort @enrol_cohort
Feature: Access visible and hidden cohorts
  In order to enrol users from cohorts
  As an manager or teacher
  I need to be able to view the list of cohorts defined above the course

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
    And the following "cohorts" exist:
      | name                 | idnumber | visible |
      | System cohort        | CV0      | 1       |
      | System hidden cohort | CH0      | 0       |
      | System empty cohort  | CVE0     | 1       |
    And the following "cohorts" exist:
      | name                        | idnumber | contextlevel | reference | visible |
      | Cohort in category 1        | CV1      | Category     | CAT1      | 1       |
      | Cohort in category 2        | CV2      | Category     | CAT2      | 1       |
      | Cohort hidden in category 1 | CH1      | Category     | CAT1      | 0       |
      | Cohort empty in category 1  | CVE1     | Category     | CAT1      | 1       |
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | First     | User     | first@example.com   |
      | user2    | Second    | User     | second@example.com  |
      | student  | Sam       | User     | student@example.com |
      | teacher  | Terry     | User     | teacher@example.com |
    And the following "cohort members" exist:
      | user    | cohort |
      | student | CV0   |
      | student | CV1   |
      | student | CV2   |
      | student | CH0   |
      | student | CH1   |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | System       |           |
      | user2 | manager | Category     | CAT1      |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript
  Scenario: Teacher can see visible cohorts defined in the above contexts
    When I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    Then "Cohort in category 1" "autocomplete_suggestions" should exist
    And "System cohort" "autocomplete_suggestions" should exist
    And "Cohort hidden in category 1" "autocomplete_suggestions" should not exist
    And "System hidden cohort" "autocomplete_suggestions" should not exist
    And "Cohort in category 2" "autocomplete_suggestions" should not exist
    And "Cohort empty in category 1" "autocomplete_suggestions" should exist
    And "System empty cohort" "autocomplete_suggestions" should exist
    And I set the field "Cohort" to "System cohort"
    And I press "Add method"
    And I am on "Course 1" course homepage
    And I navigate to "Enrolled users" node in "Course administration > Users"
    And I should see "student@example.com"
    And I am on "Course 1" course homepage
    And I navigate to "Groups" node in "Course administration > Users"
    And I press "Auto-create groups"
    And the "Select members from cohort" select box should contain "Cohort in category 1"
    And the "Select members from cohort" select box should contain "System cohort"
    And the "Select members from cohort" select box should not contain "Cohort hidden in category 1"
    And the "Select members from cohort" select box should not contain "System hidden cohort"
    And the "Select members from cohort" select box should not contain "Cohort in category 2"
    And the "Select members from cohort" select box should not contain "Cohort empty in category 1"
    And the "Select members from cohort" select box should not contain "System empty cohort"

  @javascript
  Scenario: System manager can see all cohorts defined in the above contexts
    When I log in as "user1"
    And I am on "Course 1" course homepage
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    Then "Cohort in category 1" "autocomplete_suggestions" should exist
    And "System cohort" "autocomplete_suggestions" should exist
    And "Cohort hidden in category 1" "autocomplete_suggestions" should exist
    And "System hidden cohort" "autocomplete_suggestions" should exist
    And "Cohort in category 2" "autocomplete_suggestions" should not exist
    And "Cohort empty in category 1" "autocomplete_suggestions" should exist
    And "System empty cohort" "autocomplete_suggestions" should exist
    And I set the field "Cohort" to "System cohort"
    And I press "Add method"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    And I should see "student@example.com"
    And I navigate to "Groups" node in "Course administration > Users"
    And I press "Auto-create groups"
    And the "Select members from cohort" select box should contain "Cohort in category 1"
    And the "Select members from cohort" select box should contain "System cohort"
    And the "Select members from cohort" select box should contain "Cohort hidden in category 1"
    And the "Select members from cohort" select box should contain "System hidden cohort"
    And the "Select members from cohort" select box should not contain "Cohort in category 2"
    And the "Select members from cohort" select box should not contain "Cohort empty in category 1"
    And the "Select members from cohort" select box should not contain "System empty cohort"

  @javascript
  Scenario: Category manager can see all cohorts defined in his category and visible cohorts defined above
    When I log in as "user2"
    And I am on "Course 1" course homepage
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I select "Cohort sync" from the "Add method" singleselect
    And I open the autocomplete suggestions list
    Then "Cohort in category 1" "autocomplete_suggestions" should exist
    And "System cohort" "autocomplete_suggestions" should exist
    And "Cohort hidden in category 1" "autocomplete_suggestions" should exist
    And "System hidden cohort" "autocomplete_suggestions" should not exist
    And "Cohort in category 2" "autocomplete_suggestions" should not exist
    And "Cohort empty in category 1" "autocomplete_suggestions" should exist
    And "System empty cohort" "autocomplete_suggestions" should exist
    And I set the field "Cohort" to "System cohort"
    And I press "Add method"
    And I navigate to "Enrolled users" node in "Course administration > Users"
    And I should see "student@example.com"
    And I navigate to "Groups" node in "Course administration > Users"
    And I press "Auto-create groups"
    And the "Select members from cohort" select box should contain "Cohort in category 1"
    And the "Select members from cohort" select box should contain "System cohort"
    And the "Select members from cohort" select box should contain "Cohort hidden in category 1"
    And the "Select members from cohort" select box should not contain "System hidden cohort"
    And the "Select members from cohort" select box should not contain "Cohort in category 2"
    And the "Select members from cohort" select box should not contain "Cohort empty in category 1"
    And the "Select members from cohort" select box should not contain "System empty cohort"
