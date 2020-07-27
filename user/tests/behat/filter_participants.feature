@core @core_user
Feature: Course participants can be filtered
  In order to filter the list of course participants
  As a user
  I need to visit the course participants page and apply the appropriate filters

  Background:
    Given the following "courses" exist:
      | fullname | shortname | groupmode | startdate        |
      | Course 1 | C1        |     1     | ##5 months ago## |
      | Course 2 | C2        |     0     | ##4 months ago## |
      | Course 3 | C3        |     0     | ##3 months ago## |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber | country | city   | maildisplay |
      | student1 | Student   | 1        | student1@example.com | SID1     |         | SCITY1 | 0           |
      | student2 | Student   | 2        | student2@example.com | SID2     | GB      | SCITY2 | 1           |
      | student3 | Student   | 3        | student3@example.com | SID3     | AU      | SCITY3 | 0           |
      | student4 | Student   | 4        | student4@moodle.com  | SID4     | AT      | SCITY4 | 0           |
      | student5 | Trendy    | Learnson | trendy@learnson.com  | SID5     | AU      | SCITY5 | 0           |
      | teacher1 | Teacher   | 1        | teacher1@example.org | TID1     | US      | TCITY1 | 0           |
    And the following "course enrolments" exist:
      | user     | course | role           | status | timeend       |
      | student1 | C1     | student        |    0   |               |
      | student2 | C1     | student        |    1   |               |
      | student3 | C1     | student        |    0   |               |
      | student4 | C1     | student        |    0   | ##yesterday## |
      | student1 | C2     | student        |    0   |               |
      | student2 | C2     | student        |    0   |               |
      | student3 | C2     | student        |    0   |               |
      | student5 | C2     | student        |    0   |               |
      | student1 | C3     | student        |    0   |               |
      | student2 | C3     | student        |    0   |               |
      | student3 | C3     | student        |    0   |               |
      | teacher1 | C1     | editingteacher |    0   |               |
      | teacher1 | C2     | editingteacher |    0   |               |
      | teacher1 | C3     | editingteacher |    0   |               |
    And the following "last access times" exist:
      | user     | course | lastaccess      |
      | student1 | C1     | ##yesterday##   |
      | student1 | C2     | ##2 weeks ago## |
      | student2 | C1     | ##4 days ago##  |
      | student3 | C1     | ##2 weeks ago## |
      | student4 | C1     | ##3 weeks ago## |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group A | C3     | GA       |
      | Group B | C3     | GB       |
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
      | student2 | G2    |
      | student3 | G2    |
      | student1 | GA    |
      | student2 | GA    |
      | student2 | GB    |

  @javascript
  Scenario: No filters applied
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario Outline: Filter users for a course with a single value
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "<matchtype>"
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "<filtervalue>" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should see "<expected3>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | matchtype | filtertype             | filtervalue | expected1 | expected2 | expected3 | notexpected1 | notexpected2 |
      | Any       | Groups                 | No group    | Student 1 | Student 4 | Teacher 1 | Student 2    | Student 3    |
      | All       | Groups                 | No group    | Student 1 | Student 4 | Teacher 1 | Student 2    | Student 3    |
      | None      | Groups                 | No group    | Student 2 | Student 3 |           | Student 1    | Teacher 1    |
      | Any       | Role                   | Student     | Student 1 | Student 2 | Student 3 | Teacher 1    | XX-IGNORE-XX |
      | All       | Role                   | Student     | Student 1 | Student 2 | Student 3 | Teacher 1    | XX-IGNORE-XX |
      | None      | Role                   | Student     | Teacher 1 |           |           | Student 1    | Student 2    |
      | Any       | Status                 | Active      | Student 1 | Student 3 | Teacher 1 | Student 2    | Student 4    |
      | All       | Status                 | Active      | Student 1 | Student 3 | Teacher 1 | Student 2    | Student 4    |
      | None      | Status                 | Active      | Student 2 | Student 4 |           | Student 1    | Student 3    |
      | Any       | Inactive for more than | 1 week      | Student 3 | Student 4 |           | Student 1    | Student 2    |
      | All       | Inactive for more than | 1 week      | Student 3 | Student 4 |           | Student 1    | Student 2    |
      | None      | Inactive for more than | 1 week      | Student 1 | Student 2 | Teacher 1 | Student 3    | XX-IGNORE-XX |

  @javascript
  Scenario Outline: Filter users for a course with multiple values for a single filter
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "<matchtype>"
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "<filtervalue1>" "list_item"
    And I click on "<filtervalue2>" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should see "<expected3>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | matchtype | filtertype | filtervalue1 | filtervalue2 | expected1 | expected2 | expected3 | notexpected1 | notexpected2 |
      | Any       | Groups     | Group 1      | Group 2      | Student 2 | Student 3 |           | Student 1    | XX-IGNORE-XX |
      | All       | Groups     | Group 1      | Group 2      | Student 2 |           |           | Student 1    | Student 3    |
      | None      | Groups     | Group 1      | Group 2      | Student 1 | Teacher 1 |           | Student 2    | Student 3    |

  @javascript
  Scenario Outline: Filter users which are group members in several courses
    Given I log in as "teacher1"
    And I am on "Course 3" course homepage
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "<filtertype>"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "<filtervalue>" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "<expected1>" in the "participants" "table"
    And I should see "<expected2>" in the "participants" "table"
    And I should not see "<notexpected1>" in the "participants" "table"
    And I should not see "<notexpected2>" in the "participants" "table"
    # Note the 'XX-IGNORE-XX' elements are for when there is less than 2 'not expected' items.

    Examples:
      | filtertype | filtervalue | expected1 | expected2 | notexpected1 | notexpected2 |
      | Groups     | No group    | Student 3 |           | Student 1    | Student 2    |
      | Groups     | Group A     | Student 1 | Student 2 | Student 3    | XX-IGNORE-XX |
      | Groups     | Group B     | Student 2 |           | Student 1    | Student 3    |

  @javascript
  Scenario: In separate groups mode, a student in a single group can only view and filter by users in their own group
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    # Unsuspend student 2 for to improve coverage of this test.
    And I click on "Edit enrolment" "icon" in the "Student 2" "table_row"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    And I log out
    When I log in as "student3"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    # Default view should have groups filter pre-set.
    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should see "Group 2" in the "Filter 1" "fieldset"
    And I should not see "Group 1" in the "Filter 1" "fieldset"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Testing result of removing groups filter row.
    And I click on "Remove filter row" "button" in the "Filter 1" "fieldset"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Testing result of applying groups filter manually.
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I should see "Group 2" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Group 1" in the ".form-autocomplete-suggestions" "css_element"
    And I click on "Group 2" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Testing result of removing groups filter by clearing all filters.
    And I click on "Clear filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: In separate groups mode, a student in multiple groups can only view and filter by users in their own groups
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    # Unsuspend student 2 for to improve coverage of this test.
    And I click on "Edit enrolment" "icon" in the "Student 2" "table_row"
    And I set the field "Status" to "Active"
    And I click on "Save changes" "button"
    And I log out
    When I log in as "student2"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    # Default view should have groups filter pre-set.
    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should see "Group 1" in the "Filter 1" "fieldset"
    And I should see "Group 2" in the "Filter 1" "fieldset"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Testing result of removing groups filter row.
    And I click on "Remove filter row" "button" in the "Filter 1" "fieldset"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Testing result of applying groups filter manually.
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I should see "Group 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Group 2" in the ".form-autocomplete-suggestions" "css_element"
    And I click on "Group 1" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    # Testing result of removing groups filter by clearing all filters.
    And I click on "Clear filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"

  @javascript
  Scenario: Filter users who have no role in a course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1's role assignments" "link"
    And I click on ".form-autocomplete-selection [aria-selected=true]" "css_element"
    And I press key "27" in the field "Student 1's role assignments"
    And I click on "Save changes" "link"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "No roles" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Multiple filters applied (All filterset match type)
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Add condition" "button"
    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Active" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Add more filters.
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Enrolment methods"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 3" "fieldset"
    And I click on "Manual enrolments" "list_item"
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 4" "fieldset" to "All"
    And I set the field "type" in the "Filter 4" "fieldset" to "Groups"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 4" "fieldset"
    And I click on "Group 2" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 3" in the "participants" "table"
    But I should not see "Teacher 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    # Change the active status filter to inactive.
    And I click on "Active" "autocomplete_selection"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Inactive" "list_item"
    And I click on "Apply filters" "button"
    Then I should see "Student 2" in the "participants" "table"
    But I should not see "Student 4" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Set both statuses (match any).
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Active" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    # Switch to match all.
    And I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"

  @javascript
  Scenario: Multiple filters applied (Any filterset match type)
    Given I log in as "teacher1"
    #Avoid 'Teacher' list item collisions with profile dropdown.
    And I open my profile in edit mode
    And I set the field "First name" to "Patricia"
    And I press "Update profile"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Teacher" "list_item"
    And I click on "Add condition" "button"
    # Set filterset to match any.
    And I set the field "Match" to "Any"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Active" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Patricia 1" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I set the field "Match" in the "Filter 2" "fieldset" to "None"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Patricia 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    # Add a keyword filter.
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Keyword"
    And I set the field "Type..." to "teacher1"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Patricia 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

  @javascript
  Scenario: Multiple filters applied (None filterset match type)
    Given I log in as "teacher1"
    #Avoid 'Teacher' list item collisions with profile dropdown.
    And I open my profile in edit mode
    And I set the field "First name" to "Patricia"
    And I press "Update profile"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Teacher" "list_item"
    And I click on "Add condition" "button"
    # Set filterset to match none.
    And I set the field "Match" to "None"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Active" "list_item"
    When I click on "Apply filters" "button"
    Then I should see "Student 2" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Patricia 1" in the "participants" "table"
    And I set the field "Match" in the "Filter 2" "fieldset" to "None"
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia 1" in the "participants" "table"
    # Add a keyword filter.
    And I click on "Add condition" "button"
    And I set the field "Match" in the "Filter 3" "fieldset" to "Any"
    And I set the field "type" in the "Filter 3" "fieldset" to "Keyword"
    And I set the field "Type..." to "3@"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia 1" in the "participants" "table"
    And I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I click on "Apply filters" "button"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Patricia 1" in the "participants" "table"

  @javascript
  Scenario: Filter match by one or more keywords and modified match types
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." to "1@example"
    And I press key "13" in the field "Type..."
    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I set the field "Match" in the "Filter 1" "fieldset" to "None"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I set the field "Match" in the "Filter 1" "fieldset" to "None"
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Add a second keyword filter value
    And I set the field "Type..." to "moodle"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"

  @javascript
  Scenario: Reorder users without losing filter
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    When I click on "Surname" "link"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Only possible to add filter rows for the number of filters available
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 2" "fieldset" to "Status"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 3" "fieldset" to "Roles"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 4" "fieldset" to "Enrolment methods"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 5" "fieldset" to "Groups"
    And I click on "Add condition" "button"
    And I set the field "type" in the "Filter 6" "fieldset" to "Inactive for more than"
    And the "Add condition" "button" should be disabled

  @javascript
  Scenario: Rendering filter options for teachers in a course that don't support groups
    Given I log in as "teacher1"
    And I am on "Course 2" course homepage
    When I navigate to course participants
    Then I should see "Roles" in the "type" "field"
    And I should see "Enrolment methods" in the "type" "field"
    But I should not see "Groups" in the "type" "field"

  @javascript
  Scenario: Rendering filter options for students who have limited privileges
    Given I log in as "student1"
    And I am on "Course 2" course homepage
    When I navigate to course participants
    Then I should see "Roles" in the "type" "field"
    But I should not see "Status" in the "type" "field"
    And I should not see "Enrolment methods" in the "type" "field"

  @javascript
  Scenario: Filter by user identity fields
    Given I log in as "teacher1"
    And the following config values are set as admin:
        | showuseridentity | idnumber,email,city,country |
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    # Search by email (only).
    And I set the field "Type..." to "student1@example.com"
    And I press key "13" in the field "Type..."
    When I click on "Apply filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Search by idnumber (only).
    And I click on "student1@example.com" "autocomplete_selection"
    And I set the field "Type..." to "SID"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Search by city (only).
    And I click on "SID" "autocomplete_selection"
    And I set the field "Type..." to "SCITY"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    # Search by country text (only) - should not match.
    And I click on "SCITY" "autocomplete_selection"
    And I set the field "Type..." to "GB"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"
    # Check no match.
    And I click on "GB" "autocomplete_selection"
    And I set the field "Type..." to "NOTHING"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"

  @javascript
  Scenario: Filter by user identity fields when cannot see the field data
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | moodle/site:viewuseridentity | Prevent |
    And the following config values are set as admin:
      | showuseridentity | idnumber,email,city,country |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    # Search by email (only) - should only see visible email + own.
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." to "@example."
    And I press key "13" in the field "Type..."
    When I click on "Apply filters" "button"
    Then I should not see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    # Search for other fields - should only see own results.
    And I click on "@example." "autocomplete_selection"
    And I set the field "Type..." to "SID"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"
    And I click on "SID" "autocomplete_selection"
    And I set the field "Type..." to "TID"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I click on "TID" "autocomplete_selection"
    And I set the field "Type..." to "CITY"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    # Check no match.
    And I click on "CITY" "autocomplete_selection"
    And I set the field "Type..." to "NOTHING"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Nothing to display"

  @javascript
  Scenario: Individual filters can be removed, which will automatically refresh the participants list
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Add condition" "button"
    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Keyword"
    And I set the field "Type..." to "@example"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    When I click on "Remove filter row" "button" in the "Filter 1" "fieldset"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"

  @javascript
  Scenario: All filters can be cleared at once
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "All"
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Add condition" "button"
    # Set filterset to match all.
    And I set the field "Match" to "All"
    And I set the field "Match" in the "Filter 2" "fieldset" to "Any"
    And I set the field "type" in the "Filter 2" "fieldset" to "Keyword"
    And I set the field "Type..." to "@example"
    And I press key "13" in the field "Type..."
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    When I click on "Clear filters" "button"
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Filterset match type is reset when reducing to a single filter
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Keyword"
    And I set the field "Type..." to "@example.com"
    And I press key "13" in the field "Type..."
    And I click on "Add condition" "button"
    # Set filterset to match none.
    And I set the field "Match" to "None"
    And I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I set the field "type" in the "Filter 2" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Student" "list_item"
    # Match none of student role and @example.com keyword.
    And I click on "Apply filters" "button"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    When I click on "Remove filter row" "button" in the "Filter 2" "fieldset"
    # Filterset match type and role filter are removed, leaving keyword filter only.
    Then I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should not see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
    And I click on "Add condition" "button"
    # Re-add a second filter and ensure the default (any) filterset match type is set.
    And I set the field "Match" in the "Filter 2" "fieldset" to "All"
    And I set the field "type" in the "Filter 2" "fieldset" to "Role"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 2" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Apply filters" "button"
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Student 4" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Filter users by first initial
    Given I log in as "teacher1"
    And I am on "Course 2" course homepage
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    When I click on "T" "link" in the ".firstinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"

  @javascript
  Scenario: Filter users by last initial
    Given I log in as "teacher1"
    And I am on "Course 2" course homepage
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    When I click on "L" "link" in the ".lastinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Filter users by first and last initials
    Given I log in as "teacher1"
    And I am on "Course 2" course homepage
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    When I click on "T" "link" in the ".firstinitial" "css_element"
    And I click on "L" "link" in the ".lastinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"

  @javascript
  Scenario: Initials filtering is always applied in addition to any other filtering
    Given I log in as "teacher1"
    And I am on "Course 2" course homepage
    And I navigate to course participants
    And I should see "Student 1" in the "participants" "table"
    And I should see "Student 2" in the "participants" "table"
    And I should see "Student 3" in the "participants" "table"
    And I should see "Trendy Learnson" in the "participants" "table"
    And I should see "Teacher 1" in the "participants" "table"
    When I set the field "Match" in the "Filter 1" "fieldset" to "Any"
    And I set the field "type" in the "Filter 1" "fieldset" to "Role"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I click on "Student" "list_item"
    And I click on "Apply filters" "button"
    When I click on "T" "link" in the ".firstinitial" "css_element"
    Then I should see "Trendy Learnson" in the "participants" "table"
    And I should not see "Student 1" in the "participants" "table"
    And I should not see "Student 2" in the "participants" "table"
    And I should not see "Student 3" in the "participants" "table"
    And I should not see "Teacher 1" in the "participants" "table"
